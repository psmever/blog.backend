<?php

namespace Tests\Feature;

use Tests\TestCase;

class ClientTypeValidationTest extends TestCase
{
    public function test_api_request_accepts_active_client_type_from_config_without_database_lookup(): void
    {
        $this->withHeader('Client-Type', 'CT03Z')
            ->getJson('/api/health')
            ->assertOk();
    }

    public function test_api_request_rejects_unknown_client_type(): void
    {
        $this->withHeader('Client-Type', 'UNKNOWN')
            ->getJson('/api/health')
            ->assertStatus(400)
            ->assertJsonPath('message', 'Client-Type 헤더 값이 올바르지 않습니다.');
    }
}
