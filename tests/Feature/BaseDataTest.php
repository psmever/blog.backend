<?php

namespace Tests\Feature;

use Database\Seeders\CommonCodeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BaseDataTest extends TestCase
{
    use RefreshDatabase;

    private const CLIENT_TYPE = 'CT04P';

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CommonCodeSeeder::class);
    }

    public function test_base_data_returns_grouped_common_codes(): void
    {
        $response = $this->withHeader('Client-Type', self::CLIENT_TYPE)
            ->getJson('/api/v1/base-data')
            ->assertOk();

        /** @var array<string, array<int, array<string, mixed>>> $commonCodes */
        $commonCodes = $response->json('data.common_codes');

        $response
            ->assertJsonPath('status', true)
            ->assertJsonPath('data.app.env', 'testing');

        $this->assertSame('draft', $commonCodes['post.status'][0]['code'] ?? null);
        $this->assertSame('tech', $commonCodes['post.category'][0]['code'] ?? null);
        $this->assertSame(self::CLIENT_TYPE, $commonCodes['client.type'][3]['code'] ?? null);
    }
}
