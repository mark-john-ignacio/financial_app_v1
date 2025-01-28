<?php

use Illuminate\Support\Facades\Route;
use Modules\WooCommerceWebhook\Http\Controllers\WooCommerceWebhookController;
use Modules\WooCommerceWebhook\Http\Middleware\WooSecretKeyAuth;
use Modules\WooCommerceWebhook\Livewire\AssignProductMapping;
use Modules\WooCommerceWebhook\Livewire\AuditList;
use Modules\WooCommerceWebhook\Livewire\Settings;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     Route::apiResource('woocommercewebhook', WooCommerceWebhookController::class)->names('woocommercewebhook');
// });

Route::post('/woocommerce/webhook', [WooCommerceWebhookController::class, 'handle']
)->middleware(WooSecretKeyAuth::class);

Route::delete('/woocommerce/webhook/delete-all', [WooCommerceWebhookController::class, 'deleteAll']);

Route::get('/woocommerce/mapping/data', [AssignProductMapping::class, 'getData'])
    ->name('woocommerce.mapping.data');

Route::get('/customers/search', [Settings::class, 'apiCustomersSearch'])
    ->name('customers.search');

Route::get('/woocommerce/audits/data', [AuditList::class, 'getData'])
    ->name('woocommerce.audits.data');
