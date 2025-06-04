<?php

use Harryes\SentinelLog\Models\AuthenticationLog;
use Harryes\SentinelLog\Models\SentinelSession;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

test('model uses correct table name from config', function () {
    $model = new AuthenticationLog;

    // Default table name
    expect($model->getTable())->toBe('authentication_logs');

    // Custom table name
    config(['sentinel-log.table_name' => 'custom_auth_logs']);
    expect($model->getTable())->toBe('custom_auth_logs');
});

test('model has correct fillable attributes', function () {
    $model = new AuthenticationLog;

    $expectedFillable = [
        'authenticatable_id',
        'authenticatable_type',
        'session_id',
        'event_name',
        'ip_address',
        'user_agent',
        'device_info',
        'location',
        'is_successful',
        'event_at',
        'cleared_at',
    ];

    expect($model->getFillable())
        ->toBeArray()
        ->toEqual($expectedFillable);
});

test('model has correct cast attributes', function () {
    $model = new AuthenticationLog;

    $expectedCasts = [
        'device_info' => 'array',
        'location' => 'array',
        'is_successful' => 'boolean',
        'event_at' => 'datetime',
        'cleared_at' => 'datetime',
    ];

    expect($model->getCasts())
        ->toBeArray()
        ->toMatchArray($expectedCasts);
});

test('model attributes can be set', function () {
    $model = new AuthenticationLog;

    $data = [
        'event_name' => 'login',
        'ip_address' => '127.0.0.1',
        'user_agent' => 'PHPUnit Test',
        'device_info' => ['browser' => 'Test Browser'],
        'location' => ['country' => 'Test Country'],
        'is_successful' => true,
    ];

    $model->fill($data);

    expect($model)
        ->event_name->toBe('login')
        ->ip_address->toBe('127.0.0.1')
        ->user_agent->toBe('PHPUnit Test')
        ->device_info->toBe(['browser' => 'Test Browser'])
        ->location->toBe(['country' => 'Test Country'])
        ->is_successful->toBeTrue();
});

test('model has correct relationship methods', function () {
    $model = new AuthenticationLog;

    // Test authenticatable relationship
    expect($model->authenticatable())
        ->toBeInstanceOf(MorphTo::class);

    // Test session relationship
    $sessionRelation = $model->session();

    expect($sessionRelation)
        ->toBeInstanceOf(BelongsTo::class)
        ->and($sessionRelation->getRelated())->toBeInstanceOf(SentinelSession::class)
        ->and($sessionRelation->getForeignKeyName())->toBe('session_id');
});

test('model handles json attributes correctly', function () {
    $model = new AuthenticationLog;

    // Test device_info JSON handling
    $deviceInfo = [
        'browser' => 'Chrome',
        'version' => '100.0',
        'platform' => 'Windows',
    ];
    $model->device_info = $deviceInfo;

    // Test location JSON handling
    $location = [
        'country' => 'United States',
        'city' => 'New York',
        'lat' => 40.7128,
        'lon' => -74.0060,
    ];
    $model->location = $location;

    expect($model)
        ->device_info->toBe($deviceInfo)
        ->and($model->device_info)->toBeArray()
        ->and($model->location)->toBe($location)
        ->and($model->location)->toBeArray();
});

test('model handles datetime attributes correctly', function () {
    $model = new AuthenticationLog;

    $now = now();
    $model->event_at = $now;
    $model->cleared_at = $now;

    expect($model)
        ->event_at->toBeInstanceOf(Illuminate\Support\Carbon::class)
        ->and($model->event_at->timestamp)->toBe($now->timestamp)
        ->and($model->cleared_at)->toBeInstanceOf(Illuminate\Support\Carbon::class)
        ->and($model->cleared_at->timestamp)->toBe($now->timestamp);
});
