<?php

declare(strict_types=1);

use App\Models\Setting;
use Illuminate\Support\Facades\App;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    Setting::query()->where('key', 'response_language')->delete();
    App::setLocale(config('app.locale'));
});

it('uses saved response_language when available', function () {
    Setting::set('response_language', 'pt_PT');

    app(\App\Services\LocaleService::class)->configure();

    expect(App::getLocale())->toBe('pt_PT');
});

it('detects locale from browser preferences when not saved', function () {
    $request = \Illuminate\Http\Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'es-ES,es;q=0.9,en;q=0.8',
    ]);
    app()->instance('request', $request);

    app(\App\Services\LocaleService::class)->configure();

    $savedLocale = Setting::get('response_language');
    expect($savedLocale)->toBeIn(['es_ES', 'en_US']);
});

it('falls back to default locale when no match found', function () {
    $request = \Illuminate\Http\Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9',
    ]);
    app()->instance('request', $request);

    app(\App\Services\LocaleService::class)->configure();

    $locale = App::getLocale();
    expect($locale)->toBe(config('app.locale'));
});

it('matches partial language codes', function () {
    $request = \Illuminate\Http\Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'en,en-US;q=0.9',
    ]);
    app()->instance('request', $request);

    app(\App\Services\LocaleService::class)->configure();

    $savedLocale = Setting::get('response_language');
    expect($savedLocale)->toBeString();
    expect(str_starts_with($savedLocale, 'en'))->toBeTrue();
});

it('normalizes language codes with hyphens to underscores', function () {
    $request = \Illuminate\Http\Request::create('/', 'GET', [], [], [], [
        'HTTP_ACCEPT_LANGUAGE' => 'pt-BR,pt;q=0.9',
    ]);
    app()->instance('request', $request);

    app(\App\Services\LocaleService::class)->configure();

    $savedLocale = Setting::get('response_language');
    expect($savedLocale)->toBeIn(['pt_PT', 'pt_BR', 'en_US']);
});
