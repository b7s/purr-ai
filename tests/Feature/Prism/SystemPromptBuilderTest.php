<?php

declare(strict_types=1);

use App\Models\Setting;
use App\Services\Prism\SystemPromptBuilder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('builds system prompt with mascot name from settings', function (): void {
    Setting::set('mascot_name', 'PurrBot');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('PurrBot');
});

it('uses app name when mascot name is empty', function (): void {
    Setting::set('mascot_name', '');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain(config('app.name'));
});

it('includes user name when set', function (): void {
    Setting::set('user_name', 'John');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('John');
});

it('includes user description when set', function (): void {
    Setting::set('user_description', 'Loves programming and coffee');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('Loves programming and coffee');
});

it('includes cat personality when respond_as_cat is true', function (): void {
    Setting::set('respond_as_cat', true);

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('cat');
    expect($prompt)->toContain('meow');
});

it('does not include cat personality when respond_as_cat is false', function (): void {
    Setting::set('respond_as_cat', false);

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->not->toContain('meow');
});

it('includes response detail setting', function (): void {
    Setting::set('response_detail', 'short');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('concise');
});

it('includes response tone setting', function (): void {
    Setting::set('response_tone', 'professional');

    $builder = new SystemPromptBuilder;
    $prompt = $builder->build();

    expect($prompt)->toContain('Professional');
});
