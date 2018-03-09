<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\OrderInvoiceLogRepository;
use App\Models\OrderInvoiceLog;
use App\Validators\OrderInvoiceLogValidator;

/**
 * Class OrderInvoiceLogRepositoryEloquent
 * @package namespace App\Repositories;
 */
class OrderInvoiceLogRepositoryEloquent extends BaseRepository implements OrderInvoiceLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return OrderInvoiceLog::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return OrderInvoiceLogValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
