<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/8/19
 * Time: 9:18
 */
namespace App\Repositories\Payment\Alipay\lib;

require_once("alipay_core.function.php");
require_once("alipay_md5.function.php");
require_once("alipay_rsa.function.php");

class AlipaySignature
{

    var $alipay_config;

    function __construct($alipay_config)
    {
        $this->alipay_config = $alipay_config;
    }

    function AlipaySignature($alipay_config)
    {
        $this->__construct($alipay_config);
    }

    function signature($items)
    {
        $alipayConfig = $this->alipay_config;
        if($items['partner'] == $alipayConfig['partner'] && isset($items['data']))
        {
            /****
            //将post接收到的数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串。
            $data=createLinkstring($items);
            //将待签名字符串使用私钥签名,且做urlencode. 注意：请求到支付宝只需要做一次urlencode.
            $rsa_sign=urlencode(rsaSign($data, $alipayConfig['private_key']));

            //把签名得到的sign和签名类型sign_type拼接在待签名字符串后面。
            $data = $data.'&sign='.'"'.$rsa_sign.'"'.'&sign_type='.'"'.$alipayConfig['sign_type'].'"';

            //返回给客户端,建议在客户端使用私钥对应的公钥做一次验签，保证不是他人传输。
            return data;
             ***/

            //按照“参数=参数值”的模式用“&”字符拼接成字符串
            $data = $items['data'];

            //将待签名字符串使用私钥签名,且做urlencode. 注意：请求到支付宝只需要做一次urlencode.
            $rsa_sign=rsaSign($data, $alipayConfig['private_key']);


            //返回客户端
            return $rsa_sign;
        }else{
            return '不匹配或为空';
        }
    }
}