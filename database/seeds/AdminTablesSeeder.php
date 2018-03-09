<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Role;
use Illuminate\Database\Seeder;

class AdminTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // create a user.
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

        // add default menus.
        Menu::truncate();
        Menu::insert([
            [
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '首页',
                'icon'      => 'fa-bar-chart',
                'uri'       => '/',
            ],
            [
                'parent_id' => '0',
                'order'     => '2',
                'title'     => '房源管理',
                'icon'      => 'fa-bank',
                'uri'       => ''
            ],
            [
                'parent_id' =>'2',
                'order'     =>'3',
                'title'     =>'房源',
                'icon'      =>'fa-list',
                'uri'       =>'apartment'
            ],
            [
                'parent_id' => '0',
                'order'     => '4',
                'title'     => '用户管理',
                'icon'      => 'fa-user',
                'uri'       => ''
            ],
            [
                'parent_id' =>'4',
                'order'     =>'5',
                'title'     =>'用户',
                'icon'      =>'fa-list',
                'uri'       =>'users'
            ],
            [
                'parent_id' => '0',
                'order'     => '6',
                'title'     => '订单管理',
                'icon'      => 'fa-rmb',
                'uri'       => ''
            ],
            [
                'parent_id' =>'6',
                'order'     =>'7',
                'title'     =>'订单',
                'icon'      =>'fa-list',
                'uri'       =>'orders'
            ],
            [
                'parent_id' => 0,
                'order'     => 200,
                'title'     => '管理员',
                'icon'      => 'fa-tasks',
                'uri'       => '',
            ],
            [
                'parent_id' => 8,
                'order'     => 300,
                'title'     => '用户',
                'icon'      => 'fa-users',
                'uri'       => 'auth/users',
            ],
            [
                'parent_id' => 8,
                'order'     => 400,
                'title'     => '角色',
                'icon'      => 'fa-user',
                'uri'       => 'auth/roles',
            ],
            [
                'parent_id' => 8,
                'order'     => 500,
                'title'     => '权限',
                'icon'      => 'fa-user',
                'uri'       => 'auth/permissions',
            ],
            [
                'parent_id' => 8,
                'order'     => 600,
                'title'     => '菜单',
                'icon'      => 'fa-bars',
                'uri'       => 'auth/menu',
            ],
            [
                'parent_id' => 8,
                'order'     => 700,
                'title'     => '操作日志',
                'icon'      => 'fa-history',
                'uri'       => 'auth/logs',
            ],
            [
                'parent_id' => 0,
                'order'     => 800,
                'title'     => '报名管理',
                'icon'      => 'fa-linkedin',
                'uri'       => '',
            ],
            [
                'parent_id' => 14,
                'order'     => 900,
                'title'     => '活动报名',
                'icon'      => 'fa-user-plus',
                'uri'       => 'activities',
            ]
        ]);

        // add role to menu.
        Menu::find(2)->roles()->save(Role::first());
        Menu::find(8)->roles()->save(Role::first());
    }
}
