<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Banner;

class BannerContorller extends BaseController
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('首页轮播设置');
            $content->body($this->grid());
        });
    }


    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('首页轮播设置');
            $content->description('编辑');

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

            $content->header('新建首页轮播');
            $content->description('编辑');

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
        return Admin::grid(Banner::class, function (Grid $grid){

            $grid->model()->orderBy('id', 'desc');

            $grid->id('编号');

            $grid->title('标题');
            $grid->banner_url('轮播图');
            $grid->link('跳转链接');
            $grid->order('权重')->editable();
            $grid->created_at('创建时间');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Banner::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title','标题');
            $form->number('order','权重');
            $form->text('link','跳转链接');
            $form->image('banner_url', '轮播图')->removable()->rules('image')->move('banner');
        });
    }


}
