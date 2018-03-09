<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\SignUpRepository;
use App\Models\SignUp;
use App\Validators\SignUpValidator;

/**
 * Class SignUpRepositoryEloquent
 * @package namespace App\Repositories;
 */
class SignUpRepositoryEloquent extends BaseRepository implements SignUpRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SignUp::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return SignUpValidator::class;
    }


    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
}
