<?php
/**
 * 0ss服务
 * Created by PhpStorm.
 * User: cdria
 * Date: 2018/4/20
 * Time: 11:43
 */

namespace App\Traits;

use OSS\OssClient;


class OSS
{



    private $ossClient;
    private static $bucketName;

    public function __construct($isInternal = false)
    {
        //$this->$bucketName = $bucketName;
        $serverAddress = $isInternal ? config('alioss.ossServerInternal') : config('alioss.ossServer');
        $this->ossClient = new OssClient(config('alioss.AccessKeyId'), config('alioss.AccessKeySecret'),  config('alioss.ossServer'), true);
    }


    public function getExtension($blob)
    {
        $extension = substr($blob, stripos($blob, '/') + 1, stripos($blob, ';') - stripos($blob, '/') - 1);
        return $extension;
    }


    /**
     * 随机生成文件名
     *
     * @param string $extension
     *
     * @return string
     */
    public function generateFileName($extension)
    {
        return md5(Str::random(10) . Carbon::now()->toDateTimeString()) . '.' . $extension;
    }





   /* public static function uploadFile($object, $file)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费
        $res = $oss->ossClient->uploadFile(self::$bucketName , $object, $file, $options = NULL);
        return $res;
    }*/

    /**
     * 直接把变量内容上传到oss
     * @param $osskey
     * @param $content
     */
    public static function uploadContent($osskey, $content)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费
        $oss->ossClient->setBucket(config('alioss.BucketName'));
        $oss->ossClient->uploadContent($osskey, $content);

    }

    /**
     * 删除存储在oss中的文件
     *
     * @param string $ossKey 存储的key（文件路径和文件名）
     * @return
     */
    public static function deleteObject($ossKey)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费

        return $oss->ossClient->deleteObject(config('alioss.BucketName'), $ossKey);
    }

    /**
     * 复制存储在阿里云OSS中的Object
     *
     * @param string $sourceBuckt 复制的源Bucket
     * @param string $sourceKey - 复制的的源Object的Key
     * @param string $destBucket - 复制的目的Bucket
     * @param string $destKey - 复制的目的Object的Key
     * @return Models\CopyObjectResult
     */
    public function copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS(true); // 上传文件使用内网，免流量费

        return $oss->ossClient->copyObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    /**
     * 移动存储在阿里云OSS中的Object
     *
     * @param string $sourceBuckt 复制的源Bucket
     * @param string $sourceKey - 复制的的源Object的Key
     * @param string $destBucket - 复制的目的Bucket
     * @param string $destKey - 复制的目的Object的Key
     * @return Models\CopyObjectResult
     */
    public function moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey)
    {
        $oss = new OSS(true); // 上传文件使用内网，免流量费

        return $oss->ossClient->moveObject($sourceBuckt, $sourceKey, $destBucket, $destKey);
    }

    public static function getUrl($ossKey)
    {
        $oss = new OSS();
        $oss->ossClient->setBucket(config('alioss.BucketName'));
        return $oss->ossClient->getUrl($ossKey, new \DateTime("+1 day"));
    }

    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }

    public static function getAllObjectKey($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->getAllObjectKey($bucketName);
    }

    /**
     * 获取指定Object的元信息
     *
     * @param  string $bucketName 源Bucket名称
     * @param  string $key 存储的key（文件路径和文件名）
     * @return object 元信息
     */
    public static function getObjectMeta($bucketName, $osskey)
    {
        $oss = new OSS();
        return $oss->ossClient->getObjectMeta($bucketName, $osskey);
    }
}