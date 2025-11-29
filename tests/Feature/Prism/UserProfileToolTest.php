<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\Prism\Tools\UserProfileTool;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function (): void {
    cache()->flush();
});

it('gets user profile with all fields', function (): void {
    Setting::set('user_name', 'John Doe');
    Setting::set('user_description', 'Software developer');
    Setting::set('mascot_name', 'TestCat');
    Setting::set('response_detail', 'detailed');
    Setting::set('response_tone', 'professional');
    Setting::set('timezone', 'America/New_York');

    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->getProfile(), true);

    expect($result['success'])->toBeTrue()
        ->and($result['profile']['user_name'])->toBe('John Doe')
        ->and($result['profile']['user_description'])->toBe('Software developer')
        ->and($result['profile']['mascot_name'])->toBe('TestCat')
        ->and($result['profile']['response_detail'])->toBe('detailed')
        ->and($result['profile']['response_tone'])->toBe('professional')
        ->and($result['profile']['timezone'])->toBe('America/New_York')
        ->and($result['missing_fields'])->toBeEmpty();
});

it('identifies missing profile fields', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->getProfile(), true);

    expect($result['success'])->toBeTrue()
        ->and($result['missing_fields'])->not->toBeEmpty();
});

it('updates user name', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile('Jane Doe', null, null, null, null), true);

    expect($result['success'])->toBeTrue();

    cache()->flush();
    expect(Setting::get('user_name'))->toBe('Jane Doe');
});

it('updates user description', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, 'I love coding', null, null, null), true);

    expect($result['success'])->toBeTrue();

    cache()->flush();
    expect(Setting::get('user_description'))->toBe('I love coding');
});

it('validates response detail', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, null, 'invalid', null, null), true);

    expect($result['success'])->toBeFalse()
        ->and($result['errors'])->not->toBeEmpty();
});

it('validates response tone', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, null, null, 'invalid_tone', null), true);

    expect($result['success'])->toBeFalse()
        ->and($result['errors'])->not->toBeEmpty();
});

it('validates timezone', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, null, null, null, 'Invalid/Timezone'), true);

    expect($result['success'])->toBeFalse()
        ->and($result['errors'])->not->toBeEmpty();
});

it('updates valid timezone', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, null, null, null, 'Europe/London'), true);

    expect($result['success'])->toBeTrue();

    cache()->flush();
    expect(Setting::get('timezone'))->toBe('Europe/London');
});

it('rejects name that is too long', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $longName = str_repeat('a', 101);
    $result = json_decode($handler->updateProfile($longName, null, null, null, null), true);

    expect($result['success'])->toBeFalse()
        ->and($result['errors'])->not->toBeEmpty();
});

it('returns error when no updates provided', function (): void {
    $handler = new \App\Services\Prism\Tools\UserProfileToolHandler;
    $result = json_decode($handler->updateProfile(null, null, null, null, null), true);

    expect($result['success'])->toBeFalse();
});

it('creates tool instance', function (): void {
    $tool = UserProfileTool::make();

    expect($tool)->toBeInstanceOf(\Prism\Prism\Tool::class);
});
