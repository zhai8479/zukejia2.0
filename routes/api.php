<?php


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//测试

DingoRoute::version('v1', function () {
    DingoRoute::group(['namespace' => 'App\Api\Controllers'], function () {
        DingoRoute::group(['prefix' => 'users'], function () {
            DingoRoute::get('/mobile_register_code', "UserController@mobile_register_code");
            // 手机注册
            DingoRoute::post('/mobile_register', "UserController@mobile_register");
            // 手机号登陆
            DingoRoute::post('/mobile_login', "UserController@mobile_login");
            // 检测手机号是否已被使用
            DingoRoute::post('/check_mobile_is_use', "UserController@check_mobile_is_use");
            // 检测用户名是否已经被使用
            DingoRoute::post('/check_user_name_is_use', "UserController@check_user_name_is_use");
            // 获取用户头像
            DingoRoute::get('/user_avatar', "UserController@user_avatar");
            // 获取用户简要信息
            DingoRoute::get('/user_simple_info', "UserController@user_simple_info");
            // 获取找回密码验证码
            DingoRoute::get('/find_password_code', "UserController@find_password_code");
            // 找回密码
            DingoRoute::post('/find_password', "UserController@find_password");
            // 手机验证码登陆
            DingoRoute::post('/mobile_code_login', "UserController@mobile_code_login");
            // 获取手机登陆验证码
            DingoRoute::get('/mobile_code_login_code', "UserController@mobile_code_login_code");
            //获取房源的经营信息
            DingoRoute::post('/show_apartment', "UserController@show_apartment");

            // 需要登陆
            DingoRoute::group(['middleware' => ['api.auth']], function () {
                // 获取自己的信息
                DingoRoute::get('/self_info', "UserController@self_info");
                //获取自己的房源列表
                DingoRoute::get('/self_apartment', "UserController@self_apartment");
                //获取自己的房源详细信息
                DingoRoute::get('/show_self_apartment/{id}', "UserController@show_self_apartment")->where('id', '[0-9]+');
                // 获取修改密码验证码
                DingoRoute::get('/change_password_code', "UserController@change_password_code");
                // 修改密码接口
                DingoRoute::post('/change_password', "UserController@change_password");
                // 绑定邮箱接口
                DingoRoute::post('/bind_email', "UserController@bind_email");
                // 修改头像接口
                DingoRoute::post('/change_avatar', "UserController@change_avatar");
                // 修改社区信息
                DingoRoute::post('/change_community_info', "UserController@change_community_info");
                // 修改身份证信息
                DingoRoute::post('/change_id_card_info', "UserController@change_id_card_info");
                // 获取修改手机号验证码
                DingoRoute::get('/change_mobile_code', "UserController@change_mobile_code");
                // 修改手机号
                DingoRoute::post('/change_mobile', "UserController@change_mobile");
                // 修改用户名
                DingoRoute::post('/change_user_name', "UserController@change_user_name");
                // 修改邮箱
                DingoRoute::post('/change_email', "UserController@change_email");
                //验证邮箱是否被使用
                DingoRoute::post('/check_email_is_use','UserController@check_email_is_use');
                // 获取用户详细信息
                DingoRoute::get('/user_detail_info', "UserController@user_detail_info");
                // 退出登陆操作
                DingoRoute::post('/login_out', "UserController@login_out");

            });
        });
        DingoRoute::group(['prefix' => 'district'], function () {
            // 获取省列表
            DingoRoute::get('province_list', 'DistrictController@province_list');
            // 获取市列表
            DingoRoute::get('city_list', 'DistrictController@city_list');
            // 根据省代码，获取市列表
            DingoRoute::get('city_list_by_province_code', 'DistrictController@city_list_by_province_code');
        });

        // 入住人接口
        DingoRoute::group(['prefix' => 'stay_people', 'middleware' => ['api.auth']], function () {
            // 根据id获取入住人信息
            DingoRoute::get('show/{id}', 'StayPeopleController@show');
            // 获取入住人列表
            DingoRoute::get('index', 'StayPeopleController@index');
            // 删除入住人
            DingoRoute::delete('delete/{id}', 'StayPeopleController@delete');
            // 编辑入住人
            DingoRoute::post('update', 'StayPeopleController@update');
            // 添加入住人
            DingoRoute::post('store', 'StayPeopleController@store');
        });

        // 用户资金接口
        DingoRoute::group(['prefix' => 'user_money', 'middleware' => ['api.auth']], function () {
            // 获取用户资金信息
            DingoRoute::get('me', "UserMoneyController@me");
        });

        // 用户资金记录接口
        DingoRoute::group(['prefix' => 'user_money_log', 'middleware' => ['api.auth']], function () {
            // 获取资金记录详情
            DingoRoute::get('index', "UserMoneyLogsController@index");
            // 获取资金明细列表
            DingoRoute::get('show/{id}', "UserMoneyLogsController@show");
        });


        // 短信相关
        DingoRoute::group(['prefix' => 'sms'], function () {
            // 图片验证码
            DingoRoute::get('image', 'SmsController@image');
        });

        // 房源信息
        DingoRoute::group(['prefix' => 'house'], function () {
            // 获取房源详情
            DingoRoute::get('show/{id}', 'ApartmentsController@show');
            // 获取房源列表
            DingoRoute::get('index', 'ApartmentsController@index');
            DingoRoute::get('city', 'ApartmentsController@city');//获取城市信息
            DingoRoute::get('get_city_by_key', 'ApartmentsController@get_city_by_key');
            DingoRoute::get('district', 'ApartmentsController@district');//获取行政区域信息
            DingoRoute::get('decoration_style', 'ApartmentsController@decorationStyle');//获取装修风格
            DingoRoute::get('direction', 'ApartmentsController@direction');//获取房屋朝向
            DingoRoute::get('type', 'ApartmentsController@type');//获取房屋类型
            DingoRoute::get('room', 'ApartmentsController@room');//获取房屋户型
            DingoRoute::get('facilities', 'ApartmentsController@facilities');//获取配套设施
            DingoRoute::get('get_recommend_list', 'ApartmentsController@getRecommendList');//获取推荐列表
            DingoRoute::get('get_hot_list', 'ApartmentsController@getHotList');//获取热门列表
            DingoRoute::get('get_rental_list', 'ApartmentsController@getRentalList');//获取不同类型房源列表
            DingoRoute::get('more','ApartmentsController@more');//获取更多类型信息列表
        });

        // 订单
        DingoRoute::group(['prefix' => 'order', 'middleware' => ['api.auth']], function () {
            // 创建订单
            DingoRoute::post('store', 'OrdersController@store');
            // 获取订单列表
            DingoRoute::get('index', 'OrdersController@index');
            // 获取订单详情
            DingoRoute::get('show/{id}', 'OrdersController@show');
            // 取消已提交的订单
            DingoRoute::post('cancel_no_pay', 'OrdersController@cancel_no_pay');
            // 支付订单
            DingoRoute::post('pay', 'OrdersController@pay');
            // 支付订单取消
            DingoRoute::post('cancel_is_pay', 'OrdersController@cancel_is_pay');
            // 退房
            DingoRoute::post('check_out', 'OrdersController@check_out');
            // 阿姨查房
            DingoRoute::post('rounds', 'OrdersController@rounds');
        });
        DingoRoute::group(['prefix' => 'order'], function () {
            DingoRoute::get('apartment_use_status', 'OrdersController@apartment_use_status');
        });

        // 用户积分
        DingoRoute::group(['middleware' => 'api.auth'], function () {
            DingoRoute::group(['prefix' => 'integral'], function () {
                // 用户获取积分信息
                DingoRoute::get('me', 'UserIntegralController@me');
            });
            DingoRoute::group(['prefix' => 'integral_log'], function () {
                // 用户获取积分明细列表
                DingoRoute::get('index', 'UserIntegralLogController@index');
            });

        });

        // 活动报名
        DingoRoute::group(['prefix' => 'sign_up'], function () {
            //创建一个报名记录
            DingoRoute::post('store', 'SignUpsController@store');
            //修改一个报名记录状态值
            DingoRoute::post('mark', 'SignUpsController@mark');
        });

        //预约看房
        DingoRoute::group(['prefix' => 'appointment'], function(){
            //创建一个预约看房记录
            DingoRoute::post('store','AppointmentsController@store');
            //修改一个预约记录状态值
            DingoRoute::post('update','AppointmentsController@update');
        });
        // 项目
        DingoRoute::group(['prefix' => 'project'], function () {
            // 项目还款
            DingoRoute::group(['prefix' => 'investment', 'middleware' => ['api.auth']], function () {
                DingoRoute::post('create_order', 'ProjectInvestmentsController@create_order');//创建项目订单
                DingoRoute::post('pay_order', 'ProjectInvestmentsController@pay_order');//支付项目订单
                DingoRoute::post('cancel_order', 'ProjectInvestmentsController@cancel_order');//取消项目订单
                DingoRoute::get('index', 'ProjectInvestmentsController@index');//获取项目订单列表
                DingoRoute::get('show/{id}', 'ProjectInvestmentsController@show');//获取项目订单详情
                DingoRoute::get('repayment_index', 'ProjectInvestmentsController@repayment_index');//获取还款列表
                DingoRoute::get('repayment_show/{id}', 'ProjectInvestmentsController@repayment_show');//获取还款详情
                DingoRoute::get('account_survey', 'ProjectInvestmentsController@account_survey');       // 账户概况
                DingoRoute::get('investment_survey', 'ProjectInvestmentsController@investment_survey'); // 投资概况
            });
            // 项目
            DingoRoute::get('index', 'ProjectsController@index');//获取项目列表
            DingoRoute::get('show/{id}', 'ProjectsController@show');//获取项目详情
            DingoRoute::get('recommend_index', 'ProjectsController@recommend_index');//获取项目推荐列表
            DingoRoute::get('config', 'ProjectsController@config');//返回项目相关配置信息
            DingoRoute::get('show_apartment_order_list', 'ProjectsController@show_apartment_order_list');//获取房源信息
            DingoRoute::get('investment_rank', 'ProjectInvestmentsController@investment_rank');//投资排名
            DingoRoute::get('platform_info', 'ProjectsController@platform_info');//平台信息
        });

        // 代金卷
        DingoRoute::group(['prefix' => 'voucher'], function () {
            DingoRoute::group(['middleware' => 'api.auth'], function () {
                // 获取代金卷列表
                DingoRoute::get('index', 'UserVouchersController@index');
                // 获取代金卷详情
                DingoRoute::get('show/{id}', 'UserVouchersController@show');
            });
        });

        //菜单管理
        DingoRoute::group(['prefix' => 'menu'],function(){
            //获取所有菜单名
            DingoRoute::get('help_center_menu','NavigationController@help_center_menu');
            //获取资讯中心菜单名
            DingoRoute::get('information_menu','NavigationController@information_menu');
            //获取 底栏信息
            DingoRoute::get('bottom','NavigationController@bottom');
            //获取新手学堂信息
            DingoRoute::get('new_teach','NavigationController@new_teach');
            // 新手常见问题
            DingoRoute::get('novice_common_problem_list','NavigationController@novice_common_problem_list');
            // 账号常见问题
            DingoRoute::get('account_common_problem_list','NavigationController@account_common_problem_list');
            // 项目常见问题
            DingoRoute::get('project_common_problem_list','NavigationController@project_common_problem_list');

        });
        //资讯管理
        DingoRoute::group(['prefix' => 'information'],function(){
            //文章列表
            DingoRoute::get('index','ArticlesController@index');
            //文章详情
            DingoRoute::get('show/{id}','ArticlesController@show');
        });
        //轮播设置
        DingoRoute::group(['prefix' => 'banner'],function(){
            DingoRoute::get('index','BannerController@index');
     });
    });
});

