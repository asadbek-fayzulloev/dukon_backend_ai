<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;

/**
 * Generates PHPUnit feature test scaffolds from routes/api.php.
 *
 * One test class is created per resource folder — mirroring the same
 * nested grouping used by postman:generate (static URI segments become
 * folders/namespaces, path parameters like {id} are skipped) — with one
 * test method per route+verb, named test_index/test_show/test_create/
 * test_update/test_delete to match the Postman naming, or a name derived
 * from the route name for custom (non-CRUD) actions.
 *
 * SHOW/UPDATE/DELETE tests (single trailing path parameter) get a real
 * model seeded via its factory, and the URL is built from that model's
 * actual id rather than a hardcoded placeholder — the model class is
 * detected from route-model-binding on the controller method where
 * possible, falling back to guessing App\Models\{Singular(ResourceName)}.
 *
 * Request bodies for POST/PUT/PATCH are inferred the same way as the
 * Postman generator: a Spatie Laravel Data class type-hinted directly on
 * the controller method, a classic FormRequest, or a Data class type-hinted
 * on an injected Action's handle()/execute()/__invoke() (Controller ->
 * Action -> DTO pattern).
 *
 * These are scaffolds, not finished tests — review each one and fill in
 * auth setup, factories/seeding, and real assertions.
 *
 * Usage:
 *   php artisan tests:generate-api
 *   php artisan tests:generate-api --prefix=api/v1 --output=tests/Feature/Api
 *   php artisan tests:generate-api --force   (overwrite existing test files)
 */
class GenerateApiTests extends Command
{
    protected $signature = 'tests:generate-api
        {--prefix= : Only include routes whose URI starts with this prefix, e.g. api}
        {--output=tests/Feature/Api : Directory to write generated test classes into}
        {--force : Overwrite existing test files instead of skipping them}';

    protected $description = 'Generate PHPUnit feature test scaffolds from registered API routes, grouped and named to match postman:generate';

    public function handle(): int
    {
        $prefix = trim($this->option('prefix') ?? 'api', '/');
        $outputDir = base_path($this->option('output'));
        $force = (bool) $this->option('force');

        $prefixSegments = array_values(array_filter(explode('/', $prefix)));

        $routes = collect(RouteFacade::getRoutes())
            ->filter(function (Route $route) use ($prefix) {
                if (!Str::startsWith($route->uri(), $prefix)) {
                    return false;
                }
                if (Str::contains($route->uri(), ['sanctum/', '_ignition', 'telescope', 'horizon'])) {
                    return false;
                }
                return true;
            })
            ->values();

        $this->info("Found {$routes->count()} route(s) under prefix '{$prefix}'.");

        // Group routes the same way postman:generate does: every static URI
        // segment becomes a folder level, path parameters are skipped.
        $grouped = [];

        foreach ($routes as $route) {
            /** @var Route $route */
            $methods = array_diff($route->methods(), ['HEAD']);
            $uri = $route->uri();

            $segments = explode('/', trim($uri, '/'));
            $remaining = array_slice($segments, count($prefixSegments));

            $folderPath = [];
            foreach ($remaining as $seg) {
                if ($seg === '' || Str::startsWith($seg, '{')) {
                    continue;
                }
                $folderPath[] = Str::studly($seg);
            }

            if (empty($folderPath)) {
                $folderPath = ['Root'];
            }

            $key = implode('/', $folderPath);

            foreach ($methods as $method) {
                $grouped[$key][] = [$route, $method];
            }
        }

        $created = 0;
        $skipped = 0;

        foreach ($grouped as $key => $pairs) {
            $folderPath = explode('/', $key);
            $className = array_pop($folderPath) . 'Test';
            $namespaceSuffix = implode('\\', $folderPath);
            $namespace = 'Tests\\Feature\\Api' . ($namespaceSuffix ? '\\' . $namespaceSuffix : '');

            $dirPath = $outputDir . ($namespaceSuffix ? '/' . str_replace('\\', '/', $namespaceSuffix) : '');
            $filePath = $dirPath . '/' . $className . '.php';

            if (file_exists($filePath) && !$force) {
                $this->warn("Skipped (already exists): {$filePath} — pass --force to overwrite");
                $skipped++;
                continue;
            }

            @mkdir($dirPath, 0755, true);
            file_put_contents($filePath, $this->generateTestFileContent($namespace, $className, $pairs));
            $this->info("Created: {$filePath}");
            $created++;
        }

        $this->info("Done. Created {$created} test file(s), skipped {$skipped} existing file(s).");
        $this->line('These are scaffolds — review each for auth setup, factories/seeding, and real assertions.');

        return self::SUCCESS;
    }

