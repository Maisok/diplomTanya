<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class VKontakteServiceProvider extends ServiceProvider
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
    public function boot()
    {
        $socialite = $this->app->make('Laravel\Socialite\Contracts\Factory');
        $socialite->extend('vkontakte', function () use ($socialite) {
            $config = config('services.vkontakte');
            return $socialite->buildProvider(
                \SocialiteProviders\VKontakte\Provider::class,
                $config
            );
        });
    }
}
