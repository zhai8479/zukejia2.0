<?php

namespace App\Api\Controllers;

use App\Repositories\UserMoneyRepository;
use Dingo\Api\Http\Request as HttpRequest;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;

/**
 *
 * 用户金额控制器
 *
 * @Resource("UserMoney", uri="user_money")
 *
 * Class UserMoneyController
 * @package App\Api\Controllers
 */
class UserMoneyController extends BaseController
{
    /**
     * @var UserMoneyRepository
     */
    protected $repository;

    public function __construct(UserMoneyRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * 用户获取自己资金信息
     *
     * @Get("me")
     *
     */
    public function me()
    {
        $user = $this->auth->user();
        $userMoney = $this->repository->firstOrCreate(['user_id' => $user->id]);
        return $this->array_response($userMoney);
    }
}
