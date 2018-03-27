<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    //用户管理
    $router->resource('users', UserController::class);
    $router->get('users/show/{id}', 'UserController@show');     // 用户详情 1
    $router->get('user_money/{id}', 'UserMoneyLogController@index');//用户资金变动记录列表 1
    $router->get('user_money/{id}/charge', 'UserMoneyLogController@charge');// 调节用户资金 1
    $router->post('user_money/{id}', 'UserMoneyLogController@store'); //保存数据路由如果要更新数据store换成update 1
    //房源管理
    $router->resource('apartment', ApartmentController::class);
    $router->get('user_apartment/{user_id}','ApartmentController@user_apartment_index');    // 用户房源 1
    //订单管理
    $router->resource('orders', OrderController::class);   //1
    $router->get('order_detail/{id}', 'OrderDetailController@show');//1

    //活动管理
    $router->resource('activities', ActivityController::class);
    $router->post('/activities/batch_mark', 'CustomPJaxController@batch_mark');
    $router->get('/activities/title_index/{name}', 'ActivityController@title_index');    //0元装修 1
    $router->get('/activities/title_indexT/{name}', 'ActivityController@title_indexT');    //毛胚房报名 1

    // 项目
    $router->resource('project-types', ProjectTypeController::class);
    $router->resource('project', ProjectController::class);
    $router->resource('project_investment', ProjectInvestmentsController::class);
    $router->get('/project/show/{id}', 'ProjectController@show');
    $router->get('/project_repayments/{id}', 'ProjectRepaymentsController@index');
    $router->post('/project_repayments/{id}/early_repayment', 'ProjectRepaymentsController@early_repayment');

    //栏目管理
    $router->resource('navigation_type',NavigationController::class); // 1

    //资讯管理
    $router->resource('articles',ArticlesController::class);// 1
    $router->get('articles/{id}','ArticlesController@show');// 1
});

Route::group([
    'prefix'        => '/admin/api',
    'namespace'     => config('admin.route.namespace'),
], function (Router $router) {
    $router->get('/city', 'AddressController@city');  // 1
    $router->get('/district', 'AddressController@district') ; // 1
});
