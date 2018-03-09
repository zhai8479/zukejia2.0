<?php

//支付宝支付回调
Route::any("/order_alipay_notify",function (\Illuminate\Http\Request $request){
//    @file_put_contents("notifyAll.php",$request->all());
//    @file_put_contents("jsonNotifyAll.php",json_encode($request->all()));
    // 验证请求。
    if (! app('alipay.web')->verify()) {
        return 'fail';
    }

    $order = \App\Models\Order::where("order_no",$request->out_trade_no)->first();
//    @file_put_contents("thisOrder.php",$order);
//    @file_put_contents("JsonOrder.php",json_encode($order));
    //订单不存在
    if (!$order){
//        @file_put_contents("orderNotExist.php","订单不存在!");
        return 'fail';
    }
    //支付通道不为支付宝
    if ($order->pay_channel != 2){
//        @file_put_contents("orderNotALiPay.php","订单支付通道不为支付宝!");
        return 'success';
    }
    //订单状态不是已提交待支付
    if ($order->status != 1){
//        @file_put_contents("orderNotStatusOne.php","订单状态不是已提交待支付!");
        return "success";
    }
    //订单支付状态为已完成
    if ($order->pay_status == 3){
//        @file_put_contents("orderPayStatusThree.php","订单支付状态为已完成!");
        return "success";
    }

    // 判断通知类型。
    switch ($request->trade_status) {
        case 'TRADE_SUCCESS':
//            @file_put_contents("TRADE_SUCCESS.php",$request->out_trade_no);
        case 'TRADE_FINISHED':
            //支付成功，取得订单号进行其它相关操作。
//            @file_put_contents("TRADE_FINISHED.php",$request->out_trade_no);
            // 支付成功后增加支付记录
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
            break;
    }

    return "success";
});