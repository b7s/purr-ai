<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/', Chat::class)->name('chat');
Route::get('/settings', Settings::class)->name('settings');
Route::get('/test', fn () => view('interface-test'))->name('test');

Route::get('/api/settings/window-opacity', function () {
    return response()->json([
        'opacity' => (int) \App\Models\Setting::get('window_opacity', 85),
    ]);
});
