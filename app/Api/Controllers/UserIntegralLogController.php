<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/11/3
 * Time: 15:57
 */

namespace App\Api\Controllers;


use App\Criteria\MyCriteria;
use App\Models\UserIntegralLog;
use App\Models\UserMoneyLog;
use App\Repositories\UserIntegralLogRepository;
use App\Repositories\UserIntegralLogRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;

/**
 * Class UserIntegralController
 * @package App\Api\Controllers
 *
 * @Resource("UserIntegralLog", uri="integral_log")
 *
 */
class UserIntegralLogController extends BaseController
{
    /**
     * @var UserIntegralLogRepositoryEloquent $repository
     */
    protected $repository;

    public function __construct(UserIntegralLogRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 获取积分记录
     *
     *  - return
     *      - data
     *          - id            记录id
     *          - num           变更积分数量
     *          - type          积分变更类型 (1: 注册送积分 2: 邀请好友注册送积分 3: 完成订单送积分 4: 抵扣订单支付积分 5: 管理员调整积分)
     *          - type_str      积分变更类型字符串显示形式
     *          - in_out        收支类型 (0: 支出, 1: 收入)
     *          - admin_id      操作管理员id
     *          - admin_note    管理员备注
     *
     * @Get("index")
     *
     * @return array
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(MyCriteria::class);
        $userMoneyLogs = $this->repository->orderBy('id', 'desc')->all();
        foreach ($userMoneyLogs as $userMoneyLog) {
            $userMoneyLog->type_str = UserIntegralLog::$types[$userMoneyLog->type];
        }
        return $this->array_response($userMoneyLogs);
    }

}