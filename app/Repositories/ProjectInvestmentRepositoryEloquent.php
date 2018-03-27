<?php

namespace App\Repositories;

use App\Criteria\MyCriteria;
use App\Models\Project;
use App\Presenters\ProjectInvestmentPresenter;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\ProjectInvestment;
use App\Validators\ProjectInvestmentValidator;

/**
 * Class ProjectInvestmentRepositoryEloquent
 * @package namespace App\Repositories;
 */
class ProjectInvestmentRepositoryEloquent extends BaseRepository implements ProjectInvestmentRepository
{
    protected $fieldSearchable = [
        'project_id',
        'no_num' => 'like',
        'status',
        'now_issue_num',
        'pay_at',
        'end_at',
        'cancel_at',
    ];

    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ProjectInvestment::class;
    }

    /**
    * Specify Validator class name
    *
    * @return mixed
    */
    public function validator()
    {

        return ProjectInvestmentValidator::class;
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
        return ProjectInvestmentPresenter::class;
    }

    /**
     * 取消超时订单 15 min
     */
    public function cancel_overtime_order()
    {
        $affect = ProjectInvestment::where('status', ProjectInvestment::STATUS_WAIT_PAY)
            ->where('created_at', '<', date_create(date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' -15 minutes'))))
            ->update(['status' => ProjectInvestment::STATUS_CANCEL_NO_PAY, 'cancel_at' => date_create()]);
        \Log::debug(__FUNCTION__, ['affect is ' . $affect]);
    }

    /**
     * 计算应还利息
     *
     * 利息 =（租赁价格-搜房价格）*0.8
     *
     * 尾期利息 = [(租赁价格-搜房价格)*0.8 ] *(尾期天数/30)
     *
     * @param Project $project
     * @return float
     */
    public function compute_interest(Project $project, $is_last_day)
    {
        if ($is_last_day && $project->issue_day_num != 0) {
            $issue_day_num = $project->issue_day_num;
        } else {
            $issue_day_num = 30;
        }
        return ($project->rental_money - $project->collect_money) * 0.8 * $issue_day_num / 30;
    }

    /**
     * 计算应还本金
     *
     * 期本金 = 收房价格
     *
     * 尾期本金 = 标的价格 - 期数 * 收房价格（本金）
     *
     * @param Project $project
     * @return float|int
     */
    public function compute_principal(Project $project, $is_last_day)
    {
        if ($is_last_day && $project->issue_day_num != 0) {
            return $project->money - $project->collect_money * $project->issue_total_num;
        }

        return $project->collect_money;
    }

    /**
     * 计算还款时间
     * @param \DateTime $time
     * @return \DateTime|false
     */
    public function compute_repayment_date(\DateTime $time)
    {
        if ((int)$time->format('d') <= 15) {
            // 小于等于15号的, 按照15号算
            $estimate_time = date('Y-m-d H:i:s', strtotime("{$time->format('Y-m-')}15 {$time->format('H:i:s')}"));

        } elseif ((int)$time->format('d') <= 30) {
            // 大于15, 小于等于30的按照30号算
            $estimate_time = date('Y-m-d H:i:s', strtotime("{$time->format('Y-m-')}30 {$time->format('H:i:s')}"));
        } else {
            // 大于30号的, 按照下个月15号算
            $estimate_time = date('Y-m-d H:i:s', strtotime("{$time->format('Y-')}" . ((int)$time->format('m') + 1)."-15 {$time->format('H:i:s')}"));
        }
        return date_create($estimate_time);
    }
}
