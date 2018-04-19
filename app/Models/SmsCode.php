<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsCode extends Model
{

    /**
     * 短信验证码过期时间（秒）
     */
    const EXPIRES_IN = 600;

    protected $table = 'sms_codes';

    protected $fillable = ['mobile', 'code', 'status'];
}
