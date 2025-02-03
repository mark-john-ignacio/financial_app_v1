<?php

use Illuminate\Support\Facades\Route;
use Modules\SysMgmt\Http\Controllers\SysMgmtController;
use Modules\SysMgmt\Livewire\BirFormTable;

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
    // Route::resource('sysmgmt', SysMgmtController::class)->names('sysmgmt');

    Route::get('bir-forms', BirFormTable::class)->name('bir-forms.index');
    Route::get('bir-forms/data', [BirFormTable::class, 'getData'])->name('bir-forms.data');
});


