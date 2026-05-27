<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomePageTest extends TestCase
{
    public function test_home_page_shows_api_server_message(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('blog.api server')
            ->assertSee('백엔드 API 서버입니다.');
    }
}
