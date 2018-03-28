<?php

namespace App\Admin\Controllers;

use App\Models\ProjectType;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\MessageBag;

class ProjectTypeController extends Controller
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

            $content->header('项目类型');
            $content->description('列表');

            $content->body($this->grid());
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

            $content->header('项目类型');
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

            $content->header('项目类型');
            $content->description('添加');

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
        return Admin::grid(ProjectType::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->name('项目名称');
            $grid->max_money('最大金额')->display(function ($money) {
                return $money . ' 元';
            });
            $grid->min_money('最小金额')->display(function ($money) {
                return $money  . ' 元';
            });
            $grid->repayment_type('还款类型')->display(function ($type) {
                return ProjectType::$repayment_type_list[$type];
            });
            $grid->guarantee_type('担保方式')->display(function ($type) {
                return ProjectType::$guarantee_type_list[$type];
            });
            $grid->interest_day('延迟计息天数');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');

            $grid->disableRowSelector();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
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
        return Admin::form(ProjectType::class, function (Form $form) {
            $form->display('id', 'ID');
            $form->text('name', '类型名称')->rules('required|string|max:100');
            $form->currency('min_money', '最小金额')->symbol('元')
                ->rules('required|numeric|min:0');
            $form->currency('max_money', '最大金额')->symbol('元')
                ->rules('required|numeric|min:0');
            $form->select('repayment_type', '还款方式')->options(ProjectType::$repayment_type_list);
            $form->select('guarantee_type', '担保方式')->options(ProjectType::$guarantee_type_list);
            $form->number('interest_day', '延后计息天数')->rules('required|integer|min:0');
            $form->display('created_at', '创建时间');
            $form->display('updated_at', '更新时间');
            $form->saving(function (Form $form) {
                if ($form->input('min_money') > $form->input('max_money')) {
                    $error = new MessageBag([
                        'title'   => '表单数据错误',
                        'message' => '最小金额应小于最大金额',
                    ]);
                }

                $projectTypeExist = ProjectType::where('name', $form->input('name'))->first();
                if (isset($projectTypeExist)) {
                    if ($form->input('id') && $projectTypeExist->id != $form->input('id')) {
                        // 编辑
                        $error = new MessageBag([
                            'title'   => '表单数据错误',
                            'message' => '名称重复使用',
                        ]);
                    } else {
                        // 新建
                        $error = new MessageBag([
                            'title'   => '表单数据错误',
                            'message' => '名称重复使用',
                        ]);
                    }
                }
                if (isset($error)) return back()->withInput()->with(compact('error'));

                $form->input('max_money', $form->input('max_money') );
                $form->input('min_money', $form->input('min_money') );


            });
        });
    }
}
