<?php

namespace App\Repositories;

use App\Presenters\ApartmentPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApartmentRepository;
use App\Models\Apartment;
use App\Validators\ApartmentValidator;

/**
 * Class ApartmentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ApartmentRepositoryEloquent extends BaseRepository implements ApartmentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Apartment::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ApartmentValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * 设置返回格式
     * @return string
     */
    public function presenter()
    {
        return ApartmentPresenter::class;
    }
}
