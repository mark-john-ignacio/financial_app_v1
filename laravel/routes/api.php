<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\WooSecretKeyAuth;
use App\Http\Controllers\WooCommerceWebhookController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/woocommerce/webhook', [WooCommerceWebhookController::class, 'handle']
)->middleware(WooSecretKeyAuth::class);

Route::delete('/woocommerce/webhook/delete-all', [WooCommerceWebhookController::class, 'deleteAll']);
