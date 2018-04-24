<?php

namespace App\Api\Controllers;

use App\Models\Apartment;
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
        $user = $this->auth->user();
        $all = $request->all();
        $all['user_id'] = $user->id;
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



    /**
     * 删除一条入住者信息
     * @Delete("delete")
     *
     * @Parameters({
     *  @Parameter("id", description="要删除的入住者记录id", required=true)
     * })
     *
     * @param $id
     * @return bool|mixed|null
     */
    public function delete($id)
    {
        if($id) {
            $user = $this->auth->user();
            $stayPeople = Appointment::where('user_id', $user->id)->find($id);
            if($stayPeople)
            {
                $stayPeople->deleted_at = now();
                $stayPeople->is_del = 1;
                $stayPeople->save();
            }
            return $this->array_response([],'删除成功',0);
        }
        return $this->array_response([],'删除成功',0);

    }


    /**
     * 获取预约列表
     *
     * @Get("index")
     *
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function index(HttpRequest $request)
    {
        $user_id = $request->input('user_id');
        $appointment = Appointment::where('user_id',$user_id)
            ->get();
        if ($appointment) {
            foreach ($appointment as $app) {
                $apartments = Apartment::query()->where('id',$app->apartment_id)->get();
                $result = [];
                $apartments->reject(function($item)use(&$result, $apartments){
                    $result[] = $item->indexListFilter($item);
                });
                $app->apartment_info = $result;
            }
        }
        return $this->array_response($appointment);
    }
}
