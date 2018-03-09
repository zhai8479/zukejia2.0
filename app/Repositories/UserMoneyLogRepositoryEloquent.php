<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserMoneyLogRepository;
use App\Models\UserMoneyLog;
use App\Validators\UserMoneyLogValidator;

/**
 * Class UserMoneyLogRepositoryEloquent
 * @package namespace App\Repositories;
 */
class UserMoneyLogRepositoryEloquent extends BaseRepository implements UserMoneyLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserMoneyLog::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return UserMoneyLogValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
