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

    /**
     * 初始化
     *
     * @param string $bucketName （使用仓库名称）
     *
     * @return string
     */
    public function __construct($isInternal = false)
    {
        $this->ossClient = new OssClient(config('alioss.AccessKeyId'), config('alioss.AccessKeySecret'),  config('alioss.ossServerInternal'), false);
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




    /**
     * 上传本地文件
     *
     * @param string $bucket bucket名称
     * @param string $object object名称
     * @param string $file 本地文件路径
     * @param array $options
     * @return null
     * @throws OssException
     */
    public static function uploadFile($bucketName,$object, $file)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费
        $res = $oss->ossClient->uploadFile($bucketName,$object, $file, $options = NULL);
        return $res;
    }



    /**
     * 删除存储在oss中的文件
     *
     * @param string $ossKey 存储的key（文件路径和文件名）
     * @return
     */
    public static function deleteObject($bucketName,$ossKey)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费

        return $oss->ossClient->deleteObject($bucketName, $ossKey);
    }

    /**
     * 拷贝一个在OSS上已经存在的object成另外一个object
     *
     * @param string $fromBucket 源bucket名称
     * @param string $fromObject 源object名称
     * @param string $toBucket 目标bucket名称
     * @param string $toObject 目标object名称
     * @param array $options
     * @return null
     * @throws OssException
     */
    public function copyObject($fromBucket, $fromObject, $toBucket, $toObject)
    {
        $oss = new OSS(true); // 上传文件使用内网，免流量费

        return $oss->ossClient->copyObject($fromBucket, $fromObject, $toBucket, $toObject);
    }




    /**
     * 创建bucket，默认创建的bucket的ACL是OssClient::OSS_ACL_TYPE_PRIVATE
     *
     * @param string $bucket
     * @param string $acl
     * @param array $options
     * @param string $storageType
     * @return null
     */
    public static function createBucket($bucketName)
    {
        $oss = new OSS();
        return $oss->ossClient->createBucket($bucketName);
    }


}