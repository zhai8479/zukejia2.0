<?php

namespace App\Providers;

use App\Library\OSSHelp;
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
        $this->app->singleton(OSSHelp::class, function () {
            return new OSSHelp();
        });

        $this->app->bind('App\Library\OSS', function () {
            return new OSSHelp();
        });
    }

    public function provides()
    {
        return [OSSHelp::class];
    }
}
