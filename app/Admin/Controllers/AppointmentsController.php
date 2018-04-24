<?php

namespace App\Admin\Controllers;

use App\Models\Appointment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\User;

class AppointmentsController extends Controller
{
    use ModelForm;

    /**
     * 列表接口
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('预约看房');
            $content->description('预约看房列表');

            $content->body($this->grid());
        });
    }

    /**
     * 列表实现
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Appointment::class, function (Grid $grid){

            $grid->model()->orderBy('id');
            $grid->disableCreation();


            $grid->filter(function(Grid\Filter $filter){
                // 去掉默认的id过滤器
                $filter->disableIdFilter();
                $filter->equal('status','状态')->select(['0' => '未查看','1' => '已查看']);
            });

            $grid->id('编号')->sortable();
            $grid->name('预约者')->sortable();
            $grid->mobile('手机')->sortable();
            $grid->sex('性别')->display(function ($value){
                if ($value == 0) return '未知';
                else if ($value == 1)return'男';
                return '女';
            });
            $grid->apartment_id('房源id');
            $grid->user_id('用户id');

            $grid->user_name('用户名')->display(function () {
                $user_name = User::query()->where('id', $this->user_id)->value('user_name');
                return $user_name;
            });
            $grid->appointments_time('预约时间');
            $grid->message('留言');
            $states = [
                'on'  => ['value' => 0, 'text' => '未查看', 'color' => 'primary'],
                'off' => ['value' => 1, 'text' => '已查看', 'color' => 'default'],
            ];
            $grid->status('是否查看')->switch($states);

            $grid->actions(function ($action){
                $action->disableEdit();
                $action->disableDelete();
            });
            $grid->created_at('创建时间');

            $grid->actions(function ($actions) {
                $actions->disableEdit();
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Appointment::class, function (Form $form) {

            $states = [
                'on'  => ['value' => 0, 'text' => '未查看', 'color' => 'success'],
                'off' => ['value' => 1, 'text' => '已查看', 'color' => 'danger'],
            ];
            $form->switch('status','是否查看')->states($states);
        });
    }
}