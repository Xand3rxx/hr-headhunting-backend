<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Check if the app is in local environment
        if ((env('APP_ENV') !== 'local')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        header('X-Powered-By:');
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Illuminate\Pagination\Paginator::useBootstrap();
    }
}