    protected function generateTestFileContent(string $namespace, string $className, array $routeMethodPairs): string
    {
        $usedNames = [];
        $methods = [];

        foreach ($routeMethodPairs as [$route, $method]) {
            /** @var Route $route */
            $uri = $route->uri();
            $methodName = $this->testMethodName($route, $method, $uri, $usedNames);
            $methods[] = $this->buildTestMethod($route, $method, $methodName);
        }

        $methodsCode = implode("\n\n", $methods);

        return <<<PHP
<?php

namespace {$namespace};

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class {$className} extends TestCase
{
    use RefreshDatabase;

{$methodsCode}
}

PHP;
    }

    protected function testMethodName(Route $route, string $method, string $uri, array &$usedNames): string
    {
        $label = $this->resourceActionLabel($route, $method, $uri);

        if ($label) {
            $base = 'test_' . Str::lower($label);
        } else {
            $routeName = $route->getName();
            $base = $routeName
                ? 'test_' . Str::snake(str_replace('.', '_', $routeName))
                : 'test_' . Str::snake($method . '_' . preg_replace('/[^a-zA-Z0-9]+/', '_', trim($uri, '/')));
        }

        // Route names commonly contain hyphens (e.g. "auth.forgot-password"),
        // which Str::snake() does not strip — left as-is they produce an
        // invalid PHP method name and a hard parse error. Collapse any
        // non-identifier character to an underscore and make sure the name
        // doesn't start with a digit.
        $base = preg_replace('/[^A-Za-z0-9_]/', '_', $base);
        $base = preg_replace('/_+/', '_', $base);
        $base = trim($base, '_');
        if ($base === '' || ctype_digit($base[0])) {
            $base = 'test_' . $base;
        }

        $name = $base;
        $i = 2;
        while (in_array($name, $usedNames, true)) {
            $name = $base . '_' . $i;
            $i++;
        }
        $usedNames[] = $name;

        return $name;
    }

