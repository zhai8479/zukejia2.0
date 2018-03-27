<?php

namespace App\Repositories;

use App\Criteria\ProjectCriteria;
use App\Presenters\ProjectPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

use App\Models\Project;
use App\Validators\ProjectValidator;

/**
 * Class ProjectRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ProjectRepositoryEloquent extends BaseRepository implements ProjectRepository
{
    protected $fieldSearchable = [
        'name' => 'like',
        'money',
        'status',
        'issue_total_num',
        'weight',
        'characteristic' => 'like',
        'house_address' => 'like',
        'house_status',
        'house_competitive_power' => 'like',
        'house_management_status',
        'risk_assessment' => 'like',
        'safeguard_measures' => 'like',
        'guarantor' => 'like',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Project::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProjectValidator::class;
    }

    public function presenter()
    {
        return ProjectPresenter::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
        $this->pushCriteria(ProjectCriteria::class);
    }

    /**
     * 检测项目状态并可能修改状态
     */
    public function check_project_status_and_change()
    {
        \Log::info('-------------' . __FUNCTION__ . ' start -----------');
        $now_time = date('Y-m-d H:i:s');
        $affect = Project::query()->where('status', Project::STATUS_WAIT_START)
            ->where('start_at', '<=', $now_time)
            ->where('end_at', '>', $now_time)
            ->update([
                'status' => Project::STATUS_PROCESS
            ]);
        \Log::info(__FUNCTION__, ['change wait start to process is ' . $affect . ' num']);

        $affect = Project::query()->where('status', Project::STATUS_PROCESS)
            ->where('end_at', '<', $now_time)
            ->update([
                'status' => Project::STATUS_OVERTIME
            ]);

        \Log::info(__FUNCTION__, ['change process to overtime is ' . $affect . ' num']);
        \Log::info('-------------' . __FUNCTION__ . ' end -----------');
    }
}
