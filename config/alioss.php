<?php
/**
 * oss配置读取
 * Created by PhpStorm.
 * User: cdria
 * Date: 2018/4/20
 * Time: 11:36
 */

return [
    'ossServer' => env('ALIOSS_SERVER', null),                      // 外网
    'ossServerInternal' => env('ALIOSS_SERVERINTERNAL', null),      // 内网
    'AccessKeyId' => env('ALIOSS_KEYID', null),                     // key
    'AccessKeySecret' => env('ALIOSS_KEYSECRET', null),             // secret
];