<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderCheckInUser;
use App\Validators\OrderCheckInUserValidator;
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
class OrderCheckInUserRepositoryEloquent extends BaseRepository implements OrderCheckInUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderCheckInUser::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {
        return OrderCheckInUserValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
