<?php

namespace App\Providers;

use App\Repositories\SignUpRepository;
use App\Repositories\SignUpRepositoryEloquent;
use App\Repositories\UserIntegralLogRepository;
use App\Repositories\UserIntegralLogRepositoryEloquent;
use App\Repositories\UserIntegralRepository;
use App\Repositories\UserIntegralRepositoryEloquent;
use App\Repositories\UserVoucherRepository;
use App\Repositories\UserVoucherRepositoryEloquent;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
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
        $this->app->bind(\App\Repositories\UserMoneyRepository::class, \App\Repositories\UserMoneyRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserMoneyLogRepository::class, \App\Repositories\UserMoneyLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApartmentRepository::class, \App\Repositories\ApartmentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderInvoiceLogRepository::class, \App\Repositories\OrderInvoiceLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderRefundRepository::class, \App\Repositories\OrderRefundRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderRepository::class, \App\Repositories\OrderRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\OrderCheckInUserRepository::class, \App\Repositories\OrderCheckInUserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\StayPeopleRepository::class, \App\Repositories\StayPeopleRepositoryEloquent::class);
        $this->app->bind(UserIntegralRepository::class, UserIntegralRepositoryEloquent::class);
        $this->app->bind(UserIntegralLogRepository::class, UserIntegralLogRepositoryEloquent::class);
        $this->app->bind(SignUpRepository::class, SignUpRepositoryEloquent::class);
        $this->app->bind(UserVoucherRepository::class, UserVoucherRepositoryEloquent::class);
    }
}
