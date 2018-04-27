<?php

//支付宝支付回调
Route::any("/order_alipaymobile_notify",function (\Illuminate\Http\Request $request){
    define('IN_ECS', true);



    require_once('../alipay-sdk-PHP/aop/request/AlipayTradeAppPayRequest.php');
    require_once('../alipay-sdk-PHP/aop/AopClient.php');

    $aop = new AopClient;
    $aop->alipayrsaPublicKey = config('alioss.alipaySecret');
    $flag = $aop->rsaCheckV1($_POST, NULL, "RSA2");


    if($_POST['trade_status'] == 'TRADE_SUCCESS' ){
        //业务处理
        $order = \App\Models\Order::where("order_no",$_POST['out_trade_no'])->first();
        $pay_start_at = $pay_over_at = date('Y-m-d H:i:s');
        //生成一个订单号
        $pay_order_no =  date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
//            @file_put_contents("pay_order_no.php",$pay_order_no);
        \App\Models\OrderPay::create([
            'order_id' => $order->id,
            'order_pay_no' => $pay_order_no,
            'ip' => $request->ip(),
            'pay_channel' => 2,
            'pay_account' => '',
            'pay_start_at' => $pay_start_at,
            'pay_over_at' => $pay_over_at,
            'pay_status' => 3,
        ]);
        // 记录房子被租数据
        \App\Models\RentalRecord::create([
            'apartment_id' => $order->apartment_id,
            'start_date' => $order->start_date,
            'end_date' => $order->end_date,
            'order_id' => $order->id,
        ]);
        $order->pay_channel = 2;
        $order->pay_start_at = $pay_start_at;
        $order->pay_over_at = $pay_over_at;
        $order->pay_account = '';
        $order->pay_status = 3;
        $order->order_pay_no = $pay_order_no;
        $order->status = 2;
        $order->save();
        return "success";

    }else{
        return 'fail';
    }

});