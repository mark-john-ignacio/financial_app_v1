<?php

use Illuminate\Support\Facades\Route;
use Modules\SysMgmt\Http\Controllers\SysMgmtController;

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

Route::group([], function () {
    Route::resource('sysmgmt', SysMgmtController::class)->names('sysmgmt');
});
