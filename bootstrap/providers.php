<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\SocialiteServiceProvider::class,
    App\Providers\VKontakteServiceProvider::class,
    Laravel\Socialite\SocialiteServiceProvider::class,
    SocialiteProviders\Manager\ServiceProvider::class,
    App\Providers\YandexServiceProvider::class,
];
