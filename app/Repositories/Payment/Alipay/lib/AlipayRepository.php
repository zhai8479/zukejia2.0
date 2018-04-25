<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2016/1/6
 * Time: 17:01
 */

namespace App\Repositories\Payment\Alipay\lib;


class AlipayRepository
{
    protected $alipayConfig;

    public function __construct()
    {
        $this->alipayConfig = config('alipay');
    }

    /**
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
        $this->createOnline($out_trade_no, $total_fee);//创建支付宝收款记录

        $alipay_config = $this->alipayConfig;
        /************************************************************/
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => $alipay_config['service'],
            "partner" => $alipay_config['partner'],
            "seller_id" => $alipay_config['seller_id'],
            "payment_type" => $alipay_config['payment_type'],
            "notify_url" => $alipay_config['notify_url'],
            "return_url" => $alipay_config['return_url'],
            "_input_charset" => trim(strtolower($alipay_config['input_charset'])),
            "out_trade_no" => $out_trade_no,
            "subject" => $subject,
            "total_fee" => $total_fee,
            "show_url" => $show_url,
            "app_pay" => "Y",//启用此参数能唤起钱包APP支付宝
            "body" => $body,
            //其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
            //如"参数名"	=> "参数值"   注：上一个参数末尾需要“,”逗号。
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
        return $html_text;
    }

    public function notifyUrl()
    {
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipayConfig);
        $verify_result = $alipayNotify->verifyNotify();
        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
            $orderNo = $_POST['out_trade_no'];//商户订单号
            $tradeNo = $_POST['trade_no'];//支付宝交易号
            $trade_status = $_POST['trade_status'];//交易状态
            if ($trade_status == 'TRADE_FINISHED') {
                $this->tradeAsyncFinish($orderNo, $tradeNo);
            } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                $this->tradeAsyncSuccess($orderNo, $tradeNo);
            }
            echo "success";        //请不要修改或删除
        } else {
            echo "fail"; //验证失败
        }
    }


    public function returnUrl()
    {
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipayConfig);
        $verify_result = $alipayNotify->verifyReturn();
        if ($verify_result) {//验证成功
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
            $orderNo = $_GET['out_trade_no'];//商户订单号
            $tradeNo = $_GET['trade_no'];//支付宝交易号
            $trade_status = $_GET['trade_status'];//交易状态
            if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                $this->tradeSyncOver($orderNo, $tradeNo);
            } else {
                echo "trade_status=" . $_GET['trade_status'];
            }
            $html = '<html>';
            $html = $html . '<head>';
            $html = $html . '<meta charset="utf-8">';
            $html = $html . '<meta name="description" content="">';
            $html = $html . '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1.0, user-scalable=no"/>';
            $html = $html . '<meta content="telephone=no" name="format-detection">';
            $html = $html . '<title>付款成功</title>';
            $html = $html . '</head>';
            $html = $html . '<body>';
            $html = $html . '<h2 style="text-align: center;margin-top: 30%">';
            $html = $html . '<div><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAHgAAAB4CAMAAAAOusbgAAACK1BMVEVMaXEJm+wNlOpV//8Kl+wNk+v///8QjukQj+kMlOoMleoPkeoPkOkLluoJnPULmuwOkuoPkeoKm+wTnP8LlewJoPUQj+gLoPkMluoLl+sLmescqv8LmOsPp+8Kmu0MlusMnu4OkukLm+z///8KmesKnOwJoPALnO0LnO0KmuwKmOr///8MlOkPken///////////8Lmu3////W7vv///////////87r+////////////////8Qjun////5/P4VkOkSj+kil+r8/f5Fp+4Rjunv9/0UkOnx+P39/v7A4fktnOv+/v7z+f1ase80n+z3+/7J5fqHxvMynuyIx/TY7Psvnes8o+0fleoZkumWzfXR6fo5oexpuPGTzPXN5/rr9f0dlOrm8/wqmuu02/iOyfTE4/n4+/7i8fxWr+9/wvO53vg7ou3Z7fvj8vxOq+5xvPHf7/yi0/Zmt/Ekl+ro9Pyx2vdsufHH5fl1vfJUru8XkenW6/tLqu7d7/uy2veSy/Sm1fa23Ph4v/J2vvKZz/VKqe4+pO3t9v17wPJgtPBYsO/Q6PpCpe15wPKp1vfp9P2Aw/OYzvVIqO6l1Pab0PXa7fut2PfF4/komeue0fY3oezh8PyQyvSUzPVvu/Fds/ByvPLT6vtktvDL5vobk+rl8/zk8vxApe0lmOun1fbc7vu93/hQre9itfD6/P5Sre/0+v683/iGxfMakumLyPS13PjS6vqv2ffD4vk6Vy9RAAAAPHRSTlMAbPEDo/QF/v7t5fv81zSJ9v17Ddob/ivew58JsyBl4j33igGqYjZYhpWYPeX7vLoDVr7kSDnhcK36OgKPUBGtAAAFaklEQVR4AcTSRXojQQyG4can1GZmZnt4/3tmlV0uklwkxw6NGQuV7wKvSiVPua7fajaSQroo3iumC0mj2fK7ntPavU7qBy72I9Xptd2o9eg74Wb0ParbVqtRIiCRSKKqRXZaqUG6WmVqRw0XARQLFqExO84PodEwPzZz/Riaxb4BO+vDoP5M93MnBKNoovXVgwDGBQN1d0mwEC0V2XIWlsqWVdxcDGvFOXl3noHFMn9k3RLBalSSc0cClhMjGXcpYD2x5H/vRs7f/V8BJ4k7/zwnOIrmt9xcBs7K5K675RgOi8tX4Syclr3mruC41WV3QJDu6XG97R+ko8ElNwwg38NaB0YQXoAnUOivHozJuTsjDphmZ3AfHDD6p64PHhj+sTuOueB4fATnwQUjf+iGQz54GB7AC/DBWBzAgYwg3+2Jgr07BSeM6Q6u8MKVrVut8cK16gaOAM7jAqINnHDDyX+3LrhhUd9smhve7DrFD6e899rED1P7He6BH0bvHe58BdzZfLGUIt+L1Cen7cPPuFfa87qwD7+xZs9bkiRhFMBrbZsvsLbu7Um3bdvd4+bYMz22bRtvtzurjDpV8UXG7snfv4WMSH3ahUSfZJ5I/8D0bVKvD5GKHiqFDhJ9mPkAqZikMoFkH2TeRyomqLQh2fvm0GQtKmTMc60C1OtIw3wqj2Dh9czLSMN6Kgtg4aXMG0jDBcZuuLDwRuY5pGADlZWw8Zzdgd2Ki76/sWQWgsNUhlI6cMPmA2OV/Ftpd9Witcu7kGteyFi3k8aB64+PM09p3d7JemQrp1INywO/AVFrbxEl46vPuPjXVcYGlsDKG3JUbGouolFRx51R/KmilLFFsPOS+AKpqKOFo80+sJvKEdh5XXpl+iH/NrBsZFH7vdpj+9Y8CKkz2F7G2Br7nP596FwsI8nK3hOtDpRD5X0HPZpshKX39WHxUjfJK6fOIV/jlh1llKyErQ/0iUAvyb4AgqYzt4uo49XA1ofa1OcayT6Y1LeHzFd4px6WntAme/3kVSRoWjdIjakFjbDxiS69rbE7aU7bFWoUTMxFSPKFNqHfTI7AhrN4IXU6mxtg9qK2hBkhh2HlXB0Fp7cvgcHH2qJtkNwCKysp89aUR5B8qi1TC2yD6jGadTaPymWq5iJ75HVY2MxkW6cbke9zfStigLyDZOuobN3VScHAgWuBphWha75sIouRaJjKfhdN27spGa8t0TVf8gJUFen1wMxZT2XhX9+enb5ASWnxmVnEXhAabH0k18PILaYydgh/m7dgkKJwfYM60/qW4kaSRT4MZrqptCxBLJpcSNnWU12qpahroj4iubQEEncvs9y/K7xIdSovbwDeFtvGrR7J7lFoOetCZlkV5L9I99PgyvBPcqO8miQr1wa6yHCUWcJyaATX62jwy8/f/yaMBpxi/mnZSScnM7lzntmmSqAXbFlGkx+/FoYhjWP8y/lVe6L4Akz3FzHbrXUBZCdP0+BZafzTs4L/qOy4fHxV+46DIXP0lsCsvMVwYHHg5R6k0dk9SDa3VTywPOJzqgsoKp6BnaEO8cDyULPmYSl1Nh1ugL2Zs+KB5TFuxe5O5ujeOxThv7lZJR5YHlw7G2qLd/JvZR2LThzB/3FtSjqweVTfVbPN31bR+Efxdm2FABCEQRj3nBh3d/qhi+uDctH03GYb+PN93wS9hZ6SYQlOSHDiIR/OwDHEQTKcB6As9pLhPORmsZYM50FGi81/GGBV981/GIBkp00tF53r81gQ4pEACAUI7AREvzxzNt88DuyeU5SdwvtwrsAHGnySwkc4fHbEh1aStKxlSstalW66mK4tH22niekk+WAnMB98A1PlsirWcKimAAAAAElFTkSuQmCC" alt=""></div>';
            $html = $html . '<strong style="font-size: 18px; line-height: 40px">支付成功！</strong>';
            $html = $html . '    <div><span>' . $_GET['total_fee'] . '</span><span>元</span></div>';
            $html = $html . '</h2>';
            $html = $html . '<div>';
            $html = $html . '<a style="display: block; width: 160px; height: 46px; background: #108ee9; border: 1px solid #d9d9d9;line-height: 46px;text-align: center; font-size: 16px;color: #fff;margin: 0 auto; text-decoration: none; border-radius: 5px" href="http://h5.yoyohr.com/">返回首页</a>';
            $html = $html . '</div>';
            $html = $html . '</body>';
            $html = $html . '</html>';
            echo $html;
        } else {
            echo "验证失败";
        }
    }


    public function tradeAsyncFinish($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序
        //注意：
        //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        $NewSaleOrderAccountRepository = app()->make(\App\Repositories\NewSaleOrderAccountRepository::class);
        $NewSaleOrderAccountRepository->updateStatus($orderNo, $tradeNo);
    }

    public function tradeAsyncSuccess($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
        //如果有做过处理，不执行商户的业务程序
        //注意：
        //付款完成后，支付宝系统发送该交易状态通知
        $NewSaleOrderAccountRepository = app()->make(\App\Repositories\NewSaleOrderAccountRepository::class);
        $NewSaleOrderAccountRepository->updateStatus($orderNo, $tradeNo);
    }


    public function tradeSyncOver($orderNo, $tradeNo)
    {
        //判断该笔订单是否在商户网站中已经做过处理
        //如果没有做过处理，根据订单号（$orderNo）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
        //如果有做过处理，不执行商户的业务程序
    }


    /***
     * 签名
     * @param $partner
     * @param $service
     * @return string
     */
    public function signatures($items)
    {
        $alipay_config = $this->alipayConfig;
        $alipaySignature = new AlipaySignature($alipay_config);
        return $alipaySignature->signature($items);
    }

    /**
     * 创建支付宝收款记录
     * @param $out_trade_no
     * @param $total_fee
     */
    public function createOnline($out_trade_no, $total_fee)
    {
        $NewSaleOrderAccountRepository = app()->make(\App\Repositories\NewSaleOrderAccountRepository::class);
        return $NewSaleOrderAccountRepository->createOnline($out_trade_no, $total_fee, 3);
    }


    public function notifyUrlForApp()
    {
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipayConfig);
        if ($alipayNotify->getResponse($_POST['notify_id']))//判断成功之后使用getResponse方法判断是否是支付宝发来的异步通知。
        {
            if ($alipayNotify->getSignVeryfy($_POST, $_POST['sign'])) {//使用支付宝公钥验签
                //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
                $orderNo = $_POST['out_trade_no'];//商户订单号
                $tradeNo = $_POST['trade_no'];//支付宝交易号
                $trade_status = $_POST['trade_status'];//交易状态
                if ($trade_status == 'TRADE_FINISHED') {
                    $this->tradeAsyncFinish($orderNo, $tradeNo);
                } else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                    $this->tradeAsyncSuccess($orderNo, $tradeNo);
                }
                echo "success";        //请不要修改或删除
            } else //验证签名失败
            {
                echo "sign fail";
            }
        } else //验证是否来自支付宝的通知失败
        {
            echo "response fail";
        }
    }


    public function returnUrlForApp()
    {
        return 'success:' . $_POST['success'] . 'sign：' . $_POST['sign'] . 'result:' . $_POST['result'];

        $alipay_config = $this->alipayConfig;
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($this->alipayConfig);

        //注意：在客户端把返回参数请求过来的时候务必要把sign做一次urlencode,保证"+"号字符不会变成空格。
        if ($_POST['success'] == "true")//判断success是否为true.
        {
            //验证参数是否匹配
            if (str_replace('"', '', $_POST['partner']) == $alipay_config['partner']) {

                //获取要校验的签名结果
                $sign = $_GET['sign'];

                //除去数组中的空值和签名参数,且把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
                //$data=createLinkstring(paraFilter($_POST));
                $data = $_POST['result'];

                //logResult('data:'.$data);//调试用，判断待验签参数是否和客户端一致。
                //logResult('sign:'.$sign);//调试用，判断sign值是否和客户端请求时的一致，
                $isSgin = false;

                //获得验签结果
                $isSgin = rsaVerify($data, $alipay_config['alipay_public_key'], $sign);
                if ($isSgin) {
                    //echo "return success";
                    //此处可做商家业务逻辑，建议商家以异步通知为准。
                    echo "9000";
                } else {
                    //echo "return fail";
                    echo "4000";
                }
            }
        }
    }
}

