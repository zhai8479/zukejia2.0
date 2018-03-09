<?php

namespace App\Admin\Controllers;

use App\Models\ProjectInvestment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class ProjectInvestmentsController extends Controller
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

            $content->header('项目投资');
            $content->description('投资列表');

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
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ProjectInvestment::class, function (Grid $grid) {
            $request = Request::capture();
            if ($request->get('user_id')) {
                $grid->model()->where('user_id', $request->get('user_id'));
            }
            $grid->id('ID')->sortable();
            $grid->project_id('项目id');
            $grid->user_id('用户id')->display(function ($user_id) {
                return "<a href='/admin/users/show/$user_id'>$user_id</a>";
            });
            $grid->no_num('单号');
            $grid->status('状态')->sortable()->display(function ($value) {
                return ProjectInvestment::$status_list[$value];
            });
            $grid->now_issue_num('当前所在期数');
            $grid->pay_at('支付时间');
            $grid->end_at('结束时间');
            $grid->repayment_start_at('开始还款时间');
            $grid->repayment_end_at('结束还款时间');
            $grid->cancel_at('取消订单时间');
            $grid->created_at('创建时间');
            $grid->updated_at('修改时间');

            $grid->disableCreation();
            $grid->actions(function (Grid\Displayers\Actions $actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $id = $actions->getKey();
                /**
                 * @var $row ProjectInvestment
                 */
                $row = $actions->row;
                if (in_array($row->status, [ProjectInvestment::STATUS_REPAYMENT, ProjectInvestment::STATUS_OVER])) {
                    // 还款中和还款结束显示还款列表按钮
                    $actions->prepend('<a href="/admin/project_repayments/' . $id . '"><i class="fa fa-adjust">还款列表</i></a>');
                }
            });
            $grid->disableRowSelector();

            $grid->filter(function(Grid\Filter $filter){
                // 在这里添加字段过滤器
                $filter->like('no_num', '单号');
                $filter->equal('status', '状态')
                    ->select(ProjectInvestment::$status_list);
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
        return Admin::form(ProjectInvestment::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', '创建时间');
            $form->display('updated_at', '修改时间');
        });
    }
}
