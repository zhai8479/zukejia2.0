<?php
/**
 * 积分配置
 * User: zhan
 * Date: 2017/11/8
 * Time: 15:12
 */

return [
    'log_integral' => true,
    'use_integral' => true,
    'detail' => [
        'register' => 100,
        'login' => [5, 10, 15, 50],
        'recommend' => 50,
        'pay' => 1,
        'bind' => [
            'wechat' => 50,
            'qq' => 50,
            'email' => 50
        ],
    ],
];