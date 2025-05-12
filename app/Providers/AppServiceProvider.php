<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use App\Validators\BranchValidator;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        App::setLocale('ru'); 
        

        Validator::resolver(function($translator, $data, $rules, $messages) {
            return new BranchValidator($translator, $data, $rules, $messages);
        });
    }
}