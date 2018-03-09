<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderRefundRepository;
use App\Models\OrderRefund;
use App\Validators\OrderRefundValidator;

/**
 * Class OrderRefundRepositoryEloquent
 * @package namespace App\Repositories;
 */
class OrderRefundRepositoryEloquent extends BaseRepository implements OrderRefundRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderRefund::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return OrderRefundValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
