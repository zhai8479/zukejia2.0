<?php

namespace App\Api\Controllers;

use App\Criteria\MyCriteria;
use App\Repositories\UserMoneyLogRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Dingo\Blueprint\Annotation\Response;

use App\Repositories\UserMoneyLogRepository;
use App\Validators\UserMoneyLogValidator;

/**
 * Class UserMoneyLogsController
 * @package App\Api\Controllers
 *
 * @Resource("UserMoneyLog", uri="user_money_log")
 *
 */
class UserMoneyLogsController extends BaseController
{

    /**
     * @var UserMoneyLogRepositoryEloquent
     */
    protected $repository;

    /**
     * @var UserMoneyLogValidator
     */
    protected $validator;

    public function __construct(UserMoneyLogRepository $repository, UserMoneyLogValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * 查询资金记录列表
     *
     * @Get("index")
     *
     * @Response(200, body={"data": {"id": 1, "user_id": 10000, "money": 0, "freeze": 0, "created_at": "2017-9-20 14:45:28"}})
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(MyCriteria::class);
        $userMoneyLogs = $this->repository->orderBy('id', 'desc')->all();
        return $this->array_response($userMoneyLogs);
    }

    /**
     * 查询资金记录
     *
     * @Get("show")
     *
     * @Parameters({
     *      @Parameter("id", description="要查询的记录id", required=true)
     * })
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->repository->pushCriteria(MyCriteria::class);
        $userMoneyLog = $this->repository->find($id);
        return $this->array_response($userMoneyLog);
    }
}
