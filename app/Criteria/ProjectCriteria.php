<?php
/**
 * Created by PhpStorm.
 * User: 84790
 * Date: 2018/3/24/0024
 * Time: 下午 12:01
 */

namespace App\Criteria;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;
use App\Models\Project;

/**
 * Class MyCriteria
 * @package namespace App\Criteria;
 */

class ProjectCriteria implements CriteriaInterface
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
        return $model->where('is_show', Project::IS_SHOW);
    }
}