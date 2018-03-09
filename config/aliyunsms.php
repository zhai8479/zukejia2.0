<?php
/**
 * @desc 配置文件
 * @author zhan <grianchan@gmail.com>
 * @since 2017/6/28 14:58
 */

return [
    'Endpoint' => 'https://1881794231292161.mns.cn-hangzhou.aliyuncs.com',
    'AccessKeyID' => 'LTAI6kzacZsCRHn7',
    'AccessKeySecret' => 'k1es3Ej4ZnOnb9dGqoyemtqKmT7sWq',
    'TopicName' => 'sms.topic-cn-hangzhou',
    'SignName' => '容易装',
    'template' => [
        /* 找回登陆密码  【您好，您找回密码验证码为${code}。】  */
        'template_find_password_key_name' => 'SMS_73730026',
        /* 注册验证码 【您好，您的验证码为${code}，请及时完成注册。】*/
        'template_register_key_name' => 'SMS_73860034',
        /* 修改密码验证码 【您好，您正在尝试修改密码，验证码为${code}，请注意账户安全】*/
        'temp_change_password' => 'SMS_95245030',
        /* 使用验证码登陆 【您好，您正在进行短信验证码登陆操作，验证码为 ${code}。】*/
        'temp_code_login' => 'SMS_95425022',
        /* 用户修改手机号 【您好，您正在进行修改手机号操作，验证码为 ${code}。】*/
        'temp_change_mobile' => 'SMS_95495029',
    ],
    'ttl' => [

    ]
];