<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('purpose', function ($value) {
            return "<?php
                if ($value == 1) {
                    echo 'On Payment';
                } elseif ($value == 2) {
                    echo 'Under Warranty';
                } elseif ($value == 3) {
                    echo 'Stock';
                }
            ?>";
        });
    }
}
