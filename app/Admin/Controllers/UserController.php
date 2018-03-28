<?php

namespace App\Admin\Controllers;

use App\Models\User;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Library\Password;
use App\Models\ChainDistrict;
use Illuminate\Support\MessageBag;

class UserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * 显示用户详情
     * @param integer $id 用户id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('用户');
            $content->description('详情');


            $content->body(Admin::form(User::class, function (Form $form) use ($id) {
                $user = User::find($id);


                $form->display('id', 'ID')->value($user->id);
                $form->display('user_name', '用户名')->value($user->user_name);
                $form->display('mobile', '手机号')->value($user->mobile);
                $form->email('email', '电子邮件')->readOnly()->value($user->email);
                $form->image('avatar_url', '头像')->readOnly()->value($user->avatar_url);
                $form->display('real_name', '真实姓名')->value($user->real_name);
                $form->display('id_card', '身份证')->readOnly()->value($user->id_card);
                $form->display('sex', '性别')->value($user->sex);
                $form->date('birthday', '生日')->readOnly()->value($user->birthday);
                $form->display('province_str', '省')->value($user->province_str());
                $form->display('city_str', '市')->value($user->city_str());
                $form->display('recommend_code', '邀请码')->value($user->recommend_code);
                $form->display('blood_type','血型')->value($user->blood_type);
                $form->display('education','学历')->value($user->education);
                $form->display('profession','职位')->value($user->profession);
                $form->display('from_platform', '注册平台')->value($user->from_platform);
                $form->display('ip', '注册ip')->value($user->ip);
                $form->display('created_at', '注册时间')->readOnly()->value($user->created_at);
                $form->disableReset();
                $form->disableSubmit();
            }));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('用户');
            $content->description('编辑');

            $content->body($this->form($id)->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户');
            $content->description('创建');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $model = new ChainDistrict();
        return Admin::grid(User::class, function (Grid $grid) use($model) {
            $grid->model()->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->user_name('用户名')->sortable();
            $grid->mobile('手机号')->sortable();
            $grid->real_name('真实姓名');
            $grid->email('电子邮件');
            $grid->id_card('身份证');
            $grid->sex('性别')->display(function ($value) {
                return User::$sexes[$value];
            });
            $grid->birthday('生日');
            $grid->column('userMoney.money','资金')->display(function ($value) {
                if ($value === null){
                    return 0;
                };
                return $value;
            });
            $grid->ip('最后访问ip');

            $grid->created_at('创建时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });
            $grid->updated_at('更新时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });

            $grid->disableRowSelector();

            $grid->actions(function($actions) use($grid) {
                $actions->disableDelete();
                $id = $actions->getKey();
                $actions->prepend('<a href="/admin/users/show/' . $id . '"><i class="fa fa-eye" aria-hidden="true">详情</i></a>');
                $actions->prepend('<a href="/admin/user_money/' . $id . '"title="用户资金" style="padding-right: 5px"><i class="fa fa-rmb"></i></a>');
                $actions->prepend('<a href="/admin/user_apartment/' . $id . '" title="用户房源" style="padding-right: 5px"><i class="fa fa-home" aria-hidden="true"></i></a>');
                $actions->prepend('<a href="/admin/project_investment' . '?user_id=' . $id . '"><i class="fa fa-line-chart" aria-hidden="true">投资</i></a>');
            });

            $grid->filter(function ($filter) {
                // 禁用id查询框
                $filter->disableIdFilter();

                // 文本过滤器
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('id', '=', $input)->orWhere('user_name', 'like', "%{$input}%");
                }, '编号或用户姓名');
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('id_card', '=', $input);
                }, '身份证号');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id = null)
    {
        return Admin::form(User::class, function (Form $form) use($id) {
            $request = \Dingo\Api\Http\Request::capture();
            if ($id) $form->hidden('id');
            $form->hidden('ip')->value($request->ip());
            $form->text('user_name', '用户名')->rules('required');
            $form->password('password', '密码');
            $form->text('mobile', '手机号')->rules('required|numeric');
            $form->text('real_name', '真实姓名')->rules('required');
            $form->email('email', '电子邮件');
            $form->text('id_card', '身份证')->rules('required');
            $form->select('sex', '性别')->options([0 => '未知', 1 => '男', 2 => '女']);
            $form->date('birthday', '生日');
            $form->select('province', '省')->options(function(){
                $provinceModel = new ChainDistrict();
                $province = $provinceModel->where('parent_id', '=', 0)->get(['name','id']);
                $tmp = [];
                $province->reject(function($element)use(&$tmp){
                    $tmp[$element->id] = $element->name;
                });
                return $tmp;
            })->load('city', '/admin/api/city')->rules('required');

            $form->select('city', '市')->options(function () {
                $cityModel = new ChainDistrict();
                $province = $this->province ? $this->province : 1;
                $city = $cityModel->where('parent_id', '=', $province)->get(['name','id']);
                $tmp = [];
                $city->reject(function($element)use(&$tmp){
                    $tmp[$element->id] = $element->name;
                });
                return $tmp;
            });
            $form->select('blood_type', '血型')->options(User::$bloods);
            $form->select('education', '学历')->options(User::$educations);
            $form->text('profession', '职位');

            $form->saving(function (Form $form) {

                $user_name = $form->user_name;
                $mobile = $form->mobile;
                $email = $form->email;
                if (empty($user_name) && empty($mobile) && empty($email)){
                    $error = new MessageBag([
                        'title'   => '请重新填写！！！',
                        'message' => '用户名，手机号和邮箱不能都为空！请重新填写！！！',
                    ]);
                    return back()->with(compact('error'));
                };
                $passHandle = new Password();
                if ($form->password) {
                    $form->password = $passHandle->create_password($form->password);
                } else if ($form->id) {
                    $user = User::find($form->id)->first();
                    $form->password = $user->password;
                }
            });
        });
    }
}