    protected function buildTestMethod(Route $route, string $method, string $methodName): string
    {
        $uri = $route->uri();
        $httpMethod = strtolower($method);

        // Login is a special case: it must NOT be authenticated up front
        // (that's the whole point of the endpoint), and needs a real seeded
        // user with known credentials instead of a random factory user.
        if ($httpMethod === 'post' && $this->isLoginRoute($route, $uri)) {
            $callUri = '/' . ltrim(preg_replace('/\{(\w+)\??\}/', '1', $uri), '/');
            return $this->buildLoginTestMethod($methodName, $callUri);
        }

        preg_match_all('/\{(\w+)\??\}/', $uri, $paramMatches);
        $paramNames = $paramMatches[1];

        $label = $this->resourceActionLabel($route, $method, $uri);
        $needsModelFactory = in_array($label, ['SHOW', 'UPDATE', 'DELETE'], true) && count($paramNames) === 1;

        $callMethodMap = [
            'get' => 'getJson',
            'post' => 'postJson',
            'put' => 'putJson',
            'patch' => 'patchJson',
            'delete' => 'deleteJson',
        ];
        $callMethod = $callMethodMap[$httpMethod] ?? 'json';

        $body = $this->extractRequestBody($route, $method);
        $expectedStatus = match (true) {
            $httpMethod === 'post' => 200,
            $httpMethod === 'delete' => 200,
            default => 200,
        };

        $lines = [];
        $lines[] = "    public function {$methodName}(): void";
        $lines[] = '    {';
        $lines[] = '        Sanctum::actingAs(Admin::factory()->create(), [\'*\'], \'api\');';

        $callUriExpr = null;

        if ($needsModelFactory) {
            $modelClass = $this->resolveModelForRoute($route);

            if ($modelClass) {
                $lines[] = '';
                $lines[] = "        \$model = \\{$modelClass}::factory()->create();";

                // Build the call URL as a concatenated expression using the
                // freshly created model's real id, e.g.
                // '/api/v1/orders/' . $model->id
                $template = '/' . ltrim(preg_replace('/\{(\w+)\??\}/', '__ID__', $uri), '/');
                [$before, $after] = array_pad(explode('__ID__', $template, 2), 2, '');
                $callUriExpr = var_export($before, true) . ' . $model->id . ' . var_export($after, true);
            } else {
                $lines[] = '';
                $lines[] = '        // TODO: could not auto-detect the Eloquent model for this resource —';
                $lines[] = '        // seed one manually (e.g. $model = Order::factory()->create();) and use its id below';
            }
        }

        if ($callUriExpr === null) {
            $callUri = '/' . ltrim(preg_replace('/\{(\w+)\??\}/', '1', $uri), '/');
            $callUriExpr = var_export($callUri, true);
        }

        if ($body !== null) {
            $lines[] = '';
            $lines[] = '        $payload = ' . $this->exportPhpArray($body, 2) . ';';
            $lines[] = '';
            $lines[] = "        \$response = \$this->{$callMethod}({$callUriExpr}, \$payload);";
        } else {
            $lines[] = '';
            $lines[] = "        \$response = \$this->{$callMethod}({$callUriExpr});";
        }

        $lines[] = '';
        $lines[] = "        \$response->assertStatus({$expectedStatus}); // TODO: adjust if needed";
        $lines[] = '    }';

        return implode("\n", $lines);
    }

    /**
     * Detects the login endpoint by route name (".login" suffix) or, for
     * unnamed routes, a URI ending in "/login".
     */
    protected function isLoginRoute(Route $route, string $uri): bool
    {
        $routeName = $route->getName();
        if ($routeName && Str::afterLast($routeName, '.') === 'login') {
            return true;
        }

        return Str::endsWith(rtrim($uri, '/'), 'login');
    }

    protected function buildLoginTestMethod(string $methodName, string $callUri): string
    {
        return <<<PHP
    public function {$methodName}(): void
    {
        // Seed a user with credentials we control, so we can log in with them
        \$admin = Admin::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'), // skip Hash::make() if your
            // Admin model already casts password => 'hashed'
        ]);

        \$payload = [
            'username' => 'admin@admin.com',
            'password' => 'password',
        ];

        \$response = \$this->postJson('{$callUri}', \$payload);

        \$response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token' => ['access_token', 'token_type', 'expires_in'],
                    'refresh_token' => ['refresh_token', 'token_type', 'expires_in'],
                ],
            ]); // adjust keys to match your actual response shape
    }
