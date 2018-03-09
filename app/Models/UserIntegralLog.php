<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class UserIntegralLog extends Model implements Transformable
{
    use TransformableTrait;

    const IN = 1;   // 收入
    const OUT = 0;  // 支出

    const BIND_EMAIL = 6;
    const BIND_WECHAT = 7;

    protected $fillable = ['user_id', 'type', 'num', 'in_out', 'admin_id', 'admin_note'];

    public $timestamps = true;

    public static $types = [
        1 => '注册送积分',
        2 => '邀请好友注册送积分',
        3 => '完成订单送积分',
        4 => '抵扣订单支付积分',
        5 => '管理员调整积分',
        6 => '绑定微信送积分',
        7 => '绑定邮箱送积分',
    ];

}
