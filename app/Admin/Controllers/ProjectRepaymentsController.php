<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\EarlyRepayment;
use App\Models\ProjectRepayment;

use App\Repositories\ProjectRepaymentRepository;
use App\Repositories\ProjectRepaymentRepositoryEloquent;
use App\Repositories\UserMoneyRepository;
use App\Repositories\UserMoneyRepositoryEloquent;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class ProjectRepaymentsController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @param integer   $id 项目投资id
     * @return Content
     */
    public function index($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('项目投资');
            $content->description('还款');

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

            $content->header('header');
            $content->description('description');

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
     * 提前还款操作
     * @param Request|\Request $request
     */
    public function early_repayment(Request $request)
    {
        $ids = $request->get('ids');
        foreach ($ids as $id) {
            /**
             * @var $ur UserMoneyRepositoryEloquent
             */
            $ur = app(UserMoneyRepository::class);
            $ur->repayment($id);
        }
    }

    /**
     * Make a grid builder.
     *
     * @param $investment_id
     * @return Grid
     */
    protected function grid($investment_id)
    {
        return Admin::grid(ProjectRepayment::class, function (Grid $grid) use ($investment_id) {
            $grid->model()->where('investment_id', $investment_id);
            $grid->id('ID')->sortable();
            $grid->investment_id('项目投资id');
            $grid->user_id('用户id')->display(function ($user_id) {
                return "<a href='/admin/users/show/$user_id'>$user_id</a>";
            });
            $grid->issue_num('期数');
            $grid->money('还款金额');
            $grid->principal('本金');
            $grid->interest('利息');
            $grid->is_repayment('是否已还款')->display(function ($value) {
                if ($value == ProjectRepayment::IS_REPAYMENT) {
                    $ret = "<div class='glyphicon glyphicon-ok'></div>";
                } else {
                    $ret = "<div class='glyphicon glyphicon-remove'></div>";
                }
                return $ret;
            });
            $grid->estimate_time('预计还款时间');
            $grid->real_time('实际还款时间');

            $grid->created_at();
            $grid->updated_at();

            $grid->disableCreation();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableEdit();
                $actions->disableDelete();
            });
            $grid->tools(function (Grid\Tools $tools) {
                $tools->batch(function (Grid\Tools\BatchActions $batch) {
                    $batch->disableDelete();
                    $batch->add('提前还款', new EarlyRepayment());
                });
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
        return Admin::form(ProjectRepayment::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
