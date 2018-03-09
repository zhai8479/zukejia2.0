<?php

namespace App\Api\Controllers;

use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\ProjectRepayment;
use App\Models\ProjectType;
use App\Models\User;
use App\Models\UserMoney;
use App\Repositories\ProjectInvestmentRepositoryEloquent;
use App\Repositories\ProjectRepaymentRepository;
use App\Repositories\ProjectRepaymentRepositoryEloquent;
use App\Repositories\UserMoneyRepository;
use App\Repositories\UserMoneyRepositoryEloquent;
use Dingo\Api\Http\Request as HttpRequest;
use App\Repositories\ProjectInvestmentRepository;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

/**
 * 投资
 *
 * @Resource("ProjectInvestment", uri="/project/investment")
 *
 * Class ProjectInvestmentsController
 * @package App\Api\Controllers
 *
 */
class ProjectInvestmentsController extends BaseController
{

    /**
     * @var ProjectInvestmentRepositoryEloquent
     */
    protected $repository;

    /**
     * @var ProjectRepaymentRepositoryEloquent
     */
    protected $repaymentRepository;

    public function __construct(ProjectInvestmentRepository $repository, ProjectRepaymentRepository $repaymentRepository)
    {
        $this->repository = $repository;
        $this->repaymentRepository = $repaymentRepository;
    }


    /**
     * 获取项目投资列表
     *
     * - id 主键
     * - project_id 项目id
     * - no_num 流水号
     * - status 状态
     * - status_str 状态对应字符串
     * - pay_at 支付时间
     * - now_issue_num 当前所在期
     * - pay_at 支付时间
     * - cancel_at 取消时间
     * - project_info 项目信息
     *     - name 项目名称
     *     - house_address 房屋地址
     *     - issue_total_num 总期数
     *     - issue_day_num   期数外的天数
     *     - money          总金额
     *     - issue_profit_money   每期收益
     *
     * @Get("index")
     *
     * @Parameters({
     *     @Parameter("length", description="限制一页的数据条数", required=false, default="15")
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function index(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);

        $projectInvestments = $this->repository
            ->orderBy('id', 'desc')
            ->paginate($request->input('length', 15));

        return $this->array_response($projectInvestments);
    }

    /**
     * 获取项目投资详情
     *
     * - id 主键
     * - project_id 项目id
     * - no_num 流水号
     * - status 状态
     * - status_str 状态对应字符串
     * - now_issue_num 当前所在期
     * - pay_at 支付时间/投标结束时间1
     * - cancel_at  取消时间
     * - end_at     还款结束时间
     * - project_info 项目信息
     *     - start_at
     *     - end_at
     *     - buy_over_time  投标结束时间
     * - repayment_start_at
     * - repayment_end_at
     * - total_profit           总收益
     * - is_repayment_profit    已还款
     * - wait_repayment_profit  待还款
     *     - name 项目名称
     *     - house_address 房屋地址
     *     - issue_total_num 总期数
     *     - issue_day_num   期数外的天数
     *     - money           投资金额
     *     - issue_profit_money   每期收益
     *     - type_info
     *         - name 类型名称
     *         - repayment_type
     *         - repayment_type_str     计息方式
     *         - guarantee_type
     *         - guarantee_type_str     担保方式
     *         - interest_day           计息延迟天数
     * - now_issue_info
     *     - issue_num 期数
     *     - money 本息
     *     - principal 本金
     *     - interest 利息
     *     - real_time 还款时间
     *     - is_repayment   是否已还款
     *     - estimate_time  预计还款时间
     *
     * @Get("show")
     *
     * @param  int $id
     *
     * @return array
     */
    public function show($id)
    {
        $projectInvestment = $this->repository->find($id);
        $projectRepayments = ProjectRepayment::where('investment_id', $id)->get();
        $total_profit = 0;              // 总收益
        $is_repayment_profit = 0;       // 已还款
        $wait_repayment_profit = 0;     // 等待还款

        /**
         * @param $repayment ProjectRepayment
         */
        $projectRepayments->reject(function ($repayment) use (&$total_profit, &$is_repayment_profit, &$wait_repayment_profit){
            if ($repayment->is_repayment == ProjectRepayment::IS_REPAYMENT) {
                $is_repayment_profit += $repayment->money;
            } else {
                $wait_repayment_profit += $repayment->money;
            }
            $total_profit += $repayment->money;

        });
        $projectInvestment['data']['total_profit'] = $total_profit;
        $projectInvestment['data']['is_repayment_profit'] = $is_repayment_profit;
        $projectInvestment['data']['wait_repayment_profit'] = $wait_repayment_profit;
        $projectInvestment['data']['now_issue_info'] = ProjectRepayment::find($projectInvestment['data']['now_issue_num'] + 1);

        return $this->array_response($projectInvestment);
    }

