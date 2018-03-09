<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    // 订单状态列表
    public static $order_status = [
        1 => '订单已提交',
        2 => '订单已支付',
        3 => '已退房',
        4 => '订单完成',
        5 => '取消已支付订单',
        6 => '取消未支付订单',
        7 => '订单超时',        // 下单后超过两小时未支付
        8 => '管理员取消订单退钱',
        9 => '管理员取消订单不退钱',
    ];

    // 支付状态列表
    public static $pay_status = [
        1 => '待支付',
        2 => '支付中',
        3 => '支付完成'
    ];

    // 订单支付渠道列表
    public static $pay_channels = [
        1 => '余额',
        2 => '支付宝',
        3 => '微信',
        4 => '银行卡'
    ];

    // 租房类型
    public static $rent_types = [
        1 => '短租',
        2 => '长租',
        3 => '特价'
    ];

    // 是否退款
    public static $is_refund = [
        0 => '无退款',
        1 => '有退款'
    ];

    // 是否索取发票
    public static $has_invoice = [
        0 => '客户索取发票',
        1 => '否'
    ];

    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    use SoftDeletes;

    protected $guarded = [
        'status',
        'pay_channel',
        'external_no',
        'pay_start_at',
        'pay_over_at',
        'pay_status',
        'is_refunds',
        'refunds_total_money',
    ];

    protected $dates = ['deleted_at'];

    public $timestamps = true;

    public function setCheckInUserIdsAttribute($value)
    {
        $this->attributes['check_in_user_ids'] = implode(',', $value);
    }

    public function getCheckInUserIdsAttribute($value)
    {
        return explode(',', $value);
    }


}
