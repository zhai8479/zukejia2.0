<?php

namespace App\Repositories;

use App\Models\Order;
use App\Validators\OrderValidator;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderInvoiceLogRepository;
use App\Models\OrderInvoiceLog;
use App\Validators\OrderInvoiceLogValidator;

/**
 * Class OrderInvoiceLogRepositoryEloquent
 * @package namespace App\Repositories;
 */
class OrderRepositoryEloquent extends BaseRepository implements OrderRepository
{
    protected $fieldSearchable = [
        'order_no',
        'apartment_id',
        'status',
        'start_date',
        'end_date',
        'rent_type',
        'need_invoice',
        'pay_channel',
        'pay_status',
        'is_refunds',
        'created_at',
        'updated_at',
        'order_pay_no'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Order::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
//        return OrderValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 生成一个订单号
     */
    public function generate_order_no()
    {
        if (function_exists('dk_get_next_id')) {
            return dk_get_next_id();
        } else {
            return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        }
    }

    /**
     * 计算最终应支付金额
     * @param integer $rental_price     租金
     * @param integer $rental_deposit   押金
     * @param integer $coupons_money    优惠卷抵扣金额
     * @param integer $activity_money   活动抵扣金额
     * @return integer
     */
    public function count_pay_money($rental_price, $rental_deposit, $coupons_money, $activity_money)
    {
        $result = $rental_price + $rental_deposit - $coupons_money - $activity_money;
        return $result<0?0:$result;
    }

    /**
     * 获取住房间天数或月数
     * @param string $start_date
     * @param string $end_date
     * @param integer $rent_type 住房类型 1: 短租， 2：长租
     * @return int 短租返回天数，长租返回月数
     * @throws \Exception
     */
    public function count_housing_numbers($start_date, $end_date, $rent_type)
    {
        $start =  strtotime($start_date . ' -1 day');
        $end = strtotime($end_date);
        $day = ceil(abs($end - $start)/86400);
        if ($rent_type == 2) {
            // 按月
            return intval($day / 31);
        } elseif ($rent_type == 1) {
            // 按日
            return $day;
        } else {
            throw new \Exception('rent_type 类型错误');
        }
    }

    public function count_day($start_day, $end_day)
    {
        return ceil(abs(strtotime($end_day) - strtotime($start_day))/86400);
    }
}
