<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

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
//        DB::listen(function ($query) {
//            echo $query->sql;
//            echo "\n\r";
//            print_r($query->bindings);
//            echo "\n\r";
//            $query->time;
//            echo "\n\r";
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
