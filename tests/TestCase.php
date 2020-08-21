<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseMigrations;

    protected function setUp() : void
    {
        parent::setUp();
        $this->artisan('db:seed', ['--database' => 'sqlite']);
        $this->artisan('passport:install');
    }
}