    /**
     * 创建订单
     *
     * @Post("create_order")
     *
     * @Parameters({
     *      @Parameter("project_id", description="项目id", type="integer", required=true)
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function create_order(HttpRequest $request)
    {

        // 表单验证
        $this->validate($request, [
            'project_id' => 'required|integer|exists:projects,id',
        ]);
        $project_id = $request->input('project_id');
        $project = Project::where('is_show', Project::IS_SHOW)->find($project_id);

        if (empty($project)) return $this->error_response('项目不存在');

        // 判断是否满足创建订单条件
        if (! $project->is_process()) return $this->error_response('项目不为进行中状态');

        // 创建订单
        $projectInvestment = ProjectInvestment::create([
            'project_id' => $project->id,
            'no_num' => $this->generate_order_no(),
            'user_id' => $this->auth->user()->id,
            'status' => ProjectInvestment::STATUS_WAIT_PAY,
        ]);

        return $this->array_response($projectInvestment);

    }

    /**
     * 支付订单
     *
     * @Post("pay_order")
     *
     * @Parameters({
     *      @Parameter("investment_id", description="投资id", required=true, type="integer")
     * })
     *
     * @param HttpRequest $request
     *
     * @return array
     */
    public function pay_order(HttpRequest $request)
    {
        /**
         * @var $userMoneyRepository UserMoneyRepositoryEloquent
         * @var  $investmentRepository ProjectInvestmentRepositoryEloquent
         * @var $investment ProjectInvestment
         */
        // 表单验证
        $this->validate($request, [
            'investment_id' => 'required|integer|exists:project_investments,id'
        ]);
        $id = $request->input('investment_id');
        $user_id = $this->auth->user()->id;
        $investment = ProjectInvestment::where('user_id', $user_id)->find($id);
        if (null === $investment) return $this->error_response('投资不存在');
        $project = Project::where('is_show', Project::IS_SHOW)->find($investment->project_id);
        if (empty($project)) return $this->error_response('项目不存在');
        $type = ProjectType::find($project->type_id);
        if (empty($type)) return $this->error_response('类型不存在');
        $interest_day = $type->interest_day;    // 延迟计息天数
        $pay_money = $project->money;

        $userMoneyRepository = app(UserMoneyRepository::class);
        $userMoneyRepository->firstOrCreate(['user_id' => $user_id]);

        // 判断是否满足支付条件
        if (! $project->is_process()) return $this->error_response('项目不为进行中状态');

        // 执行支付操作

        \DB::beginTransaction();
        try {

            $userMoneyRepository = app(UserMoneyRepository::class);
            $investmentRepository = app(ProjectInvestmentRepository::class);

            // 支付
            $userMoneyRepository->pay($pay_money, $user_id, '购买项目');

            // 修改状态
            $project->status = Project::STATUS_REPAYMENT;
            $investment->status = ProjectInvestment::STATUS_REPAYMENT;
            $now_time = date_create();
            $investment->pay_at = date_create();

            // 生成repayment数据
            $issue_total_num = $project->issue_total_num;
            $issue_day_num = $project->issue_day_num;
            $repayment_time = $now_time->add(date_interval_create_from_date_string($interest_day . ' days'));   // 算入延迟计息时间
            if ($issue_day_num != 0) {
                $issue_total_num ++;
                $has_day = true;
            } else {
                $has_day = false;
            }
            for ($issue_num = 1; $issue_num < $issue_total_num + 1; $issue_num ++) {
                $is_last = ($issue_total_num == $issue_num);
                // 计算预计还款时间
                // 预计还款时间 = 购买时间 + 计息时间 + 期数 * 一期长度
                if ($has_day && $is_last) {
                    // 有余天数且到了最后一天
                    $time = $repayment_time->add(date_interval_create_from_date_string("$issue_day_num days"));
                } else {
                    $time = $repayment_time->add(date_interval_create_from_date_string('30 days'));
                }

                $interest = $investmentRepository->compute_interest($project, $is_last);    // 期利息
                $principal = $investmentRepository->compute_principal($project, $is_last);  // 期本金
                $estimate_time = $investmentRepository->compute_repayment_date($time);      // 还款时间

                if ($issue_num == 1) $investment->repayment_start_at = clone $estimate_time;    // 预计开始还款时间

                $create = [
                    'investment_id' => $id,
                    'user_id' => $user_id,
                    'issue_num' => $issue_num,  // 期数
                    'money' => $interest + $principal,              // 还钱数量
                    'principal' => $principal,          // 本金
                    'interest' => $interest,           // 利息
                    'estimate_time' => $estimate_time,      // 预计还款时间
                ];
                // 增加数据
                ProjectRepayment::create($create);
            }
            $investment->repayment_end_at = clone $estimate_time;
            $project->save();
            $investment->save();
            \DB::commit();
            return $this->array_response($project);
        }catch (\Exception $exception) {
            \DB::rollBack();
            \Log::error('pay_order', [__FILE__, __FUNCTION__, __LINE__, $exception->getMessage()]);
            return $this->error_response($exception->getMessage());
        }
    }

