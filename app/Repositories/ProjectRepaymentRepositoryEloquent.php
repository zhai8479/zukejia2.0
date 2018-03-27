<?php

namespace App\Repositories;

use App\Criteria\MyCriteria;
use App\Models\ProjectRepayment;
use App\Presenters\ProjectRepaymentPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class ProjectRepaymentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ProjectRepaymentRepositoryEloquent extends BaseRepository implements ProjectRepaymentRepository
{
    protected $fieldSearchable = [
        'investment_id',
        'issue_num',
        'money',
        'principal',
        'interest',
        'is_repayment',
        'estimate_time',
        'real_time timestamp'
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProjectRepayment::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
        $this->pushCriteria(MyCriteria::class);
    }

    public function presenter()
    {
        return ProjectRepaymentPresenter::class;
    }

    /**
     * 检查并执行还款操作
     */
    public function check_do_repayment()
    {
        $repayments = ProjectRepayment::where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->where('estimate_time', '<=', date_create())
            ->get();
        \Log::debug(__FUNCTION__, ['wait repayment num is ' . $repayments->count()]);
        $repayments->reject(function ($repayment) {
            app(UserMoneyRepository::class)->repayment($repayment);
        });
        \Log::debug(__FUNCTION__, ['repayment is over ' . $repayments->count()]);
    }
}
