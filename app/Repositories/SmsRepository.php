<?php
/**
 * Summary
 *
 * Discription
 *
 * @package App\Repositories
 * @author  Leo <jiangwenhua@yoyohr.com>
 *
 */

namespace App\Repositories;

use App\Models\SmsCode;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Carbon\Carbon;

class SmsRepository
{
    public function sendSmsCode($mobile)
    {
        $smsCode = $this->generateCode(6);
        $message = '验证码短信:' . $smsCode ;
        $success = $this->sendSmsCodeSuccess($mobile, $smsCode);
        $exception = $this->sendSmsCodeException($mobile, $smsCode);
        return $this->sendAsyncRequest($mobile,$message, $success, $exception);
        return true;
    }

    public function generateCode($length = 6)
    {
        return substr(str_shuffle("01234567890123456789"), 0, $length);
    }

    public function sendSmsCodeSuccess($mobile, $smsCode)
    {
        return function (ResponseInterface $res) use ($mobile, $smsCode) {
            $status = $res->getStatusCode();
            $this->saveSmsCode($mobile, $smsCode, $status);
        };
    }

    public function sendSmsCodeException($mobile, $smsCode)
    {
        return function (RequestException $e) use ($mobile, $smsCode) {
            $status = $e->getCode();
            $this->saveSmsCode($mobile, $smsCode, $status);
        };
    }

    /**
     * 保存验证码到数据库
     * @param $mobile
     * @param $smsCode
     * @param $status
     * @return bool
     * @date 2016-12-1 11:21
     */
    public function saveSmsCode($mobile, $smsCode, $status)
    {
        // 保存验证码，不能更改，只能增加记录
        $sms = SmsCode::create([
            'mobile' => $mobile,
            'code' => $smsCode,
            'status' => $status
        ]);
        if ($sms) {
            return true;
        }
        return false;
    }

    /**
     * 验证短信验证码
     * @param $mobile
     * @param $smsCode
     * @return bool
     * @updated_at 2016-12-1 11:20
     */
    public function verifySmsCode($mobile, $smsCode)
    {
        // 短信验证码取表里成功发送短信的最近的一条记录
        $sms = SmsCode::query()
            ->where('mobile', $mobile)
            ->where('status', 200)
            ->orderBy('id', 'desc')
            ->first();
        // 如果存在并且验证码一致，才判断是否过期
        if ($sms && $sms->code == $smsCode) {
            if (time() - strtotime($sms->updated_at) < SmsCode::EXPIRES_IN) {
                return true;
            }
        }
        return false;
    }

    public function sendAsyncRequest($mobile,$content, $success, $failure)
    {

        $url = $this->getUrl($mobile,$content);
        $client = new Client();
        $data = $this->getParameter($mobile,$content);
        return env('SMS_REQUEST_URL');
        return $promise = $client->requestAsync('POST', env('SMS_REQUEST_URL'),$data);
        $promise->then($success, $failure);
        $promise->wait();
        return 1111111111;
    }

    public function sendSyncRequest($mobile,$content)
    {
        $client = new Client();
        $url = $this->getUrl($mobile,$content);
        $data = $this->getParameter($mobile,$content);
        $response = $client->request('POST', env('SMS_REQUEST_URL').$url, $data);
        return ($response->getStatusCode() == 200);
    }

    public function getUrl($mobile,$content)
    {
        $user_id = 'zkj';
        $pwd = 'zkj_zkj123';
        ini_set('date.timezone','Asia/Shanghai');
        $time_zoe = date("YmdHis");
        $str = $user_id.$pwd.$time_zoe;
        return '?action=send&userid='.$user_id.'&timestamp='.$time_zoe.'&sign='.md5($str).'&mobile='.$mobile.'&content='.$content.'&sendTime=&extno=';
    }

    public function getParameter($mobile,$content)
    {
        $user_id = 'zkj';
        $pwd = 'zkj_zkj123';
        ini_set('date.timezone','Asia/Shanghai');
        $time_zoe = date("YmdHis");
        $str = $user_id.$pwd.$time_zoe;
        return ['action'=>'send','userid'=>'zkj','timestamp'=>$time_zoe,'sign'=>md5($str),'mobile'=>$mobile,'content'=>$content,'sendTime'=>'','extno'=>''];
    }
}