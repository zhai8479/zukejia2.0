<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/11 14:24
 */

namespace App\Library;

use OSS\OssClient;

class Recommend
{

    private $ossClient;
    public function __construct($isInternal = false)
    {
        $this->ossClient = new OssClient(config('alioss.AccessKeyId'), config('alioss.AccessKeySecret'),  config('alioss.ossServerInternal'), false);
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
    public function uploadFile($bucketName,$object, $file)
    {
        $oss = new OSS(false); // 上传文件使用内网，免流量费
        $res = $oss->ossClient->uploadFile($bucketName,$object, $file, $options = NULL);
        return $res;
    }


    /**
     * 由user_id 生成邀请码
     * @param $user_id
     * @return string
     */
    public static function create_code($user_id) {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $user_id;
        $code = '';
        while ( $num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod].$code;
        }
        if(empty($code[3]))
            $code = str_pad($code,4,'0',STR_PAD_LEFT);
        return $code;
    }

    /**
     * 由邀请码解出user_id
     * @param $code
     * @return bool|int
     */
    public static function decode($code) {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        if (strrpos($code, '0') !== false)
            $code = substr($code, strrpos($code, '0')+1);
        $len = strlen($code);
        $code = strrev($code);
        $num = 0;
        for ($i=0; $i < $len; $i++) {
            $num += strpos($source_string, $code[$i]) * pow(35, $i);
        }
        return $num;
    }
}