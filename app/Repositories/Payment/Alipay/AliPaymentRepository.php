<?php

namespace App\Repositories\Payment\Alipay;



use App\Repositories\Payment\Alipay\lib\AlipayRepository;
use App\Repositories\Payment\Alipay\lib\AlipaySignature;

class AliPaymentRepository extends AlipayRepository
{
    /***
     * h5支付
     * @param $out_trade_no
     * @param $subject
     * @param $total_fee
     * @param $show_url
     * @param $body
     * @return string
     */
    public function pay($out_trade_no, $subject, $total_fee, $show_url, $body)
    {
        //支付
        return parent::pay($out_trade_no, $subject, $total_fee, $show_url, $body);
    }

    /***
     * H5支付通知接口
     */
    public function notifyUrl()
    {
        return parent::notifyUrl();
    }

    /**
     * H5支付返回接口
     */
    public function returnUrl()
    {
        return parent::returnUrl();
    }

    public function tradeAsyncFinish($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序
        //注意：
        //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        return parent::tradeAsyncFinish($orderNo, $tradeNo);
    }

    public function tradeAsyncSuccess($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序
        //注意：
        //付款完成后，支付宝系统发送该交易状态通知
        return parent::tradeAsyncSuccess($orderNo, $tradeNo);
    }


    public function tradeSyncOver($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（$orderNo）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序
        return parent::tradeSyncOver($orderNo, $tradeNo);
    }

    /***
     *创建支付宝收款记录
     * @param $out_trade_no
     * @param $total_fee
     */
    public function createOnline($out_trade_no, $total_fee)
    {
       return parent::createOnline($out_trade_no, $total_fee);
    }

    /**
     * 签名
     */
    public function signatures($items)
    {
        return parent::signatures($items);
    }

    /***
     * APP支付通知接口
     */
    public function notifyUrlForApp()
    {
        return parent::notifyUrlForApp();
    }

    /**
     * APP支付返回接口
     */
    public function returnUrlForApp()
    {
        return parent::returnUrlForApp();
    }
}
