<?php

namespace App\Api\Controllers;

use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Dotenv\Exception\ValidationException;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\SignUpRepository;
use App\Validators\SignUpValidator;
use Dingo\Api\Http\Request as HttpRequest;
use App\Models\SignUp;


/**
 * Class SignUpsController
 *
 * @package App\Api\Controllers
 *
 * @Resource("SignUp", uri="sign_up")
 *
 */
class SignUpsController extends BaseController
{

    /**
     * @var SignUpRepository
     */
    protected $repository;

    /**
     * @var SignUpValidator
     */
    protected $validator;

    public function __construct(SignUpRepository $repository, SignUpValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * 创建一条报名记录
     *
     * @Post("store")
     *
     * @Parameters({
     *  @Parameter("name", description="姓名", required=true),
     *     @Parameter("mobile", description="手机号", required=true),
     *     @Parameter("address", description="地址", required=true),
     *     @Parameter("signUpTitle", description="报名标题", required=true),
     *     @Parameter("type", description="报名来源 app|pc|web|wap", required=true),
     *     @Parameter("area", description="房屋面积", required=false)
     *     @Parameter("community", description="小区-楼盘", required=false)
     * })
     * @param HttpRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(HttpRequest $request)
    {
        $all = $request->all();

        $all['ip'] = $request->ip();

        $this->validator->with($all)->passesOrFail(ValidatorInterface::RULE_CREATE);

        $signUp = $this->repository->create($all);

        return $this->array_response($signUp);
    }

    /**
     * 更改报名记录状态
     *
     * @Post("mark")
     *
     * @Parameters({
     *      @Parameter("id", description="ID", required=true),
     *      @Parameter("status", description="状态值", required=true)
     * })
     *
     * @param HttpRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function mark(HttpRequest $request)
    {
        $status = $request->input('status');
        $id = $request->input('id');

        $record = SignUP::find($id);
        $record->status = $status;
        $record->save();

        return $this->array_response($record);
    }

}