PHP;
    }

    /**
     * Try to find the Eloquent model class this route operates on:
     *   1. A parameter on the controller method that's an Eloquent Model
     *      subclass (i.e. route-model-binding), which is the most reliable
     *      signal since it's exactly what the route resolves.
     *   2. Fall back to guessing App\Models\{Singular(ResourceSegment)}
     *      from the URI segment immediately before the trailing parameter.
     */
    protected function resolveModelForRoute(Route $route): ?string
    {
        $action = $route->getAction();

        if (isset($action['controller']) && Str::contains($action['controller'], '@')) {
            [$class, $methodName] = explode('@', $action['controller']);

            if (class_exists($class) && method_exists($class, $methodName)) {
                try {
                    $reflection = new ReflectionMethod($class, $methodName);

                    foreach ($reflection->getParameters() as $param) {
                        $type = $param->getType();
                        if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                            continue;
                        }

                        $typeName = $type->getName();
                        if (class_exists($typeName) && is_subclass_of($typeName, Model::class)) {
                            return $typeName;
                        }
                    }
                } catch (\ReflectionException $e) {
                    // fall through to guessing
                }
            }
        }

        return $this->guessModelFromUri($route->uri());
    }

    protected function guessModelFromUri(string $uri): ?string
    {
        $segments = explode('/', trim($uri, '/'));

        $paramIndexes = [];
        foreach ($segments as $i => $seg) {
            if (Str::startsWith($seg, '{')) {
                $paramIndexes[] = $i;
            }
        }

        if (empty($paramIndexes)) {
            return null;
        }

        $lastParamIndex = end($paramIndexes);
        $resourceSegment = $segments[$lastParamIndex - 1] ?? null;

        if (!$resourceSegment) {
            return null;
        }

        $guessClass = 'App\\Models\\' . Str::studly(Str::singular($resourceSegment));

        return class_exists($guessClass) ? $guessClass : null;
    }

    /**
     * Pretty-print a PHP value as short-array-syntax source code, indented
     * to sit correctly inside a generated test method body.
     */
    protected function exportPhpArray(mixed $value, int $indent = 2): string
    {
        if (is_array($value)) {
            if (empty($value)) {
                return '[]';
            }

            $pad = str_repeat('    ', $indent + 1);
            $closePad = str_repeat('    ', $indent);
            $isList = array_is_list($value);
            $lines = [];

            foreach ($value as $k => $v) {
                $valueStr = $this->exportPhpArray($v, $indent + 1);
                $lines[] = $isList
                    ? "{$pad}{$valueStr},"
                    : "{$pad}" . var_export((string) $k, true) . " => {$valueStr},";
            }

            return "[\n" . implode("\n", $lines) . "\n{$closePad}]";
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if (is_null($value)) {
            return 'null';
        }

        return var_export($value, true);
    }

    /**
     * Same REST-action labeling used by postman:generate — kept in sync so
     * test method names match the Postman item names for the same route.
     */
    protected function resourceActionLabel(Route $route, string $method, string $uri): ?string
    {
        $method = strtoupper($method);
        $routeName = $route->getName();

        $nameMap = [
            'index' => 'INDEX',
            'show' => 'SHOW',
            'store' => 'CREATE',
            'create' => 'CREATE',
            'update' => 'UPDATE',
            'edit' => 'UPDATE',
            'destroy' => 'DELETE',
            'delete' => 'DELETE',
        ];

        if ($routeName) {
            $lastSegment = Str::afterLast($routeName, '.');
            if (isset($nameMap[$lastSegment])) {
                return $nameMap[$lastSegment];
            }
            return null;
        }

        $endsWithParam = (bool) preg_match('/\{[^}]+\}\??$/', rtrim($uri, '/'));

        return match (true) {
            $method === 'POST' => 'CREATE',
            in_array($method, ['PUT', 'PATCH'], true) => 'UPDATE',
            $method === 'DELETE' => 'DELETE',
            $method === 'GET' && $endsWithParam => 'SHOW',
            $method === 'GET' => 'INDEX',
            default => null,
        };
    }

    /**
     * Same request-body inference used by postman:generate: Spatie Data
     * directly on the controller, FormRequest, or Data on an injected
     * Action's handle()/execute()/__invoke().
     */
    protected function extractRequestBody(Route $route, string $method): ?array
    {
        if (!in_array(strtoupper($method), ['POST', 'PUT', 'PATCH'])) {
            return null;
        }

        $action = $route->getAction();
        if (!isset($action['controller'])) {
            return null;
        }

        [$class, $methodName] = Str::contains($action['controller'], '@')
            ? explode('@', $action['controller'])
            : [null, null];

        if (!$class || !class_exists($class) || !method_exists($class, $methodName)) {
            return ['example_field' => 'value'];
        }

        try {
            $reflection = new ReflectionMethod($class, $methodName);
        } catch (\ReflectionException $e) {
            return ['example_field' => 'value'];
        }

        foreach ($reflection->getParameters() as $param) {
            $type = $param->getType();
            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                continue;
            }

            $typeName = $type->getName();

            if ($this->isSpatieDataClass($typeName)) {
                $body = $this->dataClassToExampleBody($typeName);
                if ($body !== null) {
                    return $body;
                }
            }

            if (is_subclass_of($typeName, FormRequest::class)) {
                try {
                    $formRequest = new $typeName();
                    $rules = method_exists($formRequest, 'rules') ? $formRequest->rules() : [];
                    return $this->rulesToExampleBody($rules);
                } catch (\Throwable $e) {
                    // fall through
                }
            }

            if (class_exists($typeName) && !is_subclass_of($typeName, FormRequest::class)) {
                $body = $this->resolveDtoFromAction($typeName);
                if ($body !== null) {
                    return $body;
                }
            }
        }

        return ['example_field' => 'value'];
    }

    protected function isSpatieDataClass(string $class): bool
    {
        return class_exists('Spatie\\LaravelData\\Data')
            && class_exists($class)
            && is_subclass_of($class, 'Spatie\\LaravelData\\Data');
    }

    protected function resolveDtoFromAction(string $actionClass): ?array
    {
        foreach (['handle', 'execute', '__invoke'] as $actionMethod) {
            if (!method_exists($actionClass, $actionMethod)) {
                continue;
            }

            try {
                $reflection = new ReflectionMethod($actionClass, $actionMethod);
            } catch (\ReflectionException $e) {
                continue;
            }

            foreach ($reflection->getParameters() as $param) {
                $type = $param->getType();
                if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    continue;
                }

                if ($this->isSpatieDataClass($type->getName())) {
                    $body = $this->dataClassToExampleBody($type->getName());
                    if ($body !== null) {
                        return $body;
                    }
                }
            }
        }

        return null;
    }

    protected function dataClassToExampleBody(string $class, int $depth = 0): ?array
    {
        if ($depth > 3 || !class_exists($class)) {
            return null;
        }

        try {
            $reflection = new ReflectionClass($class);
            $constructor = $reflection->getConstructor();
        } catch (\ReflectionException $e) {
            return null;
        }

        if ($constructor && count($constructor->getParameters()) > 0) {
            $body = [];
            foreach ($constructor->getParameters() as $param) {
                $fallback = $this->exampleValueForType($param->getType(), $depth, $param->getName());
                $body[$param->getName()] = $this->exampleValueFromHints($this->ruleHintsFor($param), $fallback);
            }
            return $body ?: null;
        }

        $body = [];
        foreach ($reflection->getProperties(ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }
            $fallback = $this->exampleValueForType($property->getType(), $depth, $property->getName());
            $body[$property->getName()] = $this->exampleValueFromHints($this->ruleHintsFor($property), $fallback);
        }

        return $body ?: null;
    }

    protected function ruleHintsFor(\ReflectionParameter|ReflectionProperty $member): array
    {
        $hints = [];

        foreach ($member->getAttributes() as $attribute) {
            if (!Str::startsWith($attribute->getName(), 'Spatie\\LaravelData\\Attributes\\')) {
                continue;
            }

            $hints[] = class_basename($attribute->getName());

            foreach ($attribute->getArguments() as $arg) {
                if (is_string($arg)) {
                    $hints[] = $arg;
                } elseif (is_array($arg)) {
                    foreach ($arg as $inner) {
                        if (is_string($inner)) {
                            $hints[] = $inner;
                        }
                    }
                }
            }
        }

        return $hints;
    }

    protected function exampleValueFromHints(array $hints, mixed $fallback): mixed
    {
        if (empty($hints)) {
            return $fallback;
        }

        $combined = implode('|', $hints);

        if (preg_match('/date_format:([^|]+)/', $combined, $m)) {
            try {
                return now()->format(trim($m[1]));
            } catch (\Throwable $e) {
                // fall through
            }
        }

        return match (true) {
            Str::contains($combined, ['Email', 'email']) => 'user@example.com',
            Str::contains($combined, ['Uuid', 'uuid']) => (string) Str::uuid(),
            Str::contains($combined, ['Url', 'url']) => 'https://example.com',
            Str::contains($combined, 'integer') => 1,
            Str::contains($combined, 'numeric') => 1,
            Str::contains($combined, 'boolean') => false,
            Str::contains($combined, 'date') => now()->toDateString(),
            default => $fallback,
        };
    }

    protected function exampleValueForType(?ReflectionType $type, int $depth, string $name = ''): mixed
    {
        if (!$type) {
            return $this->exampleStringForField($name);
        }

        if ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $inner) {
                if ($inner instanceof ReflectionNamedType && $inner->getName() !== 'null') {
                    return $this->exampleValueForType($inner, $depth, $name);
                }
            }
            return null;
        }

        if (!$type instanceof ReflectionNamedType) {
            return $this->exampleStringForField($name);
        }

        $typeName = $type->getName();

        return match (true) {
            $typeName === 'int' => $this->exampleIntForField($name),
            $typeName === 'float' => 1.0,
            $typeName === 'bool' => true,
            $typeName === 'string' => $this->exampleStringForField($name),
            $typeName === 'array' => [$this->exampleStringForField($name)],
            function_exists('enum_exists') && enum_exists($typeName) => $this->firstEnumValue($typeName),
            in_array($typeName, ['DateTime', 'DateTimeImmutable', 'Carbon\\Carbon', 'Illuminate\\Support\\Carbon'], true) => now()->toDateTimeString(),
            $this->isSpatieDataClass($typeName) => $this->dataClassToExampleBody($typeName, $depth + 1) ?? [],
            default => class_exists($typeName) ? [] : $this->exampleStringForField($name),
        };
    }

    /**
     * A non-empty, somewhat realistic value for a string field, guessed from
     * the field name. Empty strings/zeros are the most common cause of
     * spurious 422s against required/min validation, so every branch here
     * returns something a validator would actually accept.
     */
    protected function exampleStringForField(string $name): string
    {
        $key = Str::lower($name);

        return match (true) {
            Str::contains($key, 'email') => 'user@example.com',
            Str::contains($key, ['phone', 'tel', 'mobile']) => '+998901234567',
            Str::contains($key, 'password') => 'Password123!',
            Str::contains($key, ['first_name', 'firstname']) => 'John',
            Str::contains($key, ['last_name', 'lastname']) => 'Doe',
            Str::contains($key, ['name', 'title']) => 'Test Name',
            Str::contains($key, 'address') => 'Test address 123',
            Str::contains($key, ['description', 'note', 'comment', 'message', 'reason']) => 'Test description text',
            Str::contains($key, ['url', 'link', 'website']) => 'https://example.com',
            Str::contains($key, 'slug') => 'test-slug',
            Str::contains($key, 'code') => 'ABC123',
            Str::contains($key, 'uuid') => (string) Str::uuid(),
            default => 'Test value',
        };
    }

    /**
     * A non-zero int fallback guessed from the field name, since 0 commonly
     * fails min:1/gt:0 rules on amounts, quantities, and foreign keys.
     */
    protected function exampleIntForField(string $name): int
    {
        $key = Str::lower($name);

        return match (true) {
            Str::contains($key, ['amount', 'price', 'sum', 'total', 'balance']) => 1000,
            Str::contains($key, ['age']) => 25,
            Str::contains($key, ['quantity', 'qty', 'count']) => 1,
            Str::contains($key, ['_id', 'id']) => 1,
            default => 1,
        };
    }

    protected function firstEnumValue(string $enumClass): mixed
    {
        if (!enum_exists($enumClass)) {
            return null;
        }

        $cases = $enumClass::cases();
        if (empty($cases)) {
            return null;
        }

        $first = $cases[0];
        return property_exists($first, 'value') ? $first->value : $first->name;
    }

    protected function rulesToExampleBody(array $rules): array
    {
        $body = [];

        foreach ($rules as $field => $rule) {
            if (Str::contains($field, '*')) {
                continue;
            }

            $ruleString = is_array($rule) ? implode('|', $rule) : (string) $rule;

            $body[$field] = match (true) {
                Str::contains($ruleString, 'integer') => 1,
                Str::contains($ruleString, 'numeric') => 1,
                Str::contains($ruleString, 'boolean') => true,
                Str::contains($ruleString, 'email') => 'user@example.com',
                Str::contains($ruleString, 'date') => now()->toDateString(),
                Str::contains($ruleString, 'array') => [$this->exampleStringForField($field)],
                default => $this->exampleStringForField($field),
            };
        }

        return $body ?: ['example_field' => 'value'];
    }
}
