<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\ChargeButton;
use App\Models\User;
use App\Models\UserMoney;
use App\Models\UserMoneyLog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UserMoneyLogController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @param $id
     * @return Content
     */
    public function index($id)
    {
        return Admin::content(function (Content $content) use($id) {

            $content->header('用户充值');
            $content->description('新增');

            $content->body($this->grid($id));
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

            $content->header('用户充值');
            $content->description('调节');

            $content->body($this->form()->edit($id));
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

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Charge interface.
     *
     * @param $user_id
     * @return Content
     */
    public function charge($user_id)
    {
        return Admin::content(function (Content $content) use ($user_id) {

            $content->header('用户资金');
            $content->description('调节');

            $user_money = UserMoney::firstOrCreate(['user_id' => $user_id]);
            $money_id = $user_money->id;
            $content->body($this->form($money_id, $user_id));
        });
    }

    /**
     * Make a grid builder.
     *
     * @param $id
     * @return Grid
     */
    protected function grid($id)
    {
        return Admin::grid(UserMoneyLog::class, function (Grid $grid) use($id) {
            $grid->model()->where('user_id', '=', $id)->orderBy('id', 'desc');
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function ($filter) {
                // 禁用id查询框
                $filter->disableIdFilter();
            });
            $grid->disableRowSelector();

            $grid->tools(function ($tools) use($grid) {
                $tools->append(new ChargeButton($grid));
            });

            $grid->id('编号')->sortable();
            $grid->type('类型')->display(function ($value) {
                return UserMoneyLog::$logTypes[$value];
            });

            $grid->in_out('资金流向')->display(function ($value) {
                return UserMoneyLog::$in_out_list[$value];
            });

            $grid->money('资金增量')->display(function ($value) {
                if ($this->in_out) return '￥ -'.($value);
                return '￥ +'.($value);
            });

            $grid->admin_id('管理员')->display(function ($value) {
                $admin = \DB::table('admin_users')->where('id', $value)->first();
                return $admin ? $admin->name : '';
            });

            $grid->description('描述');

            $grid->admin_note('管理员备注');

            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @param null $money_id
     * @param null $user_id
     * @return Form
     */
    protected function form($money_id = null, $user_id = null)
    {
        return Admin::form(UserMoneyLog::class, function (Form $form) use($money_id, $user_id){
            $form->display('money', '现有资金')->with(function()use($money_id, $user_id){
                return \DB::table('user_money')->where('user_id', $user_id)->value('money') ;
            });
            $form->radio('in_out', '操作')->values([
                1 => '增加',
                2 => '减少'
            ])->default(1);
            $form->text('money', '金额')->rules('required');
            $form->hidden('admin_id')->value(Admin::user()->id);
            $form->hidden('user_id')->value($user_id);
            $form->radio('type', '充值类型')->values([
                5 => '管理员调节'
            ])->default(5);
            $form->textarea('description', '描述')->rules('required');
            $form->textarea('admin_note', '管理员备注')->rules('required');

            $form->saving(function ($form){
                $form->in_out  -= 1;
                $user_money_obj = UserMoney::where('user_id', $form->user_id)->first();
                $user_money = $user_money_obj->money;
                $operator = $form->in_out ? - $form->money : $form->money;
                $user_money_obj->money = ($user_money  + $operator) ;
                $user_money_obj->save();
                $form->money ;
                if (isset($error)) return back()->withInput()->with(compact('error'));
            });
        });
    }
}
