<?php

namespace App\Admin\Controllers;

use App\Models\Apartment;
use App\Models\Order;

use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class OrderController extends Controller
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

            $content->header('订单');
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

            $content->header('订单');
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

            $content->header('订单');
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
        return Admin::grid(Order::class, function (Grid $grid) {
            $model = new Order();
            $grid->disableCreation();
            $grid->filter(function ($filter) {
                // 禁用id查询框
                $filter->disableIdFilter();
            });
            $grid->disableRowSelector();

            $grid->id('编号')->sortable();

            $grid->order_no('订单编号');

            $grid->user_name('用户名')->display(function () {
                $user_name = User::query()->where('id', $this->user_id)->value('user_name');
                return $user_name;
            });

            $grid->apartment_id('房源')->display(function ($value) {
                return Apartment::whereId($value)->value('title')??'';
            });

            $grid->status('订单状态')->display(function ($value)use($model){
                $arr = $model::$order_status;
                return $arr[$value];
            });

            $grid->external_id('外部支付单号');

            $grid->pay_status('支付状态')->display(function ($value)use($model) {
                $arr = $model::$pay_status;
                return $arr[$value];
            });

            $grid->pay_account('支付账号');

            $grid->pay_channel('支付类型')->display(function ($value)use($model){
                $arr = $model::$pay_channels;
                return $arr[$value]??'';
            });

            $grid->pay_money('支付价格');

            $grid->start_date('入住起始时间');
            $grid->end_date('入住结束时间');

            $grid->rent_type('价格规则')->display(function ($value)use($model) {
                $arr = $model::$rent_types;
                return $arr[$value];
            });

            $grid->rental_price('租金');

            $grid->rental_deposit('押金');

            $grid->need_invoice('是否需要发票')->display(function ($value) {
                return $value ? '需要' : '不需要';
            });

            $grid->created_at('下单时间');
//            $grid->updated_at('更新时间');
            $grid->actions(function ($action) {
                $action->disableEdit();
                $action->disableDelete();
 //               $action->append('<a href="/admin/order_detail/' . $action->getKey() . '"><i class="fa fa-folder-open-o"></i></a>');
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
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
