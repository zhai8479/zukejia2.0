<?php

namespace App\Api\Controllers;

use App\Models\Appointment;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Dotenv\Exception\ValidationException;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Repositories\AppointmentRepository;
use App\Validators\AppointmentValidator;
use Dingo\Api\Http\Request as HttpRequest;


/**
 * Class AppointmentsController
 *
 * @package App\Api\Controllers
 *
 * @Resource("Appointment", uri="appointment")
 *
 */
class AppointmentsController extends BaseController
{

    /**
     * @var AppointmentRepository
     */
    protected $repository;

    /**
     * @var AppointmentValidator
     */
    protected $validator;

    public function __construct(AppointmentRepository $repository, AppointmentValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * 创建一条预约记录
     *
     * @Post("store")
     *
     * @Parameters({
     *  @Parameter("name", description="姓名", required=true),
     *     @Parameter("mobile", description="手机号", required=true),
     *     @Parameter("apartment_id", description="房源id", required=true),
     *     @Parmeter("user_id",description="用户id",required=false),
     *     @Parameter("appointments_time", description="预约时间", required=true),
     *     @Parameter("sex", description="性别 男|女", required=false),
     *     @Parameter("message", description="留言", required=false)
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
     * 更改预约记录信息
     *
     * @Post("mark")
     *
     * @Parameters({
     *      @Parameter("id", description="ID", required=true),
     *      @Parameter("name", description="姓名", required=true),
     *      @Parameter("mobile", description="手机号", required=true),
     *     @Parameter("apartment_id", description="房源id", required=true),
     *     @Parameter("appointments_time", description="预约时间", required=true),
     *     @Parameter("sex", description="性别 男|女", required=false),
     *     @Parameter("message", description="留言", required=false)
     * })
     *
     * @param HttpRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(HttpRequest $request)
    {
        $id = $request->input('id');
        $appointment = Appointment::find($id);
        try {
            foreach ($request->only(['id','name', 'mobile', 'appointments_time', 'sex', 'message']) as $key => $value) {
                $appointment->$key = $value;
            }
            $appointment->save();
        }
        catch (\Exception $exception) {
            return $this->error_response('修改失败', 100, [$exception->getMessage(), $exception->getPrevious(), $exception->getTraceAsString()]);
        }
        return $this->array_response($appointment);
    }

}
