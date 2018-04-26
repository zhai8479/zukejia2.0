<?php
/* *
 * 配置文件
 * 版本：3.5
 * 日期：2016-06-25
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

 * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
 * 解决方法：
 * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
 * 2、更换浏览器或电脑，重新登录查询。
 */
return [
    //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
    //合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
    'partner' => '2088921484171654',

    //收款支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
    'seller_id' => '2088921484171654',

    // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
    'key'			=> '7cu7ioqmq1rzqlmcyv3y9cas7c5j1oha',
    //商户的私钥,此处填写原始私钥去头去尾，RSA公私钥生成：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.nBDxfy&treeId=58&articleId=103242&docType=1
    'private_key'	=> 'MIICWwIBAAKBgQDLmMnYVb9ooQE6/yPNoEJlb+srpmOhpIhS0XIc3VXbj7mVFtQ8YrqFw/Wtk0/UgG9mm+y9laHQlSq9NDxA9GPehi6nPp2i6t5xOu9vsRRayYOUK5NoBq9t1fbhtXSs9+b9YgOdEyg5dzmyL27H7/MpaCqwRKpuheeFrAHQX2bq1wIDAQABAoGAGkm6FjLMKihPzlxPNSeyKwLObK5pQt/JslfU0iFKCrV+EuAFu15MEyWAPU8+CYQj3i3X3YrKf/IiuJdcCE8F7eRI73pJ9YQLsAHcJ8tj9ZX5hs0NSAcpWt/nxdAAblhbSfpXO39v0FAov2zTo8k11aXbIbnlxNUkfvZaUzvqFpECQQD8WcieNtEy/PdI4i5f+UsyiI0MXnXR8oHV4LV0iDXb4Se2xRRN48H2PDj5VpJT3diijdf1xRBD+/SCjvuhfoljAkEAzoqD5C9m3WsgG6zCRLWfp/L1A18lgGONwTOWU7vO4Slm5N6jMP0T2gXGfXWNIsfXlpN6rLH6CEdKZ6uJlh2M/QJAF2k883Cid9iAGILjSoQWNdn1O/CHfbLB2NW//8+jL4Lz7EDcYV1/4Rg1MOyXJrCuKGaAYoyltajqRjJ9pb3XgQJASDeQhEG6wuLJGEQgrSugRnYiC2rCTxEAKLSj9GUvofT7AD2EbAWhYlMwov9uOOINVJ3+f9G/LhPXRUZqVi3A9QJAZO4v+H2JVdj6ihYDlMbnBpWRybS916JScl1OhkclkbYPlwb4uUKrs80sZcky3Jpf4oFUAoac2qCeIRmbDFgyow==',

    //支付宝的公钥，查看地址：https://openhome.alipay.com/platform/keyManage.htm?keyType=partner
    'alipay_public_key' => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQCnxj/9qwVfgoUh/y2W89L6BkRAFljhNhgPdyPuBV64bfQNN1PjbCzkIM6qRdKBoLPXmKKMiFYnkd6rAoprih3/PrQEB/VsW8OoM8fxn67UDYuyBTqA23MML9q1+ilIZwBC2AQ2UBVOrFXfFl75p6/B5KsiNG9zpgmLCUYuLkxpLQIDAQAB',
    // 服务器异步通知页面路径  需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    'notify_url' => 'https://service.yoyohr.com/payment/notify',

    // 页面跳转同步通知页面路径 需http://格式的完整路径，不能加?id=123这类自定义参数，必须外网可以正常访问
    'return_url' => 'https://service.yoyohr.com/payment/sync_return',

    //签名方式  MD5  |  RSA
    'sign_type'    => strtoupper('RSA'),

    //字符编码格式 目前支持utf-8
    'input_charset' => strtolower('utf-8'),

    //ca证书路径地址，用于curl中ssl校验
    //请保证cacert.pem文件在当前文件夹目录中
    'cacert' => getcwd() . '\\cacert.pem',

    //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
    'transport' => 'https',

    // 支付类型 ，无需修改
    'payment_type' => '1',

    // 产品类型，无需修改
    'service' => 'alipay.wap.create.direct.pay.by.user',
    //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
];


