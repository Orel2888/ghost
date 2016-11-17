<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\ExtendsLaravel\ExtendedResponseFactory;
use Carbon\Carbon;

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
        // Register app providers
        $this->app->register('App\Services\Tgbot\TgbotServiceProvider');

        //
        $this->app->extend('Illuminate\Contracts\Routing\ResponseFactory', function ($factory, $app) {
            return new ExtendedResponseFactory($app['Illuminate\Contracts\View\Factory'], $app['redirect']);
        });

        require app_path('Ghost/Support/helpers.php');

        // Localization date
        setlocale(LC_TIME, 'ru');

        Carbon::setLocale('ru');
    }
}
