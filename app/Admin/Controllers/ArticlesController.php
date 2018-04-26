<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Articles;
use App\Models\Navigation;

class ArticlesController extends BaseController
{
    use ModelForm;

    protected static $count = 0;

    protected static $list_result = [];

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('文章列表');
            $content->body($this->grid());
        });
    }


    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('文章修改');
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

            $content->header('新建资讯');
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
        return Admin::grid(Articles::class, function (Grid $grid){

            $grid->model()->orderBy('id', 'desc');


            $grid->filter(function(Grid\Filter $filter){

                // 去掉默认的id过滤器
               $filter->disableIdFilter();
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('id', '=', $input)->orWhere('title', 'like', "%{$input}%");
                }, '编号或标题');
                $filter->equal('title', '所属栏目')
                    ->select($this->navigation_type_list(0));
            });
            $grid->id('编号');
            $grid->navigation_type_id('所属栏目')->display(function ($value) {
                $types = Navigation::where('id',$value)->value('title');
                return $types;
            });
            $grid->title('标题');
            $grid->keywords('关键字 ');
//            $grid->excerpt('文章摘要');
            $grid->author('作者');
            $grid->display('文章是否显示')->display(function ($value) {
                return Articles::$excerpts[$value];
            });
            $grid->created_at('创建时间');

           $grid->actions(function ($actions) {
               $id = $actions->getKey();
               $actions->prepend('<a href="/admin/articles/' . $id . '"><i class="fa fa-eye" aria-hidden="true">详情</i>');
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
        return Admin::form(Articles::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('navigation_type_id','所属栏目')->options($this->navigation_type_list(0));
            $form->text('title','标题')->rules('required');
            $form->text('keywords','关键字')->rules('required');
            $form->text('excerpt','文章摘要')->rules('required');
            $form->editor('content','内容')->rules('required');
            $form->text('author','作者');
            $form->image('img_url', '文章缩略图')->removable()->rules('image')->move('article');
            $form->select('display','文章是否显示')->options(Articles::$excerpts);
        });
    }


}
