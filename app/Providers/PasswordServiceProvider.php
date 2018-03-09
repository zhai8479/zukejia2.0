<?php

namespace App\Providers;

use App\Library\Password;
use Illuminate\Support\ServiceProvider;

class PasswordServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Password::class, function () {
            return new Password();
        });

        $this->app->bind('App\Library\Password', function () {
            return new Password();
        });
    }

    public function provides()
    {
        return [Password::class];
    }
}
