<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;
use App\Http\Middleware\ValidateApiToken;


Route::middleware([ValidateApiToken::class])->group(function () {
    Route::post('/transaction', [TransactionController::class, 'createTransaction']);
    Route::get('/balance', [TransactionController::class, 'getBalance']);
});
