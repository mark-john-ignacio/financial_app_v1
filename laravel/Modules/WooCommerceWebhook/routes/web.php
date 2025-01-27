<?php

use Illuminate\Support\Facades\Route;
use Modules\WooCommerceWebhook\Livewire\AssignProductMapping;
use Modules\WooCommerceWebhook\Livewire\Counter;
use Modules\WooCommerceWebhook\Livewire\Settings;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::group([], function () {
//     Route::resource('woocommercewebhook', WooCommerceWebhookController::class)->names('woocommercewebhook');
// });
Route::get('/counter-2', Counter::class);

Route::get('woocommerce/assign-product-mapping', AssignProductMapping::class);

Route::get('woocommerce/settings', Settings::class);
