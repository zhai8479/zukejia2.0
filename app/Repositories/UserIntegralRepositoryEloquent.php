<?php

namespace App\Repositories;

use App\Models\UserIntegralLog;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserIntegralRepository;
use App\Models\UserIntegral;
use App\Validators\UserIntegralValidator;

/**
 * Class UserIntegralRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserIntegralRepositoryEloquent extends BaseRepository implements UserIntegralRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserIntegral::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return UserIntegralValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 修改用户积分
     *
     * @param $user_id
     * @param $integral
     * @param $type
     * @param $in_out
     * @internal param null $admin_id
     * @internal param null $admin_note
     * @return bool
     */
    public function change_integral($user_id, $integral, $type, $in_out)
    {
        /**
         * @var $userIntegralLogRepository UserIntegralLogRepositoryEloquent
         */
        $userIntegralLogRepository = app(UserIntegralLogRepository::class);
        $integral = abs($integral);
        \DB::beginTransaction();
        try {
            $userIntegral = $this->firstOrCreate(['user_id' => $user_id]);
            if (empty($userIntegral)) throw new \Exception('数据库错误');
            if ($in_out == UserIntegralLog::IN) {
                $affect = \DB::update('update user_integrals set integral = integral + ? where user_id = ?', [$integral, $user_id]);
            } else {
                $affect = \DB::update('update user_integrals set integral = integral - ? where user_id = ? AND integral >= ?', [$integral, $user_id, $integral]);
            }
            if ($affect != 1) throw new \Exception('数据库错误');
            $log = $userIntegralLogRepository->log($user_id, $integral, $type, $in_out);
            if (empty($log)) throw new \Exception('积分变更日志记录失败');
            \DB::commit();
            return true;
        } catch (\Exception $exception) {
            \Log::error('修改用户积分失败', [__FILE__, __LINE__, __FUNCTION__, func_get_args()]);
            \DB::rollBack();
            return false;
        }
    }

    /**
     * 注册得积分
     *
     * @param $user_id
     *
     * @internal param $integral
     *
     * @return bool
     */
    public function register_add_integral($user_id)
    {
        $type = 1;
        $integral = config('integral.detail.register');
        return $this->change_integral($user_id, $integral, $type, UserIntegralLog::IN);
    }

    /**
     * 分享注册得积分
     *
     * @param $user_id
     *
     * @internal param $integral
     *
     * @return bool
     */
    public function share_register_add_integral($user_id)
    {
        $type = 2;
        $integral = config('integral.detail.recommend');
        return $this->change_integral($user_id, $integral, $type, UserIntegralLog::IN);
    }

    /**
     * 登陆得积分
     * 
     * @param $user_id
     */
    public function login_add_integral($user_id)
    {
        // todo 需要计算
    }

    /**
     * 绑定得积分
     * @param $user_id
     * @param $bind_type 6: 绑定微信, 7: 绑定邮箱
     * @return bool
     */
    public function bind_add_integral($user_id, $bind_type)
    {
         // 需要区分绑定类型
        if ($bind_type == UserIntegralLog::BIND_EMAIL) {
            // 微信绑定
            $type = 6;
            $integral = config('integral.detail.bind.wechat');
        }
        if ($bind_type == UserIntegralLog::BIND_WECHAT) {
            // 邮箱绑定
            $type = 7;
            $integral = config('integral.detail.bind.email');
        }
        if (isset($type)) {
            return $this->change_integral($user_id, $integral, $type, UserIntegralLog::IN);
        } else {
            return false;
        }
    }

    /**
     * 使用积分抵扣
     *
     * @param $user_id
     *
     * @param $num
     *
     * @return bool
     */
    public function use_integral($user_id, $num)
    {
        $type = 4;
        $integral = config('integral.detail.pay') * $num;
        return $this->change_integral($user_id, $integral, $type, UserIntegralLog::OUT);
    }
}