    /**
     * 取消订单
     *
     * @Get("cancel_order")
     *
     * @Parameters({
     *      @Parameter("investment_id", description="投资id", required=true, type="integer")
     * })
     *
     * @param HttpRequest $request
     *
     * @return array
     */
    public function cancel_order(HttpRequest $request)
    {
        $this->validate($request, [
            'investment_id' => 'required|integer|exists:project_investments,id'
        ]);
        $id = $request->input('investment_id');
        $user_id = $this->auth->user()->id;
        $investment = ProjectInvestment::where('user_id', $user_id)->find($id);
        if (empty($investment)) return $this->error_response('订单不存在');
        if ($investment->status != ProjectInvestment::STATUS_WAIT_PAY) return $this->error_response('订单状态不为待支付');

        $investment->status = ProjectInvestment::STATUS_CANCEL;
        $investment->cancel_at = date_create();
        $investment->save();
        return $this->array_response($investment);
    }

    /**
     * 获取还款列表
     *
     * - id
     * - investment_id  投资id
     * - issue_num      期数
     * - money          总额
     * - principal      本金
     * - interest       利息
     * - is_repayment   是否还款
     * - is_repayment_str   是否还款字符串形式
     * - estimate_time  还款时间
     * - project_info
     *     - name  项目名称
     *     - issue_total_num    期数
     *     - issue_day_num      期多余天数
     *
     * @Get("repayment_index")
     *
     * @param HttpRequest $request
     *
     * @return array
     */
    public function repayment_index(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);
        $projectInvestments = $this->repaymentRepository
            ->paginate($request->input('length', 15));

