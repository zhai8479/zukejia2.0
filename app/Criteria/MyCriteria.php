<?php

namespace App\Criteria;

use Dingo\Api\Auth\Auth;
use Dingo\Api\Http\Request;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class MyCriteria
 * @package namespace App\Criteria;
 */
class MyCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $auth = app(Auth::class);
        return $model->where('user_id', $auth->user()->id);
    }
}
