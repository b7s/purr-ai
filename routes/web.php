<?php

declare(strict_types=1);

use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\TranscribeController;
use App\Http\Controllers\Api\ValidateSpeechConfigController;
use App\Livewire\Chat;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::post('/api/transcribe', TranscribeController::class)->name('api.transcribe');
Route::get('/api/validate-speech-config', ValidateSpeechConfigController::class)->name('api.validate-speech-config');
Route::post('/api/settings', [SettingsController::class, 'store'])->name('api.settings.store');
Route::get('/api/settings/{key}', [SettingsController::class, 'show'])->name('api.settings.show');

Route::get('/settings', Settings::class)->name('settings');
Route::get('/menubar/{conversationId?}', Chat::class)->name('menubar.chat');
Route::get('/{conversationId?}', Chat::class)->name('chat');
