<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/settings', Settings::class)->name('settings');
Route::get('/{conversationId?}', Chat::class)->name('chat');
// Route::get('/test', fn () => view('interface-test'))->name('test');
