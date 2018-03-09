<?php

namespace App\Providers;

use App\Library\sms\Sms;
use Illuminate\Support\ServiceProvider;

class AliyunSmsServiceProvider extends ServiceProvider
{
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sms::class, function () {
            return new Sms(config('aliyunsms'));
        });

        $this->app->bind('App\Library\sms\Sms', function () {
            return new Sms(config('aliyunsms'));
        });
    }

    public function provides()
    {
        return [Sms::class];
    }
}
