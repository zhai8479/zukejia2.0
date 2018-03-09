<?php

namespace App\Api\Controllers;


use App\Criteria\ProjectCriteria;
use App\Models\Project;
use App\Models\ProjectInvestment;
use App\Models\ProjectRepayment;
use App\Models\Upload;
use App\Models\User;
use App\Repositories\ProjectInvestmentRepository;
use App\Repositories\ProjectInvestmentRepositoryEloquent;
use App\Repositories\ProjectRepositoryEloquent;
use Dingo\Api\Http\Request as HttpRequest;
use Dingo\Blueprint\Annotation\Method\Get;
use App\Repositories\ProjectRepository;
use App\Validators\ProjectValidator;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;


/**
 * 项目
 *
 * @Resource("Project", uri="/project")
 *
 * Class ProjectsController
 * @package App\Api\Controllers
 *
 */
class ProjectsController extends BaseController
{

    /**
     * @var ProjectRepositoryEloquent
     */
    protected $repository;

    /**
     * @var ProjectValidator
     */
    protected $validator;

    public function __construct(ProjectRepository $repository, ProjectValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * 获取项目列表
     *
     * - id
     * - name       项目名称
     * - money      项目价格
     * - status
     * - status_str 项目状态字符串
     * - issue_total_num    总期数
     * - issue_day_num      期数外的天数
     * - rental_money       租房价格
     * - collect_money      收房价格/期利润
     * - characteristic     项目特点
     * - house_address      房屋地址
     * - house_status       房屋状态
     * - house_status_str   房屋状态str
     * - house_area             房屋面积
     * - house_competitive_power    房屋竞争力
     * - house_management_status    房屋经营状况
     * - house_management_status_str
     * - house_property_certificate     房产证号
     * - house_id_card                  身份证号
     * - house_residence                户口证编号
     * - house_contract_img_ids
     * - risk_assessment                风险评估
     * - safeguard_measures             保障措施
     * - guarantor                      担保方
     * - start_at                       开始投标时间
     * - end_at                         结束投标时间
     * - contract_file_id
     * - house_contract_img_urls        房屋合同及资料文件url
     * - contract_file_url              投资合同下载url
     * - type_info
     *      - name                      类型名称
     *      - repayment_type_str        还款方式
     *      - guarantee_type_str        担保方式
     *      - interest_day              计息延迟时间
     *
     *
     * @Get("index")
     *
     * @Parameters({
     *     @Parameter("length", description="限制一页的数据条数", required=false, default="15"),
     *     @Parameter("issue_total_num", required=false, description="查询期数范围: issue_total_num: {max: 100, min: 10}"),
     *     @Parameter("status", required=false, description="查询状态编号"),
     *     @Parameter("money", description="数组类型: money: {max: 100, min: 10}", required=false)
     * })
     *
     * @return array
     */
    public function index(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1',
            'issue_total_num' => 'array',
            'issue_total_num.max' => 'integer|min:0',
            'issue_total_num.min' => 'integer|min:0',
            'status' => [
                'integer',
                Rule::in(array_keys(Project::$status_list))
            ],
            'money' => 'array',
            'money.max' => 'numeric|min:0',
            'money.min' => 'numeric|min:0'
        ]);
        $this->repository->pushCriteria(ProjectCriteria::class);
        $projects = $this->repository
            ->scopeQuery(function (Builder $query) use ($request) {
                $status = $request->input('status');
                if ($status !== '' && $status!== null) $query->where('status', $status);

                $max = $request->input('money.max');
                $min = $request->input('money.min');
                $issue_min = $request->input('issue_total_num.min');
                $issue_max = $request->input('issue_total_num.max');

                if ($max !== '' && $max !== null) $query->where('money', '<=', $max * 100);
                if ($min !== '' && $min !== null) $query->where('money', '>=', $min * 100);

                if ($issue_min !== '' && $issue_min !== null) $query->where('issue_total_num', '>=', $issue_min);
                if ($issue_max !== '' && $issue_max !== null) $query->where('issue_total_num', '<=', $issue_max);

                return $query;
            })
            ->orderBy('status')
            ->orderBy('weight')
            ->orderBy('id', 'desc')
            ->paginate($request->input('length', 15));
        return $this->array_response($projects);
    }


    /**
     * 获取项目详情
     *
     * - id
     * - name       项目名称
     * - money      项目价格
     * - status
     * - status_str 项目状态字符串
     * - issue_total_num    总期数
     * - issue_day_num      期数外的天数
     * - rental_money       租房价格
     * - collect_money      收房价格/期利润
     * - characteristic     项目特点
     * - house_address      房屋地址
     * - house_status       房屋状态
     * - house_status_str   房屋状态str
     * - house_area             房屋面积
     * - house_competitive_power    房屋竞争力
     * - house_management_status    房屋经营状况
     * - house_management_status_str
     * - house_property_certificate     房产证号
     * - house_id_card                  身份证号
     * - house_residence                户口证编号
     * - house_contract_img_ids
     * - risk_assessment                风险评估
     * - safeguard_measures             保障措施
     * - guarantor                      担保方
     * - start_at                       开始投标时间
     * - end_at                         结束投标时间
     * - contract_file_id
     * - house_contract_img_urls        房屋合同及资料文件url
     * - contract_file_url              投资合同下载url
     * - buy_over_time                  购标结束时间
     * - created_at                     上标时间
     * - repayment_over_time            还款结束时间
     * - type_info
     *      - name                      类型名称
     *      - repayment_type_str        还款方式
     *      - guarantee_type_str        担保方式
     *      - interest_day              计息延迟时间
     *
     * @Get("show")
     *
     * @Parameters({
     *      @Parameter("id", description="项目id", required=true)
     * })
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $project = $this->repository->find($id);
        return $this->array_response($project);
    }

    /**
     * 返回项目相关配置信息
     *
     * - constant
     *     - status_list 状态列表
     *     - house_status_list 房屋状态列表
     *     - house_management_status_list 房屋装修状态列表
     *
     * @Get("config")
     */
    public function config()
    {
        return $this->array_response([
            'constant' => [
                'status_list' => Project::$status_list,
                'house_status_list' => Project::$house_status_list,
                'house_management_status_list' => Project::$house_management_status_list,
            ]
        ]);
    }

    /**
     * 获取项目推荐列表
     *
     * @Get("recommend_index")
     *
     * @Parameters({
     *      @Parameter("length", description="限制一页的数据条数", required=false, default="15")
     * })
     *
     */
    public function recommend_index(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1',
            'issue_total_num_max' => 'integer|min:1',
            'issue_total_num_min' => 'integer|min:1'
        ]);
        $projects = $this->repository
            ->scopeQuery(function (Builder $query) use ($request) {
                return $query->whereIn('status', [Project::STATUS_WAIT_START, Project::STATUS_PROCESS]);
            })
            ->orderBy('type_id')
            ->orderBy('weight')
            ->orderBy('money')
            ->orderBy('collect_money', 'desc')
            ->orderBy('issue_total_num')
            ->orderBy('id', 'desc')
            ->paginate($request->input('length', 15));
        return $this->array_response($projects);
    }

    /**
     * 获取房源信息
     * @Get("show_apartment_order_list")
     */
    public function show_apartment_order_list(HttpRequest $request)
    {
        $this->validate($request, [
            'apartment_id' => 'required|integer'
        ]);
        $url = config('app.bzg_url') . "/api/users/show_apartment";
        $repository = \Requests::post($url, [], [
            'apartment_id' => $request->input('apartment_id')
        ]);
        $body = $repository->body;
        $json_body = json_decode($body);
        if ($json_body->code === 0) {
            return $this->array_response($json_body->data);
        } else {
            return $this->no_content();
        }
    }

    /**
     * 平台信息
     * - total_investment
     * - total_reg_num
     * - total_profit
     * @Get("platform_info")
     */
    public function platform_info()
    {
        $total_investment = Project::query()
            ->whereIn('status', [Project::STATUS_OVER, Project::STATUS_REPAYMENT])
            ->select(\DB::raw('sum(money)/100 as num'))
            ->value('num');
        $total_profit = ProjectRepayment::query()
            ->where('is_repayment', ProjectRepayment::IS_REPAYMENT)
            ->select(\DB::raw('sum(principal - interest)/100 as num'))
            ->value('num');
        $total_reg_num = User::query()->count();
        return $this->array_response([
            'total_investment' => $total_investment,
            'total_reg_num' => $total_reg_num,
            'total_profit' => $total_profit
        ]);

    }
}
