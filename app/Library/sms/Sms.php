<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/5/31 14:08
 */

namespace App\Library\sms;

use App\Models\AliyunSms;

class Sms extends \AliyunSms\Sms
{
    public function getCacheKeyPrefix($mobile, $templateKeyName)
    {
        // 获取缓存键值
        return $templateKeyName . '_' . $mobile;
    }

    /**
     * 从缓存中读验证码
     * @param $mobile
     * @param $templateKeyName
     * @return bool|string
     */
    public function getCacheCode($mobile, $templateKeyName)
    {
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        $code = \Cache::get($key, '');
        return $code;
    }

    /**
     * 删除缓存验证码
     * @param $mobile
     * @param $templateKeyName
     */
    public function delCacheCode($mobile, $templateKeyName)
    {
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        \Cache::forget($key);
    }

    /**
     * 验证验证码是否正确
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     * @param bool $isDelete
     * @return bool
     */
    public function checkCode($mobile, $templateKeyName, $code, $isDelete = true)
    {
        // 验证code是否正确
        if (empty($mobile) || empty($code)) return false;
        $cache_code = $this->getCacheCode($mobile,$templateKeyName);
        if (empty($cache_code)) {
            return false;
        } else {
            if ($code == $cache_code) {
                if ($isDelete) $this->delCacheCode($mobile, $templateKeyName);
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * 保存验证码
     * @param $mobile
     * @param $templateKeyName
     * @param $code
     */
    public function saveCacheCode($mobile, $templateKeyName, $code)
    {
        // 保存code到缓存中
        $key = $this->getCacheKeyPrefix($mobile, $templateKeyName);
        $ttl = config('aliyunsms.ttl.' . $templateKeyName, null);
        if (empty($ttl)) {
            $ttl = $this::DEFAULT_TTL;
        }
        \Cache::put($key, $code, $ttl);
    }

    /**
     * 发送完验证码后执行方法
     * @param array $data 短信参数
     * @param string $mobile 手机号
     * @param object $result 发送结果
     * @param string $template_key_name 模板名称
     * @param string $template_id 模板id
     * @param string $message_id 消息id
     */
    function sendDo($data, $mobile, $result, $template_key_name, $template_id, $message_id)
    {
        \Log::info('sms', func_get_args());
        // 记录到数据库中
        AliyunSms::create([
            'to' => $mobile,
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'temp_id' => $template_id,
            'ip' => request()->ip(),
            'result_info' => json_encode($result, JSON_UNESCAPED_UNICODE),
            'message_id' => $message_id,
        ]);
    }
}