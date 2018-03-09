<?php

namespace App\Repositories;

use App\Models\OrderRefund;
use App\Models\UserMoneyLog;
use App\Presenters\UserMoneyPresenter;
use App\Repositories\UserMoneyLogRepository;
use Dingo\Api\Http\Request;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserMoneyRepository;
use App\Models\UserMoney;
use App\Validators\UserMoneyValidator;

/**
 * Class UserMoneyRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserMoneyRepositoryEloquent extends BaseRepository implements UserMoneyRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserMoney::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 验证规则设定
     * @return string
     */
    public function validator()
    {
        return UserMoneyValidator::class;
    }

    /**
     * 设置返回格式
     * @return string
     */
    public function presenter()
    {
//        return UserMoneyPresenter::class;
    }

    /**
     * 用户支付操作
     * @param integer $pay_money
     * @param integer $user_id
     * @param string $description
     * @return bool
     * @throws \Exception
     */
    public function pay($pay_money, $user_id, $description = '')
    {
        // 支付
        // 记录日志
        /**
         * @var $userMoneyLogRepository UserMoneyLogRepositoryEloquent
         * @var $userMoneyRepository UserMoneyRepositoryEloquent
         * @var $userMoney UserMoney
         */
        $userMoneyLogRepository = app(UserMoneyLogRepository::class);
        $userMoneyRepository = app(UserMoneyRepository::class);
        if (empty($description)) $description = '支付房租与押金';

        $userMoney = $userMoneyRepository->firstOrCreate(['user_id' => $user_id]);
        if (empty($userMoney)) throw new \Exception('账户初始化失败');
        if ($userMoney->money < $pay_money) throw new \Exception('余额不足，无法完成支付');
        \DB::beginTransaction();
        try {
            $affect = \DB::update("update user_money set money = money - ? where money >= ? and user_id = ?", [$pay_money, $pay_money, $user_id]);
            if ($pay_money > 0 && $affect != 1) throw new \Exception('支付扣款失败');
            $userMoneyLogRepository->create([
                'user_id' => $user_id,
                'type' => 1,            // 类型为消费
                'description' => $description,
                'in_out' => 1,          // 类型为支出
                'money' => $pay_money,
            ]);
            \DB::commit();
            return true;
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
    }

    /**
     * 退款到余额操作
     * order_refunds 表
     * user_money 表
     * user_money_log 表
     * orders 表
     * @param $user_id
     * @param $order_id
     * @param $refund_type
     * @param $refund_housing_numbers
     * @param $refundMoney
     * @return bool
     * @throws \Exception
     */
    public function refund($user_id, $order_id, $refund_type, $refund_housing_numbers, $refundMoney, $desc = '')
    {
        /**
         * @var $orderRepository OrderRepositoryEloquent
         */
        $orderRepository = app(OrderRepository::class);
        $description = empty($desc)?'退款':$desc;
        try {
            // 增加退款记录
            OrderRefund::create([
                'order_id' => $order_id,
                'user_id' => $user_id,
                'refund_type' => $refund_type,
                'refund_housing_numbers' => $refund_housing_numbers,
                'refund_no' => $orderRepository->generate_order_no(),
                'refund_status' => 2,
                'refund_over_at' => date('Y-m-d H:i:s'),
                'ip' => Request::capture()->ip(),
                'money' => $refundMoney,
            ]);
            // 增加用户余额
            $userMoney = UserMoney::firstOrCreate(['user_id' => $user_id]);
            $affect = \DB::update("update user_money set money = money + ? where user_id = ?", [$refundMoney, $user_id]);
            if ($affect != 1) throw new \Exception('增加用户余额失败');
            // 记录增加操作
            UserMoneyLog::create([
                'user_id' => $user_id,
                'type' => 6,            // 类型为退款
                'description' => $description,
                'in_out' => 0,          // 类型为收入
                'money' => $refundMoney,
            ]);
            return true;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }
}