        $repayments = $projectInvestments['data'];
        if ($repayments) {
            foreach ($repayments as &$repayment) {
                $project_id = ProjectInvestment::where('id', $repayment['investment_id'])->value('project_id');
                $repayment['project_info'] = Project::find($project_id, ['name', 'issue_total_num', 'issue_day_num']);
            }
            unset($repayment);
        }
        $projectInvestments['data'] = $repayments;
        return $this->array_response($projectInvestments);
    }

    /**
     * 获取还款详情
     *
     * @Get("repayment_show")
     *
     * @param $id
     *
     * @return array
     */
    public function repayment_show($id)
    {
        /**
         * @var $repayment ProjectRepayment
         */
        $repayment = $this->repaymentRepository->find($id);

        $investment = $this->repository->find($repayment['data']['investment_id']);

        $investment['data']['total_income'] = ProjectRepayment::where('investment_id', $repayment['data']['investment_id'])->sum('money');
        $investment['data']['total_wait_repayment'] = ProjectRepayment::query()
            ->where('investment_id', $repayment['data']['investment_id'])
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->sum('money');
        $investment['data']['total_is_repayment'] = ProjectRepayment::query()
            ->where('investment_id', $repayment['data']['investment_id'])
            ->where('is_repayment', ProjectRepayment::IS_REPAYMENT)
            ->sum('money');


        $repayment['data']['investment_info']= $investment['data'];

        return $this->array_response($repayment);
    }

    /**
     * 账户概况
     *
     * - account_money 账户余额
     * - wait_repayment_interest_sum 待收利息
     * - wait_repayment_principal_sum 待收本金
     * - wait_repayment_money_sum 待收本息
     * - total_money 总资产(待收本息+账户余额)
     *
     * @Get("account_survey")
     */
    public function account_survey()
    {
        $user = $this->auth->user();
        $userMoney = UserMoney::firstOrNew(['user_id' => $user->id]);
        $account_money = $userMoney->money;
        $wait_repayment_money_sum = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->sum('money');

        $wait_repayment_principal_sum = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->sum('principal');
        $wait_repayment_interest_sum = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->sum('interest');
        $wait_repayment_principal_sum /= 100;
        $wait_repayment_money_sum /= 100;
        $wait_repayment_interest_sum /= 100;
        $total_money = $account_money + $wait_repayment_money_sum;
        return $this->array_response(
            compact([
                'account_money',
                'wait_repayment_interest_sum',
                'wait_repayment_money_sum',
                'wait_repayment_principal_sum',
                'total_money'
                ])
        );
    }

    /**
     * 投资概况
     *
     * - repayment_count 还款中项目数量
     * - over_count      已完结项目数量
     * - thirty_days_wait_repayment_money   近30天待还款
     * - historical_income_money            历史累计收益
     * - total_investment_num               总投资数量
     * - total_investment_money             总投资金额
     * - total_wait_repayment_money
     * - total_is_repayment_money
     *
     * @Get("investment_survey")
     */
    public function investment_survey()
    {
        $user = $this->auth->user();

        // 还款中
        $repayment_count = ProjectInvestment::query()
            ->where('user_id', $user->id)
            ->where('status', ProjectInvestment::STATUS_REPAYMENT)
            ->count();

        // 结束中
        $over_count = ProjectInvestment::query()
            ->where('user_id', $user->id)
            ->where('status', ProjectInvestment::STATUS_OVER)
            ->count();

        // 近30天待还
        $thirty_days_wait_repayment_money = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->where('estimate_time', '<=', date_create(strtotime(date('Y-m-d H:i:s') . ' +30 days')))
            ->sum('money');

        // 累计收益
        $historical_income_money = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::IS_REPAYMENT)
            ->sum('interest');

        // 累计投资笔数
        $total_investment_num = ProjectInvestment::query()
            ->where('user_id', $user->id)
            ->whereIn('status', [ProjectInvestment::STATUS_REPAYMENT, ProjectInvestment::STATUS_OVER])
            ->count();

        // 累计投资金额
        $total_investment_money = ProjectInvestment::query()->from('project_investments as pi')
            ->where('pi.user_id', $user->id)
            ->whereIn('pi.status', [ProjectInvestment::STATUS_REPAYMENT, ProjectInvestment::STATUS_OVER])
            ->leftJoin('projects as p', 'p.id', '=', 'pi.project_id')
            ->sum(DB::raw('p.money'));

        // 总待还金额
        $total_wait_repayment_money = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::NOT_REPAYMENT)
            ->sum('money');

        // 总已还金额
        $total_is_repayment_money = ProjectRepayment::query()
            ->where('user_id', $user->id)
            ->where('is_repayment', ProjectRepayment::IS_REPAYMENT)
            ->sum('money');

        $historical_income_money /= 100;
        $total_investment_money /= 100;
        $total_wait_repayment_money /= 100;
        $total_is_repayment_money /= 100;

        return $this->array_response(
            compact([
                'repayment_count',
                'over_count',
                'thirty_days_wait_repayment_money',
                'historical_income_money',
                'total_investment_num',
                'total_investment_money',
                'total_wait_repayment_money',
                'total_is_repayment_money',
            ])
        );
    }

    /**
     * 投资排名
     * - sum_money 总投资金额
     * - user_name 用户名
     *
     * @Get("investment_rank")
     * @Parameters({
     *      @Parameter("length", description="返回数据条数")
     * })
     */
    public function investment_rank(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);
        $rank = ProjectInvestment::query()
            ->from('project_investments as pi')
            ->whereIn('pi.status', [ProjectInvestment::STATUS_REPAYMENT, ProjectInvestment::STATUS_OVER])
            ->leftJoin('projects as p', 'pi.project_id', '=', 'p.id')
            ->leftJoin('users as u', 'pi.user_id', '=', 'u.id')
            ->groupBy('pi.user_id')
            ->select([DB::raw('sum(money)/100 as sum_money'), 'u.user_name'])
            ->orderBy('sum_money', 'desc')
            ->get();
        return $this->array_response($rank);
    }

}
