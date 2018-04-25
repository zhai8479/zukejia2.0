<?php

namespace App\Providers;

use App\Library\OSS;
use Illuminate\Support\ServiceProvider;

class OssServiceProvider extends ServiceProvider
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
        $this->app->singleton(OSS::class, function () {
            return new OSS();
        });

        $this->app->bind('App\Library\OSS', function () {
            return new OSS();
        });
    }

    public function provides()
    {
        return [OSS::class];
    }
}
