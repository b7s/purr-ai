<?php

declare(strict_types=1);

use App\Livewire\Chat;
use Illuminate\Support\Facades\Route;

Route::get('/', Chat::class)->name('chat');
Route::get('/test', fn () => view('interface-test'))->name('test');
