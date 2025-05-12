<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class YandexServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->app->make(Factory::class)->extend('yandex', function ($app) {
            $config = $app['config']['services.yandex'];
            
            return $app->make(Factory::class)->buildProvider(
                \SocialiteProviders\Yandex\Provider::class,
                $config
            );
        });
    }
}