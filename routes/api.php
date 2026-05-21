<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('orders')->group(function () {
    Route::get('/index', [OrderController::class, 'index']);
    Route::post('/store', [OrderController::class, 'store']);
    Route::get('/{id}', [OrderController::class, 'show']);
});


