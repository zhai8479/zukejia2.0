<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserIntegralLogRepository;
use App\Models\UserIntegralLog;
use App\Validators\UserIntegralLogValidator;

/**
 * Class UserIntegralLogRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserIntegralLogRepositoryEloquent extends BaseRepository implements UserIntegralLogRepository
{
    protected $fieldSearchable = [
        'id',
        'num',
        'type',
        'in_out',
        'created_at'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserIntegralLog::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return UserIntegralLogValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 添加一条记录
     * @param $user_id
     * @param $num
     * @param $type
     * @param $in_out
     * @param null $admin_id
     * @param string $admin_note
     * @return UserIntegralLog
     * @throws Exception
     */
    public function log($user_id, $num, $type, $in_out, $admin_id = null, $admin_note = null)
    {
        if (!User::whereId($user_id)->exists()) {
            throw new Exception('user_id 对应用户不存在');
        }
        if (!is_int($num) || $num <= 0) {
            throw new Exception('积分数量num必须为正整数');
        }
        if (!is_int($type)) {
            throw new Exception('非法的type值');
        }
        if (!is_int($in_out)) {
            throw new Exception('非法的in_out值');
        }
        if (!empty($admin_id) && $admin_id <= 0) {
            throw new Exception('非法的admin_id值');
        }
        if (null !== $admin_note && !is_string($admin_note)) {
            throw new Exception('非法的admin_note值');
        }
        $create = [
            'user_id' => $user_id,
            'num' => $num,
            'type' => $type,
            'in_out' => $in_out,
        ];
        if ($admin_id) {
            $create['admin_id'] = $admin_id;
        }
        if ($admin_note) {
            $create['admin_note'] = $admin_note;
        }
        /**
         * @var UserIntegralLog $integral
         */
        $integral = $this->create($create);
        return $integral;
    }
}
