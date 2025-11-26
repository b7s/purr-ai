<?php

declare(strict_types=1);

use App\Livewire\Chat;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::get('/settings', Settings::class)->name('settings');
Route::get('/menubar/{conversationId?}', Chat::class)->name('menubar.chat');
Route::get('/{conversationId?}', Chat::class)->name('chat');
