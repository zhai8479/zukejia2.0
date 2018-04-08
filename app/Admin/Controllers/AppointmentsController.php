<?php

namespace App\Admin\Controllers;

use App\Models\Appointment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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
            $grid->name('姓名')->sortable();
            $grid->mobile('手机')->sortable();
            $grid->sex('性别');
            $grid->apartment_id('房源id');
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

            $form->display('id', 'ID');
            $form->text('name','姓名');
            $form->text('mobile','电话');
            $form->text('sex','地址');
            $form->text('apartment_id','房源ID');
            $form->text('appointments_time','预约时间');
            $states = [
                'on'  => ['value' => 0, 'text' => '未查看', 'color' => 'success'],
                'off' => ['value' => 1, 'text' => '已查看', 'color' => 'danger'],
            ];
            $form->switch('status','是否查看')->states($states);
        });
    }
}