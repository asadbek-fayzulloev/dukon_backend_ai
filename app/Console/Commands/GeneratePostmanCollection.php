<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionProperty;
use ReflectionType;
use ReflectionUnionType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Generates a Postman Collection v2.1.0 JSON file directly from your
 * routes/api.php definitions, and optionally pushes it straight to your
 * Postman workspace via the Postman API.
 *
 * Folders in the generated collection mirror your routes/api/{module}/{file}.php
 * directory structure: each URI segment (except the last) becomes a nested
 * Postman folder, so v1/auth/login lands in a "V1 > Auth" folder.
 *
 * The Postman collection ID is stored inside the generated JSON file itself
 * (info._postman_id) and read back from there on each run — no .env writes,
 * no config caching to worry about. Delete the file to force a fresh create.
 *
 * Usage:
 *   php artisan postman:generate
 *   php artisan postman:generate --name="My API" --output=storage/app/postman_collection.json
 *   php artisan postman:generate --prefix=api
 *   php artisan postman:generate --sync                 (create/update in Postman)
 *   php artisan postman:generate --sync --from-file      (push existing file as-is, no regeneration)
 *
 * Requires in .env when using --sync:
 *   POSTMAN_API_KEY=PMAK-...
 *   WORKSPACE_ID=...   (optional, only used on first create)
 */
