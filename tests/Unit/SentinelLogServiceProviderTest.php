<?php

namespace Tests\Unit;

use Harryes\SentinelLog\SentinelLogServiceProvider;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

beforeEach(function () {
    $this->app->register(SentinelLogServiceProvider::class);
});

test('config is properly merged', function () {
    $config = config('sentinel-log');

    expect($config)->toBeArray()
        ->and($config)->toHaveKey('enabled')
        ->and($config)->toHaveKey('events')
        ->and($config)->toHaveKey('table_name')
        ->and($config['table_name'])->toBe('authentication_logs');
});

test('migrations are loaded', function () {
    $migrationPath = __DIR__ . '/../../database/migrations';

    expect(file_exists($migrationPath))->toBeTrue()
        ->and(collect(glob($migrationPath . '/*.php'))
            ->filter(fn ($file) => str_contains($file, 'create_authentication_logs_table.php'))
            ->isNotEmpty()
        )->toBeTrue();
});
