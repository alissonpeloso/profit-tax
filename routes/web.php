<?php

use App\Http\Controllers\ApiTokenController;
use App\Http\Controllers\BrokerController;
use App\Http\Controllers\DarfController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StockTradeController;
use App\Http\Controllers\WelcomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

Route::group(['middleware' => ['auth:sanctum', config('jetstream.auth_session'), 'verified']], function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.show');
    Route::get('/api-tokens', [ApiTokenController::class, 'index'])->name('api-tokens.index');

    Route::get('/trades', [StockTradeController::class, 'index'])->name('trades');

    Route::get('/darfs', [DarfController::class, 'index'])->name('darfs');

    Route::get('/brokers/search', [BrokerController::class, 'search'])->name('brokers.search');
});
