<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class PostmanExportCommandTest extends TestCase
{
    private string $defaultOutput;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.url' => 'https://api.test.local',
            'app.name' => 'blogBackend',
        ]);

        $this->defaultOutput = storage_path('app/postman/blog-backend.postman_collection.json');
        $this->deleteIfExists($this->defaultOutput);
    }

    protected function tearDown(): void
    {
        $this->deleteIfExists($this->defaultOutput);

        parent::tearDown();
    }

    public function test_export_command_creates_default_postman_collection_file(): void
    {
        $this->artisan('postman:export')
            ->expectsOutput(sprintf('Postman collection exported: %s', $this->defaultOutput))
            ->assertExitCode(0);

        $this->assertFileExists($this->defaultOutput);

        $collection = $this->readCollection($this->defaultOutput);

        $this->assertSame('blogBackend API', $collection['info']['name']);
        $this->assertSame('https://schema.getpostman.com/json/collection/v2.1.0/collection.json', $collection['info']['schema']);
        $this->assertArrayNotHasKey('variable', $collection);
    }

    public function test_export_command_supports_custom_output_and_generates_expected_requests(): void
    {
        $output = storage_path('framework/testing/postman/custom-blog-collection.json');
        $this->deleteIfExists($output);

        $this->artisan('postman:export', ['--output' => $output])->assertExitCode(0);

        $this->assertFileExists($output);

        $collection = $this->readCollection($output);
        $requests = $this->flattenRequests($collection['item']);
        $requestMap = collect($requests)->keyBy('name');
        $folders = collect($collection['item'])->pluck('name')->all();

        $this->assertContains('auth', $folders);
        $this->assertContains('health', $folders);
        $this->assertContains('v1/posts', $folders);
        $this->assertContains('v1/public', $folders);
        $this->assertContains('v1/base-data', $folders);
        $this->assertArrayNotHasKey('GET /api/_demo/ok', $requestMap->all());

        $login = $requestMap->get('POST /api/auth/login');
        $postsIndex = $requestMap->get('GET /api/v1/posts');
        $publicPostsIndex = $requestMap->get('GET /api/v1/public/posts');
        $publicPostShow = $requestMap->get('GET /api/v1/public/posts/:slug');
        $uploadImage = $requestMap->get('POST /api/v1/posts/:postUuid/images');
        $showPost = $requestMap->get('GET /api/v1/posts/:postUuid');

        $this->assertNotNull($login);
        $this->assertNotNull($postsIndex);
        $this->assertNotNull($publicPostsIndex);
        $this->assertNotNull($publicPostShow);
        $this->assertNotNull($uploadImage);
        $this->assertNotNull($showPost);

        $this->assertSame('{{blog_api_base_url}}/api/auth/login', $login['request']['url']['raw']);
        $this->assertSame('{{blog_api_base_url}}', $login['request']['url']['host'][0]);
        $this->assertSame('application/json', $this->findHeader($login['request']['header'], 'Content-Type'));
        $this->assertSame('{{user_email}}', $this->decodeRawBody($login['request']['body'])['email']);
        $this->assertSame('{{user_password}}', $this->decodeRawBody($login['request']['body'])['password']);
        $this->assertSame('test', $login['event'][0]['listen']);
        $this->assertStringContainsString(
            'pm.globals.set("access_token", json.data.access_token);',
            implode("\n", $login['event'][0]['script']['exec'])
        );
        $this->assertStringContainsString(
            'pm.globals.set("refresh_token", json.data.refresh_token);',
            implode("\n", $login['event'][0]['script']['exec'])
        );
        $this->assertStringContainsString(
            'pm.globals.set("access_token_expires_at", json.data.access_token_expires_at);',
            implode("\n", $login['event'][0]['script']['exec'])
        );
        $this->assertStringContainsString(
            'pm.globals.set("refresh_token_expires_at", json.data.refresh_token_expires_at);',
            implode("\n", $login['event'][0]['script']['exec'])
        );
        $this->assertStringContainsString(
            'pm.environment.set("user_id", String(json.data.user.id));',
            implode("\n", $login['event'][0]['script']['exec'])
        );

        $this->assertSame('{{postman_client_type}}', $this->findHeader($showPost['request']['header'], 'Client-Type'));
        $this->assertSame('{{access_token}}', $showPost['request']['auth']['bearer'][0]['value']);
        $this->assertSame('{{blog_api_base_url}}/api/v1/posts/:postUuid', $showPost['request']['url']['raw']);
        $this->assertSame('{{blog_api_base_url}}', $showPost['request']['url']['host'][0]);
        $this->assertSame('{{postUuid}}', $showPost['request']['url']['variable'][0]['value']);

        $this->assertSame('published', $this->findQuery($postsIndex['request']['url']['query'], 'status'));
        $this->assertSame('1', $this->findQuery($postsIndex['request']['url']['query'], 'limit'));
        $this->assertSame('1', $this->findQuery($publicPostsIndex['request']['url']['query'], 'limit'));
        $this->assertSame('샘플 텍스트', $this->findQuery($publicPostsIndex['request']['url']['query'], 'cursor'));
        $this->assertArrayNotHasKey('auth', $publicPostsIndex['request']);
        $this->assertArrayNotHasKey('auth', $publicPostShow['request']);
        $this->assertSame('{{blog_api_base_url}}/api/v1/public/posts/:slug', $publicPostShow['request']['url']['raw']);
        $this->assertSame('{{slug}}', $publicPostShow['request']['url']['variable'][0]['value']);

        $this->assertSame('formdata', $uploadImage['request']['body']['mode']);
        $this->assertSame('file', $uploadImage['request']['body']['formdata'][0]['type']);
        $this->assertSame('image', $uploadImage['request']['body']['formdata'][0]['key']);

        $postCreate = $requestMap->get('POST /api/v1/posts');
        $this->assertSame('{{postUuid}}', $this->decodeRawBody($postCreate['request']['body'])['uuid']);
        $this->assertSame('샘플 게시글 제목', $this->decodeRawBody($postCreate['request']['body'])['title']);
        $this->assertSame(['샘플태그'], $this->decodeRawBody($postCreate['request']['body'])['tags']);
        $this->assertSame('샘플 게시글 내용입니다.', $this->decodeRawBody($postCreate['request']['body'])['body']);

        $this->deleteIfExists($output);
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    private function flattenRequests(array $items): array
    {
        $requests = [];

        foreach ($items as $item) {
            if (isset($item['request'])) {
                $requests[] = $item;

                continue;
            }

            if (isset($item['item']) && is_array($item['item'])) {
                $requests = [...$requests, ...$this->flattenRequests($item['item'])];
            }
        }

        return $requests;
    }

    /**
     * @return array<string, mixed>
     */
    private function readCollection(string $path): array
    {
        return json_decode((string) File::get($path), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<string, mixed>  $collection
     */
    private function findVariable(array $collection, string $key): ?string
    {
        foreach ($collection['variable'] as $variable) {
            if (($variable['key'] ?? null) === $key) {
                return $variable['value'] ?? null;
            }
        }

        return null;
    }

    /**
     * @param  array<int, array<string, mixed>>  $headers
     */
    private function findHeader(array $headers, string $key): ?string
    {
        foreach ($headers as $header) {
            if (($header['key'] ?? null) === $key) {
                return $header['value'] ?? null;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $body
     * @return array<string, mixed>
     */
    private function decodeRawBody(array $body): array
    {
        return json_decode((string) ($body['raw'] ?? '{}'), true, 512, JSON_THROW_ON_ERROR);
    }

    /**
     * @param  array<int, array<string, mixed>>  $query
     */
    private function findQuery(array $query, string $key): ?string
    {
        foreach ($query as $parameter) {
            if (($parameter['key'] ?? null) === $key) {
                return $parameter['value'] ?? null;
            }
        }

        return null;
    }

    private function deleteIfExists(string $path): void
    {
        if (File::exists($path)) {
            File::delete($path);
        }

        $directory = dirname($path);

        if ($directory !== '' && File::isDirectory($directory) && File::glob($directory.'/*') === []) {
            File::deleteDirectory($directory);
        }
    }
}
