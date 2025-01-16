<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\WooSecretKeyAuth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(WooSecretKeyAuth::class)->post('/woocommerce/webhook', function (Request $request) {
    return $request->all();
});