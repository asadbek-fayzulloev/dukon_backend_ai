<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Generates Eloquent factory classes by statically parsing your migration
 * files (database/migrations/*.php), resolving each table's *final* column
 * set (i.e. applying Schema::table() add/drop/change() operations on top of
 * the original Schema::create()), and writing a best-guess database/factories
 * file for each table.
 *
 * Usage:
 *   php artisan make:factories                 Generate for every table
 *   php artisan make:factories products orders  Generate for specific tables
 *   php artisan make:factories --force          Overwrite existing factories
 *   php artisan make:factories --list           Just print resolved schemas, write nothing
 *   php artisan make:factories --include-system Also generate for framework/package tables
 *
 * This is a heuristic generator, not a full PHP parser — it covers the
 * single-statement-per-line `$table->method('col', ...)->modifier();` style
 * that `php artisan make:migration` produces. Review the generated files
 * before committing them.
 */
class GenerateFactories extends Command
{
    protected $signature = 'make:factories
        {tables?* : Specific table names to generate factories for (default: all)}
        {--force : Overwrite existing factory files}
        {--list : Print the resolved schema per table without writing files}
        {--include-system : Also generate factories for framework/package tables}
        {--path= : Migrations path (default: database/migrations)}
        {--out= : Output path for factories (default: database/factories)}
        {--model-namespace=App\\Models : Namespace models live in}';

    protected $description = 'Generate Eloquent factory classes from your migration files';

    /**
     * Tables that belong to Laravel core / common packages and almost never
     * need a factory. Skipped unless --include-system is passed.
     */
    protected array $systemTables = [
        'migrations', 'password_reset_tokens', 'sessions',
        'cache', 'cache_locks',
        'jobs', 'job_batches', 'failed_jobs',
        'personal_access_tokens',
        'telescope_entries', 'telescope_entries_tags', 'telescope_monitoring',
        'permissions', 'roles', 'model_has_permissions', 'model_has_roles', 'role_has_permissions',
    ];

    protected string $modelNamespace = 'App\\Models';

    public function handle(): int
    {
        $migrationsPath = $this->option('path') ?: database_path('migrations');
        $outPath = $this->option('out') ?: database_path('factories');
        $this->modelNamespace = rtrim($this->option('model-namespace') ?: 'App\\Models', '\\');

        if (! File::isDirectory($migrationsPath)) {
            $this->error("Migrations path not found: {$migrationsPath}");

            return self::FAILURE;
        }

        $files = collect(File::files($migrationsPath))
            ->filter(fn ($f) => $f->getExtension() === 'php')
            ->sortBy(fn ($f) => $f->getFilename())
            ->values();

        if ($files->isEmpty()) {
            $this->error('No migration files found.');

            return self::FAILURE;
        }

        $schemas = [];   // table => ['columns' => [name => meta], 'order' => [names...]]

        foreach ($files as $file) {
            $this->applyMigration(File::get($file->getPathname()), $schemas);
        }

        $wanted = collect($this->argument('tables'))->map(fn ($t) => Str::lower($t));
        $onlySystem = $this->option('include-system');

        $tables = collect(array_keys($schemas))
            ->when($wanted->isNotEmpty(), fn ($c) => $c->filter(fn ($t) => $wanted->contains(Str::lower($t))))
            ->when(! $wanted->isNotEmpty() && ! $onlySystem, fn ($c) => $c->reject(fn ($t) => in_array($t, $this->systemTables, true)))
            ->sort()
            ->values();

        if ($tables->isEmpty()) {
            $this->warn('Nothing to generate (no matching, non-system tables found).');

            return self::SUCCESS;
        }

        if ($this->option('list')) {
            foreach ($tables as $table) {
                $this->line("<fg=cyan>{$table}</>");
                foreach ($schemas[$table]['order'] as $col) {
                    $meta = $schemas[$table]['columns'][$col];
                    $this->line("  - {$col}: {$meta['type']}".($meta['nullable'] ? ' (nullable)' : ''));
                }
            }

            return self::SUCCESS;
        }

        File::ensureDirectoryExists($outPath);

        foreach ($tables as $table) {
            $this->writeFactory($table, $schemas[$table], $outPath);
        }

        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * Mutate $schemas in place by applying one migration file's Schema::create
     * and Schema::table calls, in the order they appear in the file.
     */
    protected function applyMigration(string $content, array &$schemas): void
    {
        // Only look inside up() — down() bodies revert/add columns for rollback
        // purposes and must not be treated as forward schema changes.
        $content = $this->extractUpMethodBody($content);
        $offset = 0;

        while (preg_match('/Schema(?:::connection\([^)]*\))?::(create|table)\(\s*[\'"]([a-zA-Z0-9_]+)[\'"]\s*,\s*function[^{]*\{/', $content, $m, PREG_OFFSET_CAPTURE, $offset)) {
            $type = $m[1][0];
            $table = $m[2][0];
            $braceStart = $m[0][1] + strlen($m[0][0]) - 1; // position of the opening '{'
            $body = $this->extractBalanced($content, $braceStart);
            $offset = $braceStart + strlen($body) + 1;

            $columns = $this->parseColumns($body);

            if ($type === 'create') {
                $schemas[$table] = ['columns' => [], 'order' => []];
                foreach ($columns as $col) {
                    $this->applyColumn($schemas[$table], $col, isNew: true);
                }
            } else {
                if (! isset($schemas[$table])) {
                    // Table altered before we ever saw a create for it (e.g. package
                    // migration not included in this scan) — start tracking it anyway.
                    $schemas[$table] = ['columns' => [], 'order' => []];
                }
                foreach ($columns as $col) {
                    $this->applyColumn($schemas[$table], $col, isNew: false);
                }
            }
        }
    }

    /**
     * Isolate the body of `public function up(): void { ... }` so that we
     * never read Schema calls from the corresponding down() rollback method.
     * Falls back to the full file if no up() method is found (e.g. anonymous
     * one-off scripts), since it's better to over-read than silently skip.
     */
    protected function extractUpMethodBody(string $content): string
    {
        if (! preg_match('/function\s+up\s*\([^)]*\)(?:\s*:\s*\??\\\\?[A-Za-z_][A-Za-z0-9_\\\\]*)?\s*\{/', $content, $m, PREG_OFFSET_CAPTURE)) {
            return $content;
        }

        $braceStart = $m[0][1] + strlen($m[0][0]) - 1;

        return $this->extractBalanced($content, $braceStart);
    }

    /**
     * Given the string and the index of an opening '{', return the substring
     * of its contents up to (not including) the matching closing '}'.
     */
    protected function extractBalanced(string $content, int $openBraceIndex): string
    {
        $depth = 0;
        $len = strlen($content);

        for ($i = $openBraceIndex; $i < $len; $i++) {
            if ($content[$i] === '{') {
                $depth++;
            } elseif ($content[$i] === '}') {
                $depth--;
                if ($depth === 0) {
                    return substr($content, $openBraceIndex + 1, $i - $openBraceIndex - 1);
                }
            }
        }

        return substr($content, $openBraceIndex + 1);
    }

    /**
     * Parse `$table->method('col', ...)->chained(...)->more();` style lines
     * out of a Schema::create/table closure body.
     */
    protected function parseColumns(string $body): array
    {
        $columns = [];

        preg_match_all('/\$table->(\w+)\(([^;]*?)\);/s', $body, $matches, PREG_SET_ORDER);

        foreach ($matches as $stmt) {
            $method = $stmt[1];
            // The outer regex's literal ");" terminator consumes the base call's
            // closing paren without capturing it — add it back before splitting.
            $rest = $stmt[2].')';

            // Split off the base call's arguments from any chained ->modifier() calls.
            // rest looks like: 'name', 18, 3)->nullable()->change()
            if (! preg_match('/^(.*?)\)((?:\s*->\s*\w+\([^)]*\))*)\s*$/s', $rest, $parts)) {
                continue;
            }

            $argsRaw = $parts[1];
            $chain = $parts[2] ?? '';

            $skipMethods = ['unique', 'index', 'primary', 'foreign', 'dropIndex', 'dropUnique', 'dropPrimary', 'dropForeign', 'engine'];
            if (in_array($method, $skipMethods, true)) {
                continue;
            }

            $firstArg = null;
            if (preg_match('/^\s*[\'"]([^\'"]*)[\'"]/', $argsRaw, $am)) {
                $firstArg = $am[1];
            }

            $isNullable = (bool) preg_match('/->\s*nullable\s*\(/', $chain);
            $isChange = (bool) preg_match('/->\s*change\s*\(/', $chain);
            $isUnique = (bool) preg_match('/->\s*unique\s*\(/', $chain);
            $isConstrained = (bool) preg_match('/->\s*constrained\s*\(/', $chain);
            $default = null;
            if (preg_match('/->\s*default\s*\(([^)]*)\)/', $chain, $dm)) {
                $default = trim($dm[1]);
            }

            switch ($method) {
                case 'dropColumn':
                    $names = [];
                    if (preg_match('/\[(.*)\]/s', $argsRaw, $arrMatch)) {
                        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $arrMatch[1], $nm);
                        $names = $nm[1];
                    } elseif ($firstArg !== null) {
                        $names = [$firstArg];
                    }
                    foreach ($names as $n) {
                        $columns[] = ['op' => 'drop', 'name' => $n];
                    }
                    break;

                case 'dropConstrainedForeignId':
                    if ($firstArg !== null) {
                        $columns[] = ['op' => 'drop', 'name' => $firstArg];
                    }
                    break;

                case 'id':
                case 'bigIncrements':
                case 'increments':
                    $columns[] = ['op' => 'set', 'name' => $firstArg ?? 'id', 'type' => 'id', 'nullable' => false];
                    break;

                case 'timestamps':
                case 'timestampsTz':
                    $columns[] = ['op' => 'set', 'name' => 'created_at', 'type' => 'timestamp', 'nullable' => true];
                    $columns[] = ['op' => 'set', 'name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true];
                    break;

                case 'softDeletes':
                case 'softDeletesTz':
                    $columns[] = ['op' => 'set', 'name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true];
                    break;

                case 'rememberToken':
                    $columns[] = ['op' => 'set', 'name' => 'remember_token', 'type' => 'string', 'nullable' => true];
                    break;

                case 'morphs':
                case 'nullableMorphs':
                    $columns[] = ['op' => 'set', 'name' => ($firstArg ?? 'model').'_id', 'type' => 'unsignedBigInteger', 'nullable' => $method === 'nullableMorphs'];
                    $columns[] = ['op' => 'set', 'name' => ($firstArg ?? 'model').'_type', 'type' => 'string', 'nullable' => $method === 'nullableMorphs'];
                    break;

                case 'foreignId':
                case 'foreignUuid':
                case 'foreignIdFor':
                    if ($firstArg !== null) {
                        $columns[] = [
                            'op' => $isChange ? 'change' : 'set',
                            'name' => $firstArg,
                            'type' => $method === 'foreignUuid' ? 'uuid' : 'unsignedBigInteger',
                            'nullable' => $isNullable,
                            'foreign' => true,
                        ];
                    }
                    break;

                default:
                    if ($firstArg === null) {
                        break; // e.g. $table->foreign(...)->references(...) already skipped above
                    }
                    $columns[] = [
                        'op' => $isChange ? 'change' : 'set',
                        'name' => $firstArg,
                        'type' => $method,
                        'nullable' => $isNullable,
                        'unique' => $isUnique,
                        'default' => $default,
                        'foreign' => $isConstrained || Str::endsWith($firstArg, '_id'),
                        'args' => $argsRaw,
                    ];
                    break;
            }
        }

        return $columns;
    }

    protected function applyColumn(array &$table, array $col, bool $isNew): void
    {
        if ($col['op'] === 'drop') {
            unset($table['columns'][$col['name']]);
            $table['order'] = array_values(array_filter($table['order'], fn ($n) => $n !== $col['name']));

            return;
        }

        $name = $col['name'];
        $meta = $table['columns'][$name] ?? [];

        $meta['type'] = $col['type'];
        $meta['nullable'] = $col['nullable'] ?? ($meta['nullable'] ?? false);
        $meta['unique'] = $col['unique'] ?? ($meta['unique'] ?? false);
        $meta['default'] = array_key_exists('default', $col) ? $col['default'] : ($meta['default'] ?? null);
        $meta['foreign'] = $col['foreign'] ?? ($meta['foreign'] ?? false);
        $meta['args'] = $col['args'] ?? ($meta['args'] ?? '');

        if (! isset($table['columns'][$name])) {
            $table['order'][] = $name;
        }
        $table['columns'][$name] = $meta;
    }

    protected function writeFactory(string $table, array $schema, string $outPath): void
    {
        $model = Str::studly(Str::singular($table));
        $factoryClass = "{$model}Factory";
        $target = "{$outPath}/{$factoryClass}.php";

        if (File::exists($target) && ! $this->option('force')) {
            $this->line("<fg=yellow>Skipped</> {$factoryClass} (already exists, use --force to overwrite)");

            return;
        }

        $fields = [];
        $uses = ["use {$this->modelNamespace}\\{$model};", 'use Illuminate\\Database\\Eloquent\\Factories\\Factory;'];
        $needsHash = false;
        $needsStr = false;

        foreach ($schema['order'] as $col) {
            if (in_array($col, ['id', 'created_at', 'updated_at', 'deleted_at'], true)) {
                continue;
            }

            $meta = $schema['columns'][$col];
            [$expr, $needs] = $this->fakerExpression($col, $meta);

            if ($needs === 'hash') {
                $needsHash = true;
            } elseif ($needs === 'str') {
                $needsStr = true;
            } elseif ($needs === 'model' && $expr['model'] !== $model) {
                $fqcn = "{$this->modelNamespace}\\{$expr['model']}";
                $useLine = "use {$fqcn};";
                if (! in_array($useLine, $uses, true)) {
                    $uses[] = $useLine;
                }
            }

            $value = is_array($expr) ? $expr['expr'] : $expr;
            $comment = $meta['nullable'] ? '  // nullable' : '';
            $fields[] = "            '{$col}' => {$value},{$comment}";
        }

        if ($needsHash) {
            $uses[] = 'use Illuminate\\Support\\Facades\\Hash;';
        }
        if ($needsStr) {
            $uses[] = 'use Illuminate\\Support\\Str;';
        }

        sort($uses);
        $usesBlock = implode("\n", array_unique($uses));
        $fieldsBlock = implode("\n", $fields);

        $stub = <<<PHP
<?php

namespace Database\Factories;

{$usesBlock}

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\\{$this->modelNamespace}\\{$model}>
 *
 * Auto-generated from migrations by `php artisan make:factories`.
 * Review before committing — heuristics on column name/type won't always
 * guess the right fake data or relationship.
 */
class {$factoryClass} extends Factory
{
    protected \$model = {$model}::class;

    public function definition(): array
    {
        return [
{$fieldsBlock}
        ];
    }
}

PHP;

        File::put($target, $stub);
        $this->line("<fg=green>Created</> {$factoryClass} -> {$target}");
    }

    /**
     * Guess a faker expression for a column. Returns [expr, needsImport|null].
     * needsImport is 'hash' | 'str' | 'model' | null.
     */
    protected function fakerExpression(string $name, array $meta): array
    {
        $type = $meta['type'];
        $lower = Str::lower($name);

        // Foreign keys: point at the related model's factory.
        if (($meta['foreign'] ?? false) || Str::endsWith($name, '_id')) {
            $related = Str::studly(Str::singular(Str::beforeLast($name, '_id')));
            if ($related !== '') {
                return [['expr' => "{$related}::factory()", 'model' => $related], 'model'];
            }
        }

        // Name-based heuristics take priority over raw column type.
        return match (true) {
            $lower === 'uuid' => ["\$this->faker->uuid()", null],
            Str::contains($lower, 'email') => ["\$this->faker->unique()->safeEmail()", null],
            $lower === 'password' => ["Hash::make('password')", 'hash'],
            $lower === 'remember_token' => ["Str::random(10)", 'str'],
            Str::contains($lower, 'phone') => ["\$this->faker->unique()->numerify('+998#########')", null],
            $lower === 'name' || Str::endsWith($lower, '_name') => ["\$this->faker->name()", null],
            Str::contains($lower, ['token', 'uuid_token']) => ["Str::random(40)", 'str'],
            Str::contains($lower, ['code', 'barcode', 'sku']) => ["\$this->faker->unique()->ean13()", null],
            Str::contains($lower, ['status']) => ["\$this->faker->randomElement(['pending', 'active', 'completed'])", null],
            Str::contains($lower, ['type']) && $type === 'string' => ["\$this->faker->randomElement(['default', 'other'])", null],
            Str::contains($lower, ['key', 'slug']) => ["\$this->faker->unique()->slug(2)", null],
            Str::contains($lower, ['url', 'path', 'file']) => ["\$this->faker->filePath()", null],
            Str::contains($lower, ['image', 'avatar', 'photo']) => ["\$this->faker->imageUrl()", null],
            Str::contains($lower, ['price', 'amount', 'total', 'paid', 'debt', 'subtotal']) && in_array($type, ['integer', 'bigInteger', 'unsignedBigInteger', 'unsignedInteger']) => ["\$this->faker->numberBetween(1000, 500000)", null],
            Str::contains($lower, ['price', 'amount', 'total']) && in_array($type, ['float', 'double', 'decimal']) => ["\$this->faker->randomFloat(2, 1000, 500000)", null],
            Str::contains($lower, 'quantity') && in_array($type, ['float', 'double', 'decimal']) => ["\$this->faker->randomFloat(3, 1, 100)", null],
            Str::contains($lower, 'quantity') => ["\$this->faker->numberBetween(1, 100)", null],
            $type === 'boolean' => ["\$this->faker->boolean()", null],
            in_array($type, ['text', 'mediumText', 'longText']) => ["\$this->faker->paragraph()", null],
            $type === 'json' => ["[]", null],
            in_array($type, ['integer', 'bigInteger', 'unsignedBigInteger', 'unsignedInteger', 'tinyInteger', 'smallInteger']) => ["\$this->faker->numberBetween(1, 1000)", null],
            in_array($type, ['float', 'double', 'decimal']) => ["\$this->faker->randomFloat(2, 1, 1000)", null],
            in_array($type, ['date']) => ["\$this->faker->date()", null],
            in_array($type, ['dateTime', 'timestamp', 'dateTimeTz', 'timestampTz']) => ["\$this->faker->dateTimeBetween('-6 months', 'now')", null],
            $type === 'string' => ["ucfirst(\$this->faker->words(3, true))", null],
            default => ["\$this->faker->word()", null],
        };
    }
}
