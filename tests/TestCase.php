<?php

declare(strict_types=1);

namespace Tests;

use Database\Seeders\LocaleSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';
        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(LocaleSeeder::class);
    }
}
