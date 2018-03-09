<?php

namespace App\Api\Controllers;

use App\Criteria\MyCriteria;
use App\Models\StayPeople;
use App\Repositories\StayPeopleRepository;
use Dingo\Api\Http\Request as HttpRequest;
use Dingo\Blueprint\Annotation\Method\Delete;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;

/**
 * 入住人控制器
 *
 * @Resource("StayPeople", uri="stay_people")
 *
 * Class StayPeopleController
 * @package App\Http\Controllers
 */
class StayPeopleController extends BaseController
{
    protected $repository;

    public function __construct (StayPeopleRepository $repository) {
        $this->repository = $repository;
    }

    /**
     * 获取一条入住者信息
     * @Get("show")
     *
     * @Parameters({
     *  @Parameter("id", description="要查询的入住id", required=true)
     * })
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|mixed|null|static|static[]
     */
    public function show($id)
    {
        $user = $this->auth->user();
        \Log::info('user_id', [$user->id]);
        return $this->array_response(StayPeople::whereUserId($user->id)->find($id));
    }



    /**
     * 获取入住者列表
     * @Get("index")
     *
     * @Parameters({
     *  @Parameter("page", description="页数", default="1"),
     *  @Parameter("pageSize", description="数据条数", default="15")
     * })
     *
     * @param HttpRequest $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|static[]
     */
    public function index(HttpRequest $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(MyCriteria::class);
        $pageSize = $request->input('pageSize', 15);
        $list = $this->repository->paginate($pageSize);
        return $this->array_response($list);
    }

    /**
     * 添加一条入住者信息
     * @Post("store")
     *
     * @Parameters({
     *  @Parameter("real_name", description="入住者姓名", required=true),
     *  @Parameter("id_card", description="入住者身份证号", required=true),
     *  @Parameter("mobile", description="入住者手机号", required=false)
     * })
     *
     * @param HttpRequest $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function store(HttpRequest $request)
    {
        $this->validate($request, [
            'real_name' => 'required|string|max:40',
            'id_card' => 'required|string|max:20',
            'mobile' => 'string|regex:/^1[34578][0-9]{9}$/'
        ]);
        $input = $request->only(['real_name', 'id_card']);
        $user = $this->auth->user();
        if ($request->has('mobile')) {
            $input['mobile'] = $request->input('mobile');
        }
        $input['user_id'] = $user->id;
        $stayPeople = StayPeople::create($input);
        return $this->array_response($stayPeople);
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
        $user = $this->auth->user();
        $delete_num = StayPeople::whereUserId($user->id)->where('id', $id)->delete();
        return ['delete_num' => $delete_num];
    }

    /**
     * 编辑一条入住者信息
     * @Post("update")
     * @Parameters({
     *  @Parameter("real_name", description="真实姓名", required=false),
     *  @Parameter("id_card", description="身份证号", required=false),
     *  @Parameter("mobile", description="手机号", required=false),
     *  @Parameter("id", description="要编辑的入住者信息", required=true)
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function update(HttpRequest $request)
    {
        $this->validate($request, [
            'real_name' => 'string|max:40',
            'id_card' => 'string|max:20',
            'mobile' => 'string|regex:/^1[34578][0-9]{9}$/',
            'id' => 'required|integer|exists:stay_people'
        ]);
        $user = $this->auth->user();
        $id = $request->input('id');
        $stayPeople = StayPeople::where('user_id', $user->id)->whereNull('deleted_at')->find($id);
        if (empty($stayPeople)) return $this->error_response('id对应数据已被删除');
        if ($request->has('real_name')) {
            $stayPeople->real_name = $request->real_name;
        }
        if ($request->has('id_card')) {
            $stayPeople->id_card = $request->id_card;
        }
        if ($request->has('mobile')) {
            $stayPeople->mobile = $request->mobile;
        }
        $success = $stayPeople->save();
        return ['success' => $success];
    }
}
