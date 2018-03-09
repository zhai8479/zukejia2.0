<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AliyunSms
 *
 * @property int $id
 * @property string $to 接收短信手机号码
 * @property string $temp_id 模板id
 * @property string $data 传输数据
 * @property string $ip 请求数据的ip地址
 * @property string $result_info 返回消息
 * @property string $message_id 消息id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereResultInfo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereTempId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\AliyunSms whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AliyunSms extends Model
{
    protected $table = 'aliyun_sms';

    public $timestamps = true;

    protected $guarded = [];
}
