<?php

namespace App\Services\Postman;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Str;

class PostmanCollectionExporter
{
    private const BASE_URL_VARIABLE = 'blog_api_base_url';

    private const CLIENT_TYPE_VARIABLE = 'postman_client_type';

    private const ACCESS_TOKEN_VARIABLE = 'access_token';

    private const REFRESH_TOKEN_VARIABLE = 'refresh_token';

    private const ACCESS_TOKEN_EXPIRES_AT_VARIABLE = 'access_token_expires_at';

    private const REFRESH_TOKEN_EXPIRES_AT_VARIABLE = 'refresh_token_expires_at';

    private const USER_ID_VARIABLE = 'user_id';

    public function __construct(
        private readonly Router $router,
        private readonly InlineRequestValidationExtractor $validationExtractor
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function export(): array
    {
        return [
            'info' => [
                'name' => sprintf('%s API', config('app.name', 'Laravel')),
                'schema' => 'https://schema.getpostman.com/json/collection/v2.1.0/collection.json',
            ],
            'item' => $this->buildFolders(),
        ];
    }

    public function toJson(): string
    {
        return json_encode(
            $this->export(),
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function buildFolders(): array
    {
        $folders = [];

        foreach ($this->routesForExport() as $route) {
            $folderName = $this->resolveFolderName($route->uri());

            if (! array_key_exists($folderName, $folders)) {
                $folders[$folderName] = [
                    'name' => $folderName,
                    'item' => [],
                ];
            }

            $folders[$folderName]['item'][] = $this->buildRequestItem($route);
        }

        return array_values($folders);
    }

    /**
     * @return array<int, Route>
     */
    private function routesForExport(): array
    {
        $routes = array_values(array_filter(
            $this->router->getRoutes()->getRoutes(),
            fn (Route $route) => $this->shouldIncludeRoute($route)
        ));

        usort($routes, function (Route $left, Route $right) {
            return [$this->resolveFolderName($left->uri()), $left->uri(), $this->resolveMethod($left)]
                <=>
                [$this->resolveFolderName($right->uri()), $right->uri(), $this->resolveMethod($right)];
        });

        return $routes;
    }

    private function shouldIncludeRoute(Route $route): bool
    {
        $uri = $route->uri();

        if (! Str::startsWith($uri, 'api/')) {
            return false;
        }

        if (Str::startsWith($uri, 'api/_demo/')) {
            return false;
        }

        return true;
    }

    private function resolveFolderName(string $uri): string
    {
        $segments = explode('/', trim($uri, '/'));
        $root = $segments[1] ?? 'api';

        if ($root === 'v1' && isset($segments[2])) {
            return sprintf('v1/%s', $segments[2]);
        }

        return $root;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildRequestItem(Route $route): array
    {
        $method = $this->resolveMethod($route);
        $uri = $route->uri();
        $validation = $this->validationForRoute($route);
        $normalizedPath = implode('/', array_map(
            fn (string $segment) => $this->normalizePathSegment($segment),
            explode('/', trim($uri, '/'))
        ));

        $item = [
            'name' => sprintf('%s /%s', $method, $normalizedPath),
            'request' => $this->buildRequest($route, $method, $uri, $validation),
            'response' => [],
        ];

        $event = $this->buildEvent($method, $uri);

        if ($event !== null) {
            $item['event'] = [$event];
        }

        return $item;
    }

    private function resolveMethod(Route $route): string
    {
        $methods = array_values(array_filter(
            $route->methods(),
            fn (string $method) => $method !== 'HEAD'
        ));

        return $methods[0] ?? 'GET';
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     * @return array<string, mixed>
     */
    private function buildRequest(Route $route, string $method, string $uri, array $validation): array
    {
        $body = $this->buildBody($method, $validation);
        $headers = [
            ['key' => 'Accept', 'value' => 'application/json', 'type' => 'text'],
            ['key' => 'Client-Type', 'value' => sprintf('{{%s}}', self::CLIENT_TYPE_VARIABLE), 'type' => 'text'],
        ];

        if (($body['mode'] ?? null) === 'raw') {
            $headers[] = ['key' => 'Content-Type', 'value' => 'application/json', 'type' => 'text'];
        }

        $request = [
            'method' => $method,
            'header' => $headers,
            'url' => $this->buildUrl($method, $uri, $validation),
        ];

        if ($this->requiresBearerAuth($route)) {
            $request['auth'] = [
                'type' => 'bearer',
                'bearer' => [
                    ['key' => 'token', 'value' => sprintf('{{%s}}', self::ACCESS_TOKEN_VARIABLE), 'type' => 'string'],
                ],
            ];
        }

        if ($body !== null) {
            $request['body'] = $body;
        }

        return $request;
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     * @return array<string, mixed>
     */
    private function buildUrl(string $method, string $uri, array $validation): array
    {
        $originalSegments = explode('/', trim($uri, '/'));
        $pathSegments = array_map(
            fn (string $segment) => $this->normalizePathSegment($segment),
            $originalSegments
        );

        $query = $method === 'GET'
            ? $this->buildQueryParameters($validation)
            : [];

        $raw = sprintf('{{%s}}/', self::BASE_URL_VARIABLE).implode('/', $pathSegments);

        if ($query !== []) {
            $raw .= '?'.http_build_query(
                collect($query)
                    ->mapWithKeys(fn (array $parameter) => [$parameter['key'] => $parameter['value']])
                    ->all()
            );
        }

        $url = [
            'raw' => $raw,
            'host' => [sprintf('{{%s}}', self::BASE_URL_VARIABLE)],
            'path' => $pathSegments,
        ];

        if ($query !== []) {
            $url['query'] = $query;
        }

        $variables = $this->buildPathVariables($originalSegments);

        if ($variables !== []) {
            $url['variable'] = $variables;
        }

        return $url;
    }

    private function normalizePathSegment(string $segment): string
    {
        if (! preg_match('/^\{(.+)\}$/', $segment, $matches)) {
            return $segment;
        }

        return ':'.$this->postmanVariableName($matches[1]);
    }

    /**
     * @param  array<int, string>  $segments
     * @return array<int, array<string, string>>
     */
    private function buildPathVariables(array $segments): array
    {
        $variables = [];

        foreach ($segments as $segment) {
            if (! preg_match('/^\{(.+)\}$/', $segment, $matches)) {
                continue;
            }

            $name = $this->postmanVariableName($matches[1]);

            $variables[] = [
                'key' => $name,
                'value' => sprintf('{{%s}}', $name),
            ];
        }

        return $variables;
    }

    private function postmanVariableName(string $parameter): string
    {
        if ($parameter === 'uuid') {
            return 'postUuid';
        }

        return Str::camel($parameter);
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     * @return array<int, array<string, string>>
     */
    private function buildQueryParameters(array $validation): array
    {
        $query = [];

        foreach ($validation as $field => $metadata) {
            if ($this->isWildcardField($field)) {
                continue;
            }

            $sample = $this->sampleValue($field, $metadata, $validation);

            $query[] = [
                'key' => $field,
                'value' => $this->stringifyValue($sample),
            ];
        }

        return $query;
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     * @return array<string, mixed>|null
     */
    private function buildBody(string $method, array $validation): ?array
    {
        if (! in_array($method, ['POST', 'PUT', 'PATCH'], true) || $validation === []) {
            return null;
        }

        if ($this->containsFileUpload($validation)) {
            $formData = [];

            foreach ($validation as $field => $metadata) {
                if ($this->isWildcardField($field)) {
                    continue;
                }

                if ($this->fieldType($metadata) === 'image') {
                    $formData[] = [
                        'key' => $field,
                        'type' => 'file',
                        'src' => '',
                    ];

                    continue;
                }

                $formData[] = [
                    'key' => $field,
                    'type' => 'text',
                    'value' => $this->stringifyValue($this->sampleValue($field, $metadata, $validation)),
                ];
            }

            return [
                'mode' => 'formdata',
                'formdata' => $formData,
            ];
        }

        $payload = [];

        foreach ($validation as $field => $metadata) {
            if ($this->isWildcardField($field)) {
                continue;
            }

            $payload[$field] = $this->sampleValue($field, $metadata, $validation);
        }

        return [
            'mode' => 'raw',
            'raw' => json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'options' => [
                'raw' => [
                    'language' => 'json',
                ],
            ],
        ];
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     */
    private function containsFileUpload(array $validation): bool
    {
        foreach ($validation as $metadata) {
            if ($this->fieldType($metadata) === 'image') {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array{rules: array<int, string>, source: string}  $metadata
     */
    private function fieldType(array $metadata): string
    {
        if (in_array('image', $metadata['rules'], true)) {
            return 'image';
        }

        if (in_array('array', $metadata['rules'], true)) {
            return 'array';
        }

        if (in_array('integer', $metadata['rules'], true)) {
            return 'integer';
        }

        return 'string';
    }

    private function isWildcardField(string $field): bool
    {
        return str_contains($field, '.*');
    }

    /**
     * @param  array{rules: array<int, string>, source: string}  $metadata
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     */
    private function sampleValue(string $field, array $metadata, array $validation): mixed
    {
        if ($field === 'email') {
            return '{{user_email}}';
        }

        if ($field === 'password') {
            return '{{user_password}}';
        }

        if ($field === 'refresh_token') {
            return sprintf('{{%s}}', self::REFRESH_TOKEN_VARIABLE);
        }

        if ($field === 'uuid') {
            return '{{postUuid}}';
        }

        return match ($this->fieldType($metadata)) {
            'array' => $this->arraySample($field, $validation),
            'integer' => 1,
            default => $this->stringSample($field, $metadata),
        };
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     * @return array<int, string>
     */
    private function arraySample(string $field, array $validation): array
    {
        if (! $this->hasWildcardChildren($field, $validation)) {
            return [];
        }

        if ($field === 'tags') {
            return ['샘플태그'];
        }

        return ['샘플항목'];
    }

    /**
     * @param  array{rules: array<int, string>, source: string}  $metadata
     */
    private function stringSample(string $field, array $metadata): string
    {
        foreach ($metadata['rules'] as $rule) {
            if (str_starts_with($rule, 'in:') && strlen($rule) > 3) {
                $options = array_values(array_filter(explode(',', substr($rule, 3))));

                if ($options !== []) {
                    return $options[0];
                }
            }
        }

        if ($field === 'status' && str_contains($metadata['source'], 'STATUS_PUBLISHED')) {
            return 'published';
        }

        return match ($field) {
            'title' => '샘플 게시글 제목',
            'body', 'content' => '샘플 게시글 내용입니다.',
            default => '샘플 텍스트',
        };
    }

    /**
     * @param  array<string, array{rules: array<int, string>, source: string}>  $validation
     */
    private function hasWildcardChildren(string $field, array $validation): bool
    {
        return array_key_exists($field.'.*', $validation);
    }

    private function stringifyValue(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        }

        if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        return (string) $value;
    }

    private function requiresBearerAuth(Route $route): bool
    {
        foreach ($route->gatherMiddleware() as $middleware) {
            if (str_contains($middleware, 'sanctum')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array{listen: string, script: array{type: string, exec: array<int, string>}}|null
     */
    private function buildEvent(string $method, string $uri): ?array
    {
        if ($method !== 'POST' || ! in_array($uri, ['api/auth/login', 'api/auth/refresh'], true)) {
            return null;
        }

        return [
            'listen' => 'test',
            'script' => [
                'type' => 'text/javascript',
                'exec' => [
                    'try {',
                    '    const json = pm.response.json();',
                    '    if (json && json.data) {',
                    '        if (json.data.access_token) {',
                    sprintf('            pm.globals.set("%s", json.data.access_token);', self::ACCESS_TOKEN_VARIABLE),
                    '        }',
                    '        if (json.data.refresh_token) {',
                    sprintf('            pm.globals.set("%s", json.data.refresh_token);', self::REFRESH_TOKEN_VARIABLE),
                    '        }',
                    '        if (json.data.access_token_expires_at) {',
                    sprintf('            pm.globals.set("%s", json.data.access_token_expires_at);', self::ACCESS_TOKEN_EXPIRES_AT_VARIABLE),
                    '        }',
                    '        if (json.data.refresh_token_expires_at) {',
                    sprintf('            pm.globals.set("%s", json.data.refresh_token_expires_at);', self::REFRESH_TOKEN_EXPIRES_AT_VARIABLE),
                    '        }',
                    '        if (json.data.user && json.data.user.id !== undefined) {',
                    sprintf('            pm.environment.set("%s", String(json.data.user.id));', self::USER_ID_VARIABLE),
                    '        }',
                    '    }',
                    '} catch (error) {',
                    '    console.warn("Unable to store auth tokens from response", error);',
                    '}',
                ],
            ],
        ];
    }

    /**
     * @return array<string, array{rules: array<int, string>, source: string}>
     */
    private function validationForRoute(Route $route): array
    {
        $action = $route->getActionName();

        if (! str_contains($action, '@')) {
            return [];
        }

        [$controllerClass, $method] = explode('@', $action, 2);

        return $this->validationExtractor->forAction($controllerClass, $method);
    }
}
