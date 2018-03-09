<?php

namespace App\Repositories;

use App\Presenters\UserVoucherPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserVoucherRepository;
use App\Models\UserVoucher;

/**
 * Class UserVoucherRepositoryEloquent
 * @package namespace App\Repositories;
 *
 */
class UserVoucherRepositoryEloquent extends BaseRepository implements UserVoucherRepository
{

    protected $fieldSearchable = [
        'name',
        'is_use',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserVoucher::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function presenter()
    {
//        return UserVoucherPresenter::class;
    }

    /**
     * 添加一张代金卷
     * @param $user_id
     * @param $voucher_id
     * @return bool
     * @throws \Exception
     */
    public function add_voucher($user_id, $voucher_id)
    {
        $voucher = \DB::table('vouchers')->find($voucher_id);
        if (empty($voucher)) throw new \Exception("id: $voucher_id 代金卷不存在于表 vouchers");

        // 计算时间
        if (isset($voucher->effective_day)) {
            $effective_day = $voucher->effective_day + 1;
            $start_time = date_create(date('Y-m-d'));
            $end_time = date_create(date('Y-m-d', strtotime(date('Y-m-d') . " +$effective_day days")));
        } else {
            $start_time = $voucher->start_time;
            $end_time = $voucher->end_time;
        }
        $this->create([
            'user_id' => $user_id,
            'name' => $voucher->name,
            'desc' => $voucher->desc,
            'rules' => $voucher->rules,
            'scheme_id' => $voucher->scheme_id,
            'is_use' => UserVoucher::NOT_USE,
            'start_time' => $start_time,
            'end_time' => $end_time
        ]);
        return true;
    }

    /**
     * 注册赠送代金卷
     * @param $user_id
     * @return bool
     */
    public function register_add_voucher($user_id)
    {
        \DB::beginTransaction();
        try {
            $this->add_voucher($user_id, 1);
            $this->add_voucher($user_id, 2);
            $this->add_voucher($user_id, 2);
            $this->add_voucher($user_id, 3);
            $this->add_voucher($user_id, 3);
            \DB::commit();
            return true;
        } catch (\Exception $exception) {
            \DB::rollBack();
            \Log::error('赠送代金卷失败', [__FILE__, __LINE__, __FUNCTION__, $exception->getMessage()]);
            return false;
        }
    }

    /**
     * 邀请赠送代金卷
     * @param $user_id
     * @return bool
     */
    public function recommend_add_voucher($user_id)
    {
        \DB::beginTransaction();
        try {
            $this->add_voucher($user_id, 1);
            $this->add_voucher($user_id, 2);
            $this->add_voucher($user_id, 2);
            $this->add_voucher($user_id, 3);
            $this->add_voucher($user_id, 3);
            \DB::commit();
            return true;
        } catch (\Exception $exception) {
            \DB::rollBack();
            \Log::error('赠送代金卷失败', [__FILE__, __LINE__, __FUNCTION__, $exception->getMessage()]);
            return false;
        }
    }

    /**
     * 判定规则
     * @param $rules
     * @param integer $money
     * @param integer $type 0: 短租, 1: 长租
     * @return bool
     */
    public function check_rules($rules, $money, $type)
    {
        if (empty($rules)) return true;
        $rule_arr = explode(',', $rules);
        foreach ($rule_arr as $rule_id) {
            $rule = \DB::table('vouchers_rules')->find($rule_id);
            if ($rule->type == 1) {
                // 满额要求
                if ($rule->val > $money) return false;
            }
            if ($rule->type == 2) {
                // 租房类型要求
                if ($rule->val != $type) return false;
            }
        }
        return true;
    }
}
