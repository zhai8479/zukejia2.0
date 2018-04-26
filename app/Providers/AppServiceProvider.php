<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Prettus\Validator\Exceptions\ValidatorException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 设置dingo的异常处理

        // 表单验证失败
        $this->app['Dingo\Api\Exception\Handler']->register(function (ValidationException $exception) {
            return Response::make([
                'msg'     => '表单数据不合规则',
                'code' => 422,
                'errors' => $exception->validator->errors(),
            ], 200);
        });
        $this->app['Dingo\Api\Exception\Handler']->register(function (ValidatorException $exception) {
            return Response::make([
                'msg'     => '表单数据不合规则',
                'code' => 422,
                'errors' => $exception->getMessageBag(),
            ], 200);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->register(PasswordServiceProvider::class);
        $this->app->register(AliyunSmsServiceProvider::class);
    }
}
