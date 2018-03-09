<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/11/3
 * Time: 15:57
 */

namespace App\Api\Controllers;


use App\Repositories\UserIntegralRepository;
use App\Repositories\UserIntegralRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;

/**
 * Class UserIntegralController
 * @package App\Api\Controllers
 *
 * @Resource("UserIntegral", uri="integral")
 *
 */
class UserIntegralController extends BaseController
{
    /**
     * @var UserIntegralRepositoryEloquent $repository
     */
    protected $repository;


    public function __construct(UserIntegralRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 获取自己的积分信息
     *
     *  - return
     *      - data
     *          - integral 积分数量
     *
     * @Get("me")
     *
     *
     * @return array
     */
    public function me()
    {
        $user = $this->auth->user();
        $integral = $this->repository->firstOrCreate(['user_id' => $user->id]);
        if (!isset($integral->integral)) $integral->integral = 0;
        return $this->array_response($integral);
    }

}