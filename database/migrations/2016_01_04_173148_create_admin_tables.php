<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;

class CreateAdminTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        Schema::connection($connection)->create(config('admin.database.users_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('username', 190)->unique();
            $table->string('password', 60);
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->string('remember_token', 100)->nullable();
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.roles_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50);
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.permissions_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->unique();
            $table->string('slug', 50);
            $table->string('http_method')->nullable();
            $table->text('http_path');
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.menu_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->default(0);
            $table->integer('order')->default(0);
            $table->string('title', 50);
            $table->string('icon', 50);
            $table->string('uri', 50)->nullable();
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.role_users_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('user_id');
            $table->index(['role_id', 'user_id']);
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.role_permissions_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('permission_id');
            $table->index(['role_id', 'permission_id']);
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.user_permissions_table'), function (Blueprint $table) {
            $table->integer('user_id');
            $table->integer('permission_id');
            $table->index(['user_id', 'permission_id']);
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.role_menu_table'), function (Blueprint $table) {
            $table->integer('role_id');
            $table->integer('menu_id');
            $table->index(['role_id', 'menu_id']);
            $table->timestamps();
        });
        Schema::connection($connection)->create(config('admin.database.operation_log_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('path');
            $table->string('method', 10);
            $table->string('ip', 15);
            $table->text('input');
            $table->index('user_id');
            $table->timestamps();
        });

        $this->initAdminDb();
    }

    protected function initAdminDb () {
        Administrator::truncate();
        Administrator::create([
            'username'  => 'admin',
            'password'  => bcrypt('admin'),
            'name'      => 'Administrator',
        ]);

        // create a role.
        Role::truncate();
        Role::create([
            'name'  => 'Administrator',
            'slug'  => 'administrator',
        ]);

        // add role to user.
        Administrator::first()->roles()->save(Role::first());

        //create a permission
        Permission::truncate();
        Permission::insert([
            [
                'name'        => 'All permission',
                'slug'        => '*',
                'http_method' => '',
                'http_path'   => '*',
            ],
            [
                'name'        => 'Dashboard',
                'slug'        => 'dashboard',
                'http_method' => 'GET',
                'http_path'   => '/',
            ],
            [
                'name'        => 'Login',
                'slug'        => 'auth.login',
                'http_method' => '',
                'http_path'   => "/auth/login\r\n/auth/logout",
            ],
            [
                'name'        => 'User setting',
                'slug'        => 'auth.setting',
                'http_method' => 'GET,PUT',
                'http_path'   => '/auth/setting',
            ],
            [
                'name'        => 'Auth management',
                'slug'        => 'auth.management',
                'http_method' => '',
                'http_path'   => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs",
            ],
            [
                'name'        => 'Api tester',
                'slug'        => 'ext.api-tester',
                'http_method' => '',
                'http_path'   => '/api-tester*',
            ],
        ]);

        Role::first()->permissions()->save(Permission::first());

        // add default menus.
        Menu::truncate();
        Menu::insert([
            //1
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '首页',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            //2
            [
                'parent_id' => '0',
                'order'     => '2',
                'title'     => '房源管理',
                'icon'      => 'fa-bank',
                'uri'       => ''
            ],
            //3
            [
                'parent_id' =>'2',
                'order'     =>'3',
                'title'     =>'房源',
                'icon'      =>'fa-list',
                'uri'       =>'apartment'
            ],
            //4
            [
                'parent_id' => '0',
                'order'     => '3',
                'title'     => '订单管理',
                'icon'      => 'fa-rmb',
                'uri'       => ''
            ],
            //5
            [
                'parent_id' =>'4',
                'order'     =>'7',
                'title'     =>'订单',
                'icon'      =>'fa-list',
                'uri'       =>'orders'
            ],
            //6
            [
                'parent_id' =>'0',
                'order'     =>'4',
                'title'     =>'项目管理',
                'icon'      =>'fa-optin-monster',
                'uri'       =>'/'
            ],
            //7
            [
                'parent_id' =>'6',
                'order'     =>'3',
                'title'     =>'项目',
                'icon'      =>'fa-list',
                'uri'       =>'project'
            ],
            //8
            [
                'parent_id' =>'6',
                'order'     =>'3',
                'title'     =>'项目类型',
                'icon'      =>'fa-sliders',
                'uri'       =>'project-types'
            ],
            //9
            [
                'parent_id' =>'6',
                'order'     =>'3',
                'title'     =>'项目投资',
                'icon'      =>'fa-sliders',
                'uri'       =>'project_investment'
            ],
            //10
            [
                'parent_id' => 0,
                'order'     => 5,
                'title'     => '报名管理',
                'icon'      => 'fa-linkedin',
                'uri'       => '',
            ],
            //11
            [
                'parent_id' => 10,
                'order'     => 900,
                'title'     => '活动报名',
                'icon'      => 'fa-user-plus',
                'uri'       => 'activities',
            ],
            //12
            [
                'parent_id' => 10,
                'order'     => 1400,
                'title'     => '0元装修',
                'icon'      => 'fa-user-plus',
                'uri'       => '/activities/title_index/0元装修',
            ],
            //13
            [
                'parent_id' => 10,
                'order'     => 1500,
                'title'     => '毛胚房报名',
                'icon'      => 'fa-user-plus',
                'uri'       => '/activities/title_indexT/毛胚房报名',
            ],
            //14
            [
                'parent_id' => 0,
                'order'     => 6,
                'title'     => '资讯管理系统',
                'icon'      => 'fa-bars',
                'uri'       => '',
            ],
            //15
            [
                'parent_id' => 14,
                'order'     => 1100,
                'title'     => '栏目管理',
                'icon'      => 'fa-bars',
                'uri'       => '/navigation_type',
            ],
            //16
            [
                'parent_id' => 14,
                'order'     => 1200,
                'title'     => '文章管理',
                'icon'      => 'fa-bars',
                'uri'       => '/articles',
            ],
            //17
            [
                'parent_id' => '0',
                'order'     => '7',
                'title'     => '用户管理',
                'icon'      => 'fa-user',
                'uri'       => ''
            ],
            //18
            [
                'parent_id' =>'17',
                'order'     =>'5',
                'title'     =>'用户',
                'icon'      =>'fa-list',
                'uri'       =>'users'
            ],

            //19
            [
                'parent_id' => 0,
                'order'     => 8,
                'title'     => '管理员',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            //20
            [
                'parent_id' => 19,
                'order'     => 300,
                'title'     => '用户',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            //21
            [
                'parent_id' => 19,
                'order'     => 400,
                'title'     => '角色',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            //22
            [
                'parent_id' => 19,
                'order'     => 500,
                'title'     => '权限',
                'icon'      => 'fa-user',
                'uri'       => 'auth/permissions',
            ],
            //23
            [
                'parent_id' => 19,
                'order'     => 600,
                'title'     => '菜单',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            //24
            [
                'parent_id' => 19,
                'order'     => 700,
                'title'     => '操作日志',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
            //25
            [
                'parent_id' => 0,
                'order'     => 9,
                'title'     => '接口测试',
                'icon'      => 'fa-user-plus',
                'uri'       => 'api-tester',
            ],

        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(8)->roles()->save(Role::first());
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('admin.database.connection') ?: config('database.default');
        Schema::connection($connection)->dropIfExists(config('admin.database.users_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.roles_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.permissions_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.menu_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.user_permissions_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.role_users_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.role_permissions_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.role_menu_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.operation_log_table'));
    }
}
