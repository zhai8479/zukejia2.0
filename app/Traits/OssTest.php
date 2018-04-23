<?php
/**
 * Created by PhpStorm.
 * User: xcm
 * Date: 2018/4/23
 * Time: 15:07
 */

namespace App\Traits;

use JohnLui\AliyunOSS;

class OSSTest
{
    private $ossClient;
    private static $bucketName;

    public function __construct($isInternal = false)
    {
        $serverAddress = $isInternal ? config('alioss.ossServerInternal') : config('alioss.ossServer');
        $this->ossClient = AliyunOSS::boot(
            $serverAddress,
            config('alioss.AccessKeyId'),
            config('alioss.AccessKeySecret')
        );
    }

    public static function upload($ossKey, $filePath,$BucketName)
    {
        $oss = new OSSTest(false); // 上传文件使用内网，免流量费
        $oss->ossClient->setBucket($BucketName);
        $res = $oss->ossClient->uploadFile($ossKey, $filePath);
        return $res;
    }


}