class GeneratePostmanCollection extends Command
{
    protected $signature = 'postman:generate
        {--name= : Collection name (defaults to config app.name)}
        {--output=storage/app/postman/collection.json : Where to read/write the collection file}
        {--prefix= : Only include routes whose URI starts with this prefix, e.g. api}
        {--base-url= : Base URL variable value, e.g. https://your-app.test}
        {--sync : Push the collection to your Postman workspace via the Postman API}
        {--workspace= : Postman workspace ID to create the collection in (only used on first sync, falls back to WORKSPACE_ID env)}
        {--from-file : Skip regenerating from routes and sync using the existing file at --output as-is}';

    protected $description = 'Generate a Postman Collection v2.1 JSON file from registered routes (routes/api.php), grouped into folders that mirror your route file directory structure';

    public function handle(): int
    {
        $outputPath = base_path($this->option('output'));

        if ($this->option('from-file')) {
            if (!file_exists($outputPath)) {
                $this->error("No file found at {$outputPath}. Run without --from-file first to generate one.");
                return self::FAILURE;
            }

            $collection = json_decode(file_get_contents($outputPath), true);
            if (!is_array($collection)) {
                $this->error("Could not parse JSON at {$outputPath}.");
                return self::FAILURE;
            }

            $this->info("Read existing collection from: {$outputPath}");
            return $this->syncToPostman($collection, $outputPath);
        }

        $prefix = trim($this->option('prefix') ?? 'api', '/');
        $baseUrl = $this->option('base-url') ?? config('app.url', 'http://localhost');
        $collectionName = $this->option('name') ?? config('app.name', 'Laravel API');

        $routes = collect(RouteFacade::getRoutes())
            ->filter(function (Route $route) use ($prefix) {
                if (!Str::startsWith($route->uri(), $prefix)) {
                    return false;
                }
                // Skip framework/internal routes
                if (Str::contains($route->uri(), ['sanctum/', '_ignition', 'telescope', 'horizon'])) {
                    return false;
                }
                return true;
            })
            ->values();

        $this->info("Found {$routes->count()} route(s) under prefix '{$prefix}'.");

        $prefixSegments = array_values(array_filter(explode('/', $prefix)));

        // Build a nested tree keyed by directory-like URI segments, so the
        // resulting Postman folders mirror routes/api/{module}/{file}.php.
        $tree = [];

        foreach ($routes as $route) {
            /** @var Route $route */
            $methods = array_diff($route->methods(), ['HEAD']);
            $uri = $route->uri();

            $segments = explode('/', trim($uri, '/'));
            // Strip the leading prefix segments (e.g. "api") before grouping
            $remaining = array_slice($segments, count($prefixSegments));

            // Every static (non-parameter) segment becomes a nested folder
            // level — regardless of whether it's the last segment or not —
            // so both "orders" and "orders/{id}" land in the same "Orders"
            // folder. Path parameters (e.g. {id}) are skipped entirely and
            // never become a folder of their own.
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

            foreach ($methods as $method) {
                $item = $this->buildItem($route, $method, $baseUrl);
                $this->insertIntoTree($tree, $folderPath, $item);
            }
        }

        // Reuse the existing collection's stored ID (if the file already
        // exists) so regenerating from routes doesn't lose the link to the
        // collection already synced in Postman.
        $existingId = null;
        if (file_exists($outputPath)) {
            $existing = json_decode(file_get_contents($outputPath), true);
            $existingId = $existing['info']['_postman_id'] ?? null;
        }

        $collection = [
            'info' => [
                'name' => $collectionName,
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
                '_postman_id' => $existingId ?: (string) Str::uuid(),
            ],
            'variable' => [
                [
                    'key' => 'base_url',
                    'value' => rtrim($baseUrl, '/'),
                    'type' => 'string',
                ],
                [
                    'key' => 'token',
                    'value' => '',
                    'type' => 'string',
                ],
            ],
            'auth' => [
                'type' => 'bearer',
                'bearer' => [
                    ['key' => 'token', 'value' => '{{token}}', 'type' => 'string'],
                ],
            ],
            'item' => $this->treeToPostmanFolders($tree),
        ];

        @mkdir(dirname($outputPath), 0755, true);
        file_put_contents($outputPath, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        $this->info("Postman collection written to: {$outputPath}");
        $this->line('Import it in Postman via File > Import, or drag the file into the app.');

        if ($this->option('sync')) {
            return $this->syncToPostman($collection, $outputPath);
        }

        return self::SUCCESS;
    }

    /**
     * Insert an item into a nested tree at the given folder path, creating
     * intermediate folder nodes as needed. Each node holds its own items
     * (__items) separately from its child folders (__children) so ordering
     * between "items at this level" and "subfolders" stays predictable.
     */
    protected function insertIntoTree(array &$tree, array $path, array $item): void
    {
        $key = array_shift($path);

        if (!isset($tree[$key])) {
            $tree[$key] = ['__items' => [], '__children' => []];
        }

        if (empty($path)) {
            $tree[$key]['__items'][] = $item;
            return;
        }

        $this->insertIntoTree($tree[$key]['__children'], $path, $item);
    }

    /**
     * Recursively convert the nested tree into Postman's folder/item shape:
     * { name, item: [...items..., ...subfolders...] }
     */
    protected function treeToPostmanFolders(array $tree): array
    {
        ksort($tree);

        $folders = [];
        foreach ($tree as $name => $node) {
            $childFolders = $this->treeToPostmanFolders($node['__children']);
            $folders[] = [
                'name' => $name,
                'item' => array_merge($node['__items'], $childFolders),
            ];
        }

        return $folders;
    }

    /**
     * Create (first run / no valid stored id) or update (subsequent runs)
     * the collection in Postman. The collection ID lives in the JSON file's
     * info._postman_id field — read from there, and written back there after
     * a create, so there is nothing to keep in sync in .env.
     */
    protected function syncToPostman(array $collection, string $outputPath): int
    {
        $apiKey = trim((string) env('POSTMAN_API_KEY'));
        $collectionId = trim((string) ($collection['info']['_postman_id'] ?? ''));

        if (!$apiKey) {
            $this->error('POSTMAN_API_KEY is empty. Set it in .env, then run: php artisan config:clear');
            return self::FAILURE;
        }

        $headers = [
            'X-Api-Key' => $apiKey,
            'Content-Type' => 'application/json',
        ];

        // Only IDs that look like real Postman uids (ownerId-uuid) are worth
        // trying against the API — a freshly generated local uuid has no
        // matching collection yet, so go straight to create for those.
        $looksLikeRealPostmanId = $collectionId && Str::contains($collectionId, '-') && strlen($collectionId) > 36;

        if ($looksLikeRealPostmanId) {
            $response = Http::withHeaders($headers)
                ->put("https://api.getpostman.com/collections/{$collectionId}", [
                    'collection' => $collection,
                ]);

            if ($response->successful()) {
                $this->info("Synced to existing Postman collection ({$collectionId}).");
                return self::SUCCESS;
            }

            $this->warn("Stored collection id ({$collectionId}) is no longer valid: " . $response->body());
            $this->warn('Creating a new collection instead...');
        }

        return $this->createAndPersist($collection, $headers, $outputPath);
    }

    protected function createAndPersist(array $collection, array $headers, string $outputPath): int
    {
        $workspace = $this->option('workspace') ?: env('WORKSPACE_ID');

        $url = 'https://api.getpostman.com/collections';
        if ($workspace) {
            $url .= '?workspace=' . urlencode($workspace);
        }

        $response = Http::withHeaders($headers)->post($url, ['collection' => $collection]);

        if ($response->failed()) {
            $this->error('Failed to create Postman collection: ' . $response->body());
            return self::FAILURE;
        }

        $newId = $response->json('collection.uid') ?? $response->json('collection.id');

        if (!$newId) {
            $this->error('Postman created the collection but did not return an id. Response: ' . $response->body());
            return self::FAILURE;
        }

        $this->info("Created new Postman collection: {$newId}");

        // Persist the real ID into the JSON file itself so the next run
        // reads it back from here — no .env, no config cache involved.
        $collection['info']['_postman_id'] = $newId;
        file_put_contents($outputPath, json_encode($collection, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->info("Collection id saved into {$outputPath} (info._postman_id). Future syncs will reuse it automatically.");

        return self::SUCCESS;
    }

    protected function buildItem(Route $route, string $method, string $baseUrl): array
    {
        $uri = $route->uri();
        $resourceLabel = $this->resourceActionLabel($route, $method, $uri);
        $name = $resourceLabel ?? ($route->getName() ?: ($method . ' /' . $uri));

        // Convert Laravel {param} to Postman :param path variables
        $postmanUri = preg_replace('/\{(\w+)\??\}/', ':$1', $uri);
        $pathParams = [];
        preg_match_all('/\{(\w+)\??\}/', $uri, $matches);
        foreach ($matches[1] as $param) {
            $pathParams[] = [
                'key' => $param,
                'value' => '',
                'description' => 'Path parameter',
            ];
        }

        $urlParts = explode('/', trim($postmanUri, '/'));

        $body = $this->extractRequestBody($route, $method);

        $item = [
            'name' => $name,
            'request' => [
                'method' => strtoupper($method),
                'header' => [
                    ['key' => 'Accept', 'value' => 'application/json'],
                    ['key' => 'Content-Type', 'value' => 'application/json'],
                ],
                'url' => [
                    'raw' => '{{base_url}}/' . $postmanUri,
                    'host' => ['{{base_url}}'],
                    'path' => $urlParts,
                ],
                'description' => 'Controller/Action: ' . $this->actionLabel($route),
            ],
            'response' => [],
        ];

        if (!empty($pathParams)) {
            $item['request']['url']['variable'] = $pathParams;
        }

        if ($body !== null) {
            $item['request']['body'] = [
                'mode' => 'raw',
                'raw' => json_encode($body, JSON_PRETTY_PRINT),
                'options' => ['raw' => ['language' => 'json']],
            ];
        }

        return $item;
    }

    /**
     * Map a route to a REST resource action label — INDEX, SHOW, CREATE,
     * UPDATE, DELETE — the way Laravel's Route::apiResource() names routes
     * (index/show/store/update/destroy). Returns null (leaving the caller to
     * fall back to the route name or method+uri) for genuinely custom,
     * non-CRUD actions like "auth.login" rather than mislabeling them.
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

            // Route has a real, custom name (e.g. "auth.login") — respect
            // it rather than force-fitting a CRUD label onto it.
            return null;
        }

        // No route name at all (closures / unnamed routes): best-effort
        // guess purely from HTTP verb + whether the URI ends in a path
        // parameter (i.e. targets a single resource vs. the collection).
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

    protected function actionLabel(Route $route): string
    {
        $action = $route->getAction();
        if (isset($action['controller'])) {
            return $action['controller'];
        }
        return 'Closure';
    }

    /**
     * Inspect the route's controller method signature and build an example
     * JSON request body. Checks, in order:
     *   1. A Spatie Laravel Data class type-hinted directly on the controller method
     *   2. A classic FormRequest type-hinted on the controller method
     *   3. An injected Action class whose handle()/execute()/__invoke() type-hints
     *      a Spatie Laravel Data class (Controller -> Action -> DTO pattern)
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

            // 1. Spatie Laravel Data object injected directly into the controller
            if ($this->isSpatieDataClass($typeName)) {
                $body = $this->dataClassToExampleBody($typeName);
                if ($body !== null) {
                    return $body;
                }
            }

            // 2. Classic FormRequest, for controllers not using Data yet
            if (is_subclass_of($typeName, FormRequest::class)) {
                try {
                    $formRequest = new $typeName();
                    $rules = method_exists($formRequest, 'rules') ? $formRequest->rules() : [];
                    return $this->rulesToExampleBody($rules);
                } catch (\Throwable $e) {
                    // fall through to other checks
                }
            }

            // 3. Controller -> Action -> DTO: an Action class is injected
            // into the controller method, and the DTO lives on the Action's
            // handle()/execute()/__invoke() signature instead.
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

    /**
     * Given an Action class (injected into the controller), inspect its
     * handle()/execute()/__invoke() method for a Spatie Data parameter and
     * build an example body from that instead.
     */
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

    /**
     * Reflect a Spatie Laravel Data class's constructor-promoted properties
     * and build an example JSON body from their types. Nested Data objects
     * are resolved recursively (capped to avoid runaway recursion on
     * self-referencing structures).
     */
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

        // Constructor-promoted style: class UpdateData { public function
        // __construct(public string $name) {} }
        if ($constructor && count($constructor->getParameters()) > 0) {
            $body = [];
            foreach ($constructor->getParameters() as $param) {
                $fallback = $this->exampleValueForType($param->getType(), $depth, $param->getName());
                $body[$param->getName()] = $this->exampleValueFromHints(
                    $this->ruleHintsFor($param),
                    $fallback
                );
            }
            return $body ?: null;
        }

        // Plain public property style: class UpdateData extends Data {
        // public string $name; } — no explicit constructor, which Spatie
        // Data supports natively.
        $body = [];
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $fallback = $this->exampleValueForType($property->getType(), $depth, $property->getName());
            $body[$property->getName()] = $this->exampleValueFromHints(
                $this->ruleHintsFor($property),
                $fallback
            );
        }

        return $body ?: null;
    }

    /**
     * Read Spatie Data validation attributes (e.g. #[Rule('date_format:Y-m-d H:i:s')],
     * #[Email], #[Uuid]) off a constructor parameter or property and return
     * their raw rule strings, so example values can match the expected
     * format instead of a generic empty placeholder.
     */
    protected function ruleHintsFor(\ReflectionParameter|\ReflectionProperty $member): array
    {
        $hints = [];

        foreach ($member->getAttributes() as $attribute) {
            if (!Str::startsWith($attribute->getName(), 'Spatie\\LaravelData\\Attributes\\')) {
                continue;
            }

            // Use the short attribute name too (e.g. "Email", "Uuid") since
            // some Spatie rule attributes carry no string arguments at all.
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

        // date_format rules in Laravel/Spatie use literal PHP date() tokens,
        // so we can feed the captured format straight into now()->format().
        // Captures up to the next hint separator (not \S+, which would stop
        // at the first space and truncate formats like "Y-m-d H:i:s").
        if (preg_match('/date_format:([^|]+)/', $combined, $m)) {
            try {
                return now()->format(trim($m[1]));
            } catch (\Throwable $e) {
                // malformed format string, fall through to generic handling
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
     * spurious 422s against required/min validation.
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
            Str::contains($key, 'age') => 25,
            Str::contains($key, ['quantity', 'qty', 'count']) => 1,
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

    /**
     * Fallback for controllers still using FormRequest validation rules
     * rather than Spatie Data.
     */
    protected function rulesToExampleBody(array $rules): array
    {
        $body = [];

        foreach ($rules as $field => $rule) {
            // Skip nested array rule keys like items.*.name for a flat top-level example
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
