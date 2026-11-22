<?php

use App\Http\Controllers\BitqueryController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/trades');

Route::get('/trades', [BitqueryController::class, 'trades'])->name('trades');
Route::get('/tokens', [BitqueryController::class, 'tokens'])->name('tokens');
Route::get('/wallet', [BitqueryController::class, 'wallet'])->name('wallet');
