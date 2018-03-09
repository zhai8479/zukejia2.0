<?php
return [

	// 安全检验码，以数字和字母组成的32位字符。
	'key' => '7cu7ioqmq1rzqlmcyv3y9cas7c5j1oha',

	//签名方式
	'sign_type' => 'MD5',

	// 服务器异步通知页面路径。
	'notify_url' => 'http://api.zukehouse.com/order_alipay_notify',

	// 页面跳转同步通知页面路径。
	'return_url' => 'http://www.zukehouse.com/user_center/order'
];
