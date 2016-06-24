<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\ExtendsLaravel\ExtendedResponseFactory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->app->extend('Illuminate\Contracts\Routing\ResponseFactory', function ($factory, $app) {
            return new ExtendedResponseFactory($app['Illuminate\Contracts\View\Factory'], $app['redirect']);
        });
    }
}
