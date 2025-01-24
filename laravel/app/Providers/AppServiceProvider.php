<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            require __DIR__ . '/../../../Connection/connection_string.php';
            $prefix = $laravel_prefix;

            Livewire::setUpdateRoute(function ($handle) use ($prefix) {
                return Route::post($prefix . '/livewire/update', $handle)
                    ->name('sanctum.livewire.update')
                    ->middleware('web');
            });

            Livewire::setScriptRoute(function ($handle) use ($prefix) {
                return Route::get($prefix . '/livewire/livewire.js', $handle)
                    ->name('sanctum.livewire.script')
                    ->middleware('web');
            });
//
//            // Add source maps route
//            Route::get($prefix . '/livewire/livewire.min.js.map', [
//                'uses' => '\Livewire\Features\SupportFileUploads\FilePreviewController@maps',
//                'as' => 'sanctum.livewire.maps'
//            ])->middleware('web');
        });
    }
}
