<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class UserMoneyLog extends Model implements Transformable
{
    use TransformableTrait;

    public static $logTypes = [
        1 => '消费',
        2 => '支付宝充值',
        3 => '微信充值',
        4 => '银行卡充值',
        5 => '管理员调节账户',
        6 => '退款',
    ];

    public static $in_out_list = [
        0 => '收入',
        1 => '支出',
    ];

    protected $guarded = [];

    public $timestamps = true;

}
