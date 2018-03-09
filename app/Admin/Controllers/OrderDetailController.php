<?php

namespace App\Admin\Controllers;

use App\Models\Apartment;
use App\Models\Order;

use App\Models\OrderCheckInUser;
use App\Models\OrderInvoiceLog;
use App\Models\OrderRefund;
use App\Models\User;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class OrderDetailController extends Controller
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

            $content->header('header');
            $content->description('description');

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
            $content->description('详情');

            $content->body($this->form($id)->edit($id));
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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @param $id
     * @return Form
     */
    protected function form($id = null)
    {
        return Admin::form(Order::class, function (Form $form) use ($id) {
            $form->tools(function (Form\Tools $tools) {
                // 去掉跳转列表按钮
                $tools->disableListButton();
            });

            $form->display('id', '订单ID');

            $form->display('order_no', '订单编号');

            $form->display('status', '订单状态')->with(function ($value) {
                $order = new Order();
                $arr = $order::$order_status;
                return $arr[$value];
            });

            $form->display('user_id', '用户名')->with(function ($value) {
                $model = User::find($value)->first();
                return $model->user_name;
            });

            $form->display('apartment_id', '房源')->with(function ($value) {
                $model = Apartment::find($value)->first();
                return $model->title;
            });
//
            $form->display('apartment_id', '房源地址')->with(function ($value) {
                $model = Apartment::find($value)->first();
                return $model->search_address;
            });

            $form->display('rent_type', '价格规则')->with(function ($value) {
                $order = new Order();
                $arr = $order::$rent_types;
                return $arr[$value];
            });

            $form->display('rental_price', '租金')->with(function ($value) {
                return $value / 100;
            });;

            $form->display('rental_deposit', '押金')->with(function ($value) {
                return $value / 100;
            });;

            $form->display('start_date', '入住开始日期');

            $form->display('end_date', '入住结束日期');

            $form->display('housing_numbers', '入住天数');

            $form->display('pay_status', '付款状态')->with(function ($value) {
                $order = new Order();
                $arr = $order::$pay_status;
                return $arr[$value];
            });

            $form->display('pay_channel', '付款渠道')->with(function ($value) {
                $order = new Order();
                $arr = $order::$pay_channels;
                return $arr[$value];
            });

            $form->display('pay_account', '付款账户');

            $form->display('pay_money', '付款金额')->with(function ($value) {
                return $value / 100;
            });;

            $form->display('external_id', '外部付款单号');

            $form->display('is_refunds', '是否有退款')->with(function ($value) {
                $order = new Order();
                $arr = $order::$is_refund;
                return $arr[$value] . ' <a href=""></a>';
            });

            $form->display('refunds_total_money', '退款金额')->with(function ($value) {
                return $value / 100;
            });

            $form->display('is_refunds', '是否需要发票')->with(function ($value) {
                $order = new Order();
                $arr = $order::$has_invoice;
                return $arr[$value];
            });

            $form->display('created_at', '下单时间');
//            $form->display('updated_at', 'Updated At');

            /*
             * 获取发票数据
             */
            $invoice = OrderInvoiceLog::where('order_id', '=', $id)->first();
            if ($invoice) {
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->invoice_no} </div></div>", '发票号');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->title} </div></div>", '发票抬头');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->taxpayer_no} </div></div>", '纳税人识别码');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->money} </div></div>", '发票金额');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->addressee_phone} </div></div>", '联系电话');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->addressee_name} </div></div>", '姓名');
                $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$invoice->addressee_address} </div></div>", '联系地址');
            }

            $orderCheckIns = OrderCheckInUser::where('order_id', '=', $id)->get();

            /*
            * 获取入住人
            */
            if ($orderCheckIns) {
                foreach ($orderCheckIns as $key => $value) {
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->real_name} </div></div>", '入住人 ' . ($key + 1) . ' 姓　名');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->id_card} </div></div>", '入住人 ' . ($key + 1) . ' 身份证');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->mobile} </div></div>", '入住人 ' . ($key + 1) . ' 手机号');
                }
            }

            $orderRefunds = OrderRefund::where('order_id', '=', $id)->get();

            if ($orderRefunds) {
                foreach ($orderRefunds as $key => $value) {
                    $typeStr = '';
                    switch ($value->refund_type) {
                        case 1:
                            $typeStr = '押金退款';
                            break;
                        case 2:
                            $typeStr = '房款退款';
                            break;
                        default:
                            break;
                    }

                    $statusStr = '';
                    switch ($value->refund_type) {
                        case 1:
                            $statusStr = '退款中';
                            break;
                        case 2:
                            $statusStr = '退款成功';
                            break;
                        case 3:
                            $statusStr = '退款失败';
                            break;
                        default:
                            break;
                    }

                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$typeStr} </div></div>", '退款订单 ' . ($key + 1) . ' 退款类型');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->refund_housing_numbers} </div></div>", '退款订单 ' . ($key + 1) . ' 入住人数');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->refund_no} </div></div>", '退款订单 ' . ($key + 1) . ' 退款单号');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->external_refund_id} </div></div>", '退款订单 ' . ($key + 1) . ' 外部单号');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$statusStr} </div></div>", '退款订单 ' . ($key + 1) . ' 退款状态');
                    $form->html("<div class=\"box box-solid box-default no-margin\"> <div class=\"box-body\"> {$value->refund_over_at} </div></div>", '退款订单 ' . ($key + 1) . ' 退款时间');
                }
            }
        });
    }
}
