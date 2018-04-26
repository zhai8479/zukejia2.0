<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'oss',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'admin' => [
            'driver' => 'local',
            'root' => public_path('uploads'),
            'visibility' => 'public',
            'url' => env('APP_URL').'/uploads',
        ],
        'oss' => [
            'driver'  => 'oss',
            'domains' => [
                'default'   => 'oss-cn-beijing-internal.aliyuncs.com', //你的七牛域名
                'https'     => 'static.zukehouse.com',         //你的HTTPS域名
                'custom'    => 'static.zukehouse.com',                //你的自定义域名
            ],
            'access_key'=> 'LTAIfewnsSfKs8oq',  //AccessKey
            'secret_key'=> 'joLPEzjpqopAXvY9j7LBbOQQuX8Cx2',  //SecretKey
            'bucket'    => 'zkj-static',  //Bucket名字
            'notify_url'=> '',  //持久化处理回调地址
            'url'       => 'http://zkj-static.oss-cn-beijing-internal.aliyuncs.com/',  // 填写文件访问根url
        ],
    ],

];
