<?php

namespace App\Admin\Controllers;

use App\Models\SignUp;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Admin\Extensions\Mark;
use App\Admin\Extensions\Tools\BatchMark;

class ActivityController extends Controller
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

            $content->header('报名管理');
            $content->description('活动报名');

            $content->body($this->grid());
        });
    }
    /**
     * 免费装修列表
     */
    public function title_index($name)
    {
        return Admin::content(function (Content $content) use ($name){

            $content->header('报名管理');
            $content->description('活动报名');

            $content->body($this->grid($name));
        });
    }

    /**
     * 毛胚房报名列表
     */
    public function title_indexT($name)
    {
        return Admin::content(function (Content $content) use ($name){

            $content->header('报名管理');
            $content->description('毛胚房报名');

            $content->body($this->grid($name));
        });
    }

    /**
     * 列表实现
     *
     * @return Grid
     */
    protected function grid($name = null)
    {
        return Admin::grid(SignUp::class, function (Grid $grid) use ($name){
            if ($name) {
                $grid->model()->where('signUpTitle', $name);
            }
            $grid->model()->orderBy('id', 'desc');
            $grid->disableCreation();


            $grid->filter(function(Grid\Filter $filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('signUpTitle', '活动的标题');

            });


            $grid->id('编号')->sortable();
            $grid->name('姓名')->sortable();
            $grid->mobile('手机')->sortable();
            $grid->address('地址');
            $grid->signUpTitle('活动标题');
            $grid->type('来源');
            $grid->ip('IP地址');
            $grid->area('面积');
            $grid->community('小区/楼盘');

            $grid->actions(function ($action){
                $action->disableEdit();
                $action->disableDelete();
            });
            $grid->created_at('创建时间');

            $grid->actions(function ($actions) {
                $actions->disableEdit();
//                $actions->disableDelete();
//                // 标记操作
//                $row = $actions->row;
//                $actions->append(new Mark($actions->getKey()));
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
        return Admin::form(SignUp::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('name','姓名');
            $form->text('mobile','电话');
            $form->text('address','地址');
            $form->text('signUpTitle','报名标题');
            $form->text('ip','ip地址');
            $form->text('community','小区/楼盘');
        });
    }
}