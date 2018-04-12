<?php

namespace App\Api\Controllers;
use App\Events\LoginEvent;
use App\Models\Apartment;
use App\Models\ChainDistrict;
use App\Library\Password;
use App\Library\Recommend;
use App\Models\Order;
use App\Models\User;
use App\Models\UserIntegralLog;
use App\Repositories\UserIntegralRepository;
use App\Repositories\UserIntegralRepositoryEloquent;
use App\Repositories\UserRepository;
use App\Repositories\UserRepositoryEloquent;
use App\Repositories\UserVoucherRepository;
use App\Repositories\UserVoucherRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
//use Dingo\Blueprint\Annotation\Request;
use Dingo\Blueprint\Annotation\Resource;
use Dingo\Blueprint\Annotation\Response;
use Dingo\Blueprint\Annotation\Transaction;
use Dingo\Blueprint\Annotation\Versions;
use Dingo\Api\Http\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Dingo\Api\Exception\StoreResourceFailedException;
use JWTAuth;
use Sms;

/**
 * 用户操作接口
 *
 * @Resource("User", uri="/users")
 */
class UserController extends BaseController
{

    /**
     * @var UserIntegralRepositoryEloquent $userIntegralRepository
     */
    public $userIntegralRepository;




    /**
     * UserController constructor.
     * @param UserIntegralRepository $userIntegralRepository
     */
    public function __construct(UserIntegralRepository $userIntegralRepository)
    {
        $this->userIntegralRepository = $userIntegralRepository;
    }

    /**
     * 获取注册验证码
     *
     * @Get("mobile_register_code")
     *
     * @Parameters({
     *     @Parameter("mobile", description="手机号")
     * })
     *
     *
     * @param HttpRequest $request
     * @return array
     */
    public function mobile_register_code(HttpRequest $request)
    {
//        $this->validate($request, [
//            'mobile' => 'required|string|unique:users,mobile|regex:/^1[34578][0-9]{9}$/'
//        ]);
        $mobile = $request->input('mobile');
        if (!is_string($mobile) || !preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            return $this->error_response('手机号格式不正确');
        }
        $send_ret = Sms::sendCode($mobile, 'template_register_key_name', true);
        return $this->send_code_sms($send_ret);
    }

    /**
     * 手机号注册
     *
     * @Post("/mobile_register")
     * @Versions({"v1"})
     * @Transaction({
     *      @Request({"code": "短信验证码", "mobile": "13517210601", "password": "123456", "user_name": "用户名"}),
     *      @Response(200, body={"msg": "success"}),
     *      @Response(422, body={"error": {"user_name": {"用户名必须传入"}}})
     * })
     * @Parameters({
     *      @Parameter("code", description="手机短信验证码", required=true, type="string"),
     *      @Parameter("mobile", description="手机号", required=true, type="string"),
     *      @Parameter("password", description="密码", required=true, type="string"),
     *      @Parameter("user_name", description="用户名", required=true, type="string")
     * })
     *
     *
     * @param HttpRequest $request
     * @param Password $pwd
     * @return array
     * @throws \Exception
     */
    public function mobile_register(HttpRequest $request, Password $pwd)
    {
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        if (empty($mobile) || empty($password)) return $this->error_response('手机号与密码必须传入');
        if (!is_string($mobile) || !preg_match("/^1[34578]{1}\d{9}$/", $mobile)) {
            return $this->error_response('手机号格式不正确');
        }
        if (!is_string($password) || strlen($password) < 6) {
            return $this->error_response('密码格式不正确');
        }
        $mobile_exists = User::whereMobile($mobile)->exists();
        if ($mobile_exists) return $this->error_response('手机号已被注册');

        if (isset($request['user_name'])) {
            $user_name = $request['user_name'];
            if (!is_string($user_name) || strlen($user_name) > 40) return $this->error_response('用户名格式不正确');
            if (User::whereUserName($user_name)->exists()) return $this->error_response('用户名已被使用');
        }

        $code = $request->input('code');

        // 校验验证码
        $check_code  = Sms::checkCode($mobile, 'template_register_key_name', $code);
        if (! $check_code) {
            return $this->error_response('验证码无效');
        }

        $password = $request->input('password');
        $user_name = $request->input('user_name');
        $password_hash = $pwd->create_password($password);
        $create = [
            'mobile' => $mobile,
            'password' => $password_hash,
            'ip' => $request->ip(),
            'from_platform' => User::FROM_PLATFORM_PC
        ];
        if ($user_name) {
            $create['user_name'] = $user_name;
        }
        if ($request->has('from_user_mobile')) {
            $create['from_user_id'] = User::whereMobile($request->input('from_user_mobile'))->value('id');
        }
        try {
            /**
             * @var $userVoucherRepository UserVoucherRepositoryEloquent
             */
            $userVoucherRepository = app(UserVoucherRepository::class);

            // 创建用户
            $user = User::create($create);

            // 给积分
            $this->userIntegralRepository->register_add_integral($user->id);
            // 给代金卷
            $userVoucherRepository->recommend_add_voucher($user->id);

            // 给邀请者
            if ($request->has('from_user_mobile')) {
                // 增加积分
                $this->userIntegralRepository->share_register_add_integral($create['from_user_id']);
                // 增加代金卷
                $userVoucherRepository->recommend_add_voucher($create['from_user_id']);
            }

            $user_id = $user->id;
            $user->recommend_code = Recommend::create_code($user_id);
            $user->save();
            return $this->array_response([], '注册成功');
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * 手机号登陆
     *
     * @Post("/mobile_login")
     * @Versions({"v1"})
     *
     * @Parameters({
     *      @Parameter("mobile", description="手机号", required=true, type="string"),
     *      @Parameter("password", description="密码", required=true, type="string")
     * })
     *
     * @Response(200, body={"msg": "登陆成功"}, headers={"Authorization": "Bearer *****"})
     *
     * @param HttpRequest $request
     * @param Password $pwd
     * @return array
     */
    public function mobile_login(HttpRequest $request, Password $pwd)
    {
        $this->validate($request, [
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/|exists:users',
            'password' => 'required|string|min:6'
        ]);
        $password = $request->input('password');
        $mobile = $request->input('mobile');

        /* @var $user User*/
        $user = User::query()->where('mobile', $mobile)->first();
        if ($pwd->check_password($password, $user->password)) {
            // 验证密码成功
            $token = JWTAuth::fromUser($user);
            return $this->response->array(['msg' => '登陆成功', 'code' => 0,'date' =>['token'=> [$token,$mobile], 'user'=>$user]])->withHeader('Authorization', 'Bearer ' . $token);
        } else {
            // 验证密码错误
            return $this->error_response('密码错误');

        }
    }

    /**
     * 获取自己的信息
     *
     * @Get("self_info")
     * @Request({}, headers={"Authorization": "Bearer ****"})
     *
     * @return \Dingo\Api\Http\Response
     */
    public function self_info()
    {
        /**
         * @var $user User
         */
        $user = $this->user;
        $province = ChainDistrict::where('code', $user->province)->first(['id', 'name']);
        $city = ChainDistrict::where('code', $user->city)->first(['id', 'name']);
        if (!empty($province)) {
            $user->province_str = $province->name;
            $user->province_id = $province->id;

        }
        if (!empty($city)) {
            $user->city_str = $city->name;
            $user->city_id = $city->id;
        }


        $user->country_str = User::$countries[$user->country]??'';
        $user->education_str = User::$educations[$user->education]??'';
        $user->blood_type_str = User::$bloods[$user->blood_type]??'';
        return $this->array_response(['data' => $user]);
    }
    /**
     * 获取自己的房源列表
     *
     */
    public function self_apartment(){
        $user = $this->user();
        $apartment = Apartment::where('user_id',['user_id' => $user->id])
                                ->get();
        return $this->array_response(['data' => $apartment]);
    }
    /**
     * 获取自己的房源详细信息
     *
     */
    public function show_self_apartment($id){
        $user = $this->user();
        $apartment = Apartment::where('user_id',['user_id' => $user->id])
            ->where('id',[$id])
            ->first();
        $orders = Order::query()->where('apartment_id', $id)->get();
        $orders->reject(function (Order &$order) {
            $user = User::find($order->user_id, ['real_name', 'id_card', 'mobile']);
            $user->mobile = substr_replace($user->mobile, '****', 4, 4);
            //
            $order->user_info = $user->toArray();
        });

        return $this->array_response([
            'apartment' => $apartment,
            'orders' => $orders,
            'num_people' => count($orders),
        ]);
    }


    /**
     * 查看房源经营信息
     *
     * @Post("/show_apartment")
     * @Versions({"v1"})
     *
     * @Parameters({
     *      @Parameter("apartment_id", description="房源id", required=true, type="int")
     * })
     *
     * @Response(200, body={"data": {"apartment": {}, "orders": {}}, "msg": "成功"})
     *
     * @param HttpRequest $request
     * @return array
     */

    public function show_apartment(HttpRequest $request){
        $this->validate($request, [
            'apartment_id' => 'required|int'
        ]);
        $id = $request->input('apartment_id');
        $apartment = Apartment::query()->where('id',[$id])
            ->first();
        $orders = Order::query()->where('apartment_id', $id)->where('status',3)->get();
        $orders->reject(function (Order &$order) {
            $user = User::find($order->user_id, ['real_name', 'id_card', 'mobile']);
            $user->mobile = substr_replace($user->mobile, '****', 4, 4);
            $user->id_card = substr_replace($user->id_card, '****', 4, 8);
            //
            $order->user_info = $user->toArray();
        });

        return $this->array_response([
            'apartment' => $apartment,
            'orders' => $orders,
        ]);
    }


    /**
     * 验证手机号是否已经被使用
     *
     * @Post("check_mobile_is_use")
     * @Transaction({
     *      @Request({"mobile": "13517210601"}),
     *      @Response(200, body={"is_use": "true"}),
     *      @Response(200, body={"is_use": "false"})
     * })
     *
     * @Parameters({
     *     @Parameter("mobile", description="要查询的手机号", required=true, type="string")
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function check_mobile_is_use(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/'
        ]);
        $mobile = $request->input('mobile');
        $exists = User::where('mobile', $mobile)->exists();
        return $this->array_response(['is_use' => $exists]);
    }

    /**
     * 验证邮箱是否已经被使用
     *
     * @Post("check_email_is_use")
     * @Transaction({
     *      @Request({"email": "390961827@qq.com"}),
     *      @Response(200, body={"is_use": "true"}),
     *      @Response(200, body={"is_use": "false"})
     * })
     *
     * @Parameters({
     *  @Parameter("mobile", description="要查询的邮箱", required=true, type="string")
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function check_email_is_use(HttpRequest $request)
    {
        $this->validate($request, [
            'email' => 'required|string|email'
        ]);
        $email = $request->input('email');
        $exists = User::where('email', $email)->exists();
        return $this->array_response(['is_use' => $exists]);
    }

    /**
     * 判断用户名是否已被使用
     *
     * @Post("check_user_name_is_use")
     * @Transaction({
     *      @Request({"user_name": "13517210601"}),
     *      @Response(200, body={"is_use": true}),
     *      @Response(200, body={"is_use": false})
     * })
     *
     * @Parameters({
     *  @Parameter("user_name", description="要查询的用户名", type="string", required=true)
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function check_user_name_is_use(HttpRequest $request)
    {
        $this->validate($request, [
            'user_name' => 'required|min:4|max:40'
        ]);
        $user_name = $request->input('user_name');
        $exists = User::where('user_name', $user_name)->exists();
        return $this->array_response(['is_use' => $exists]);

    }

    /**
     * 获取用户简要信息
     *
     * @Get("user_simple_info")
     * @Parameters({
     *     @Parameter("user_id", description="要查询的用户id", required=true, type="integer")
     * })
     *
     * @param HttpRequest $request
     * @return \Dingo\Api\Http\Response
     */
    public function user_simple_info(HttpRequest $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id'
        ]);
        $user_id = $request->input('user_id');
        $user = User::select(['id', 'user_name', 'created_at'])->find($user_id);
        return $this->array_response(['user' => $user]);
    }

    /**
     * 发送找回密码验证码
     *
     * @Get("find_password_code")
     *
     * @Parameters({
     *  @Parameter("mobile", description="要找回密码的手机号", required=true, type="string")
     * })
     * @Transaction({
     *     @Request({"mobile": "13517210601"}),
     *      @Response(200, body={"msg": "发送成功"}),
     *      @Response(500, body={"msg": "发送验证码失败"})
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function find_password_code(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/|exists:users'
        ]);
        $mobile = $request->input('mobile');
        $send_ret = Sms::sendCode($mobile, 'template_find_password_key_name', true);
        return $this->send_code_sms($send_ret);
    }

    /**
     * 找回密码接口
     *
     * @Post("find_password")
     * @Request({"code": "045215", "mobile": "13517210601", "password": "123456"})
     * @Parameters({
     *      @Parameter("code", required=true, type="string", description="找回密码验证码"),
     *      @Parameter("mobile", description="手机号", type="string"),
     *      @Parameter("password", description="密码", type="string")
     * })
     *
     * @param HttpRequest $request
     * @param Password $pwd
     * @return array
     */
    public function find_password(HttpRequest $request, Password $pwd)
    {
        $this->validate($request, [
            'code' => 'required|string|min:5',
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/|exists:users',
            'password' => 'required|string|min:6|max:40'
        ]);
        $code  = $request->input('code');
        $mobile = $request->input('mobile');
        $password = $request->input('password');
        $check_code = Sms::checkCode($mobile, 'template_find_password_key_name', $code, false);
        if (! $check_code) {
            return $this->error_response('验证码无效');
        }
        Sms::delCacheCode($mobile, 'template_find_password_key_name');
        $user = User::where('mobile', $mobile)->first();
        $user->password = $pwd->create_password($password);
        $user->save();
        return $this->array_response([], '找回密码成功');
    }

    /**
     * 获取修改密码验证码
     *
     * @Get("change_password_code")
     */
    public function change_password_code()
    {
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $mobile = $user->mobile;
        $send_ret = Sms::sendCode($mobile, 'temp_change_password', true);
        return $this->send_code_sms($send_ret);
    }

    /**
     * 修改密码
     *
     * @Post("change_password")
     *
     * @Parameters({
     *      @Parameter("password", description="修改后的密码", required=true, type="string"),
     *      @Parameter("code", description="手机验证码", required=true, type="string")
     * })
     *
     * @param HttpRequest $request
     * @param Password $pwd
     * @return array
     */
    public function change_password(HttpRequest $request, Password $pwd)
    {
        $this->validate($request, [
            'code' => 'required|string|min:5',
            'password' => 'required|string|min:6|max:40'
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $mobile = $user->mobile;
        $password = $request->input('password');
        $code = $request->input('code');
        $check_code = Sms::checkCode($mobile, 'temp_change_password', $code, false);
        if (! $check_code) {
            return $this->sms_code_error();
        }
        Sms::delCacheCode($mobile, 'temp_change_password');
        $user->password = $pwd->create_password($password);
        $user->save();
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        return $this->array_response([], '修改密码成功');
    }

    /**
     * 绑定邮箱操作
     *
     * @Post("bind_email")
     * @Parameters({
     *      @Parameter("email", description="要绑定的邮箱", type="string", required=true)
     * })
     *
     *
     * @param HttpRequest $request
     * @return array
     */
    public function bind_email(HttpRequest $request)
    {
        $this->validate($request, [
            'email' => 'required|email|string|unique:users,email'
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        if(!empty($user->email)) return $this->error_response('用户已绑定邮箱');
        $user->email = $request->input('email');
        $user->save();
        // 赠送绑定积分
        $this->userIntegralRepository->bind_add_integral($user->id, UserIntegralLog::BIND_EMAIL);
        return $this->array_response([], '绑定成功');
    }

    /**
     * 修改绑定的邮箱
     *
     * @Post("change_email")
     *
     * @Parameters({
     *  @Parameter("email", description="要修改的邮箱")
     * })
     *
     * @param HttpRequest $request
     *
     * @return array
     */
    public function change_email(HttpRequest $request)
    {
        $this->validate($request, [
            'email' => 'required|email|string|unique:users,email'
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $user->email = $request->input('email');
        $user->save();
        return $this->array_response([], '修改绑定成功');
    }

    /**
     * 获取用户详细信息
     * @Get("user_detail_info")
     * @Response(200, body={"id": "1000", "user_name": "zhan", "mobile": "13517210601", "email": "390961827@qq.com"})
     * @return \Illuminate\Auth\GenericUser|\Illuminate\Database\Eloquent\Model
     */
    public function user_detail_info()
    {
        return $this->array_response($this->auth->user());
    }

    /**
     * 修改用户名
     * @Post("change_user_name")
     * @Parameters({
     *      @Parameter("user_name", description="要修改的用户名", type="string", required=true)
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function change_user_name(HttpRequest $request)
    {
        $this->validate($request, [
            'user_name' => 'required|min:4|max:40|unique:users,user_name',
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $user->user_name = $request->input('user_name');
        $user->save();
        return $this->array_response([], '修改用户名成功');
    }

    /**
     * 发送修改手机验证码
     *
     * @Get("change_mobile_code")
     * @Parameters({
     *  @Parameter("mobile", required=true, description="要修改为的手机号")
     * })
     *
     * @param HttpRequest $request
     * @return array|void
     */
    public function change_mobile_code(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|unique:users,mobile|regex:/^1[34578][0-9]{9}$/'
        ]);
        /**
         * @var User $user
         */
        $mobile = $request->input('mobile');
        $send_ret = Sms::sendCode($mobile, 'temp_change_mobile', true);
        return $this->send_code_sms($send_ret);
    }

    /**
     * 用户修改手机号
     * @Post("change_mobile")
     * @Parameters({
     *  @Parameter("mobile", required=true, description="要修改为的手机号"),
     *  @Parameter("code", required=true, description="要修改为的手机号的验证码")
     * })
     *
     * @param HttpRequest $request
     * @return array|void
     */
    public function change_mobile(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|unique:users,mobile|regex:/^1[34578][0-9]{9}$/',
            'code' => 'required|string|min:4'
        ]);
        // 验证验证码
        $mobile = $request->input('mobile');
        $code  = $request->input('code');
        $check_code = Sms::checkCode($mobile, 'temp_change_mobile', $code, false);
        if (!$check_code) {
            return $this->error_response('验证码错误');
        }
        Sms::delCacheCode($mobile, 'temp_change_mobile');
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $user->mobile = $request->input('mobile');
        $user->save();
        // 退出登陆
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        return $this->array_response([], '修改手机号成功');
    }

    /**
     * 获取手机验证码登陆验证码
     *
     * @Get("mobile_code_login_code")
     *
     * @Parameters({
     *      @Parameter("mobile", description="手机号", required=true, type="string")
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function mobile_code_login_code(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/|exists:users'
        ]);
        $mobile = $request->input('mobile');
        $send_ret = Sms::sendCode($mobile, 'temp_code_login', true);
        return $this->send_code_sms($send_ret);
    }
    
    /**
     * 手机验证码登陆账号
     *
     * @Post("mobile_code_login")
     *
     * @Parameters({
     *  @Parameter("mobile", required=true, description="手机号"),
     *  @Parameter("code", required=true, description="验证码")
     * })
     *
     * @Response(200, body={"msg": "登陆成功"}, headers={"authorization": "Bearer ****"})
     *
     * @param HttpRequest $request
     *
     * @return array
     */
    public function mobile_code_login(HttpRequest $request)
    {
        $this->validate($request, [
            'mobile' => 'required|string|exists:users,mobile|regex:/^1[34578][0-9]{9}$/',
            'code' => 'required|int|min:4'
        ]);
        $mobile = $request->input('mobile');
        $code = $request->input('code');
        $check_ret = Sms::checkCode($mobile, 'temp_code_login', $code, false);
        if (!$check_ret) return $this->sms_code_error();
        $user = User::where('mobile', $mobile)->first();
        $token = JWTAuth::fromUser($user);
        return $this->response->array(['msg' => '登陆成功', 'code' => 0,'date' =>['token'=> $token, 'user'=>$user]])->withHeader('Authorization', 'Bearer ' . $token);;
    }

    /**
     * 用户设置头像
     * @Post("change_avatar")
     *
     * @Parameters({
     *  @Parameter("avatar", description="要上传的头像文件键值", required=true)
     * })
     *
     *
     * @param HttpRequest $request
     * @return array
     */
    public function change_avatar(HttpRequest $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image'
        ]);
        
        $path = getcwd() . '/avatar/';
        $file_name =  $request->file('avatar')->hashName();
        $request->file('avatar')->move($path, $file_name);

        /**
         * @var  User $user
         */
        $user = $this->auth->user();
        $user->avatar_url = '/avatar/'.$file_name;
        $user->save();
        return $this->array_response(['path' => $path, 'full_path' => '/avatar/'.$file_name]);

    }

    /**
     * 获取用户头像链接
     * @Get("user_avatar")
     *
     * @Parameters({
     *  @Parameter("user_id", description="要查询的用户id", required=true)
     * })
     *
     * @Response(200, body={"avatar": "http://****"})
     *
     * @param HttpRequest $request
     * @return array
     */
    public function user_avatar(HttpRequest $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer|exists:users,id'
        ]);
        $user_id = $request->input('user_id');
        $user = User::find($user_id);
        return $this->array_response(['avatar' => $user->avatar_url]);
    }

    /**
     * 用户修改社会信息
     * @Post("change_community_info")
     * @Parameters({
     *  @Parameter("sex", required=false, description="性别代码 0: 未知， 1：男， 2：女", type="integer"),
     *  @Parameter("birthday", required=false, description="出生日期", type="date"),
     *  @Parameter("blood_type", required=false, description="血型代码  1-4, 1: AB, 2: A, 3: B, 4: O, 5: 其他",),
     *  @Parameter("education", required=false, description="学历 1-7, 1: 初中, 2: 高中, 3: 中专, 4: 大专, 5: 本科, 6: 硕士, 7: 博士"),
     *  @Parameter("profession", required=false, description="职业"),
     *  @Parameter("country", required=false, description="国家代码 传默认值1"),
     *  @Parameter("province", required=false, description="省代码 通过 /district/province_list 获取"),
     *  @Parameter("city", required=false, description="市代码 通过 /district/city_list 获取")
     * })
     *
     * @param HttpRequest $request
     * @return array|void
     */
    public function change_community_info(HttpRequest $request)
    {
        $this->validate($request, [
            'sex' => 'integer|min:0|max:2',
            'birthday' => 'date',
            'blood_type' => 'integer|min:1|max:4',
            'education' => 'integer|min:1|max:7',
            'profession' => 'string|max:50',
            'country' => 'integer',
            'province' => 'integer',
            'city' => 'integer'
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        if ($request->has('sex')) {
            $user->sex = $request->sex;
        }
        if ($request->has('birthday')) {
            $user->birthday = $request->birthday;
        }
        if ($request->has('blood_type')) {
            $user->blood_type = $request->blood_type;
        }
        if ($request->has('education')) {
            $user->education = $request->education;
        }
        if ($request->has('profession')) {
            $user->profession = $request->profession;
        }
        if ($request->has(['country', 'province', 'city'])) {
            $country = 1;
            $province = $request->province;
            $city = $request->city;
            $exist_province = ChainDistrict::where('code', $province)->exists();
            $exist_city = ChainDistrict::where('code', $city)->exists();
            if (!$exist_province) return $this->error_response('code 对应省不存在');
            if (!$exist_city) return $this->error_response('code 对应市不存在');
            $user->country = $country;
            $user->province = $province;
            $user->city = $city;
        }
        $user->save();
        return $this->no_content('保存成功');
    }

    /**
     * 用户修改身份证信息
     * @Post("change_id_card_info")
     * @Parameters({
     *  @Parameter("real_name", required=true, description="真实姓名"),
     *  @Parameter("id_card", required=true, description="身份证号")
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function change_id_card_info(HttpRequest $request)
    {
        $this->validate($request, [
            'real_name' => 'required|string|min:1|max:20',
            'id_card' => 'required|string|min:16|max:18'
        ]);
        /**
         * @var User $user
         */
        $user = $this->auth->user();
        $user->real_name = $request->real_name;
        $user->id_card = $request->id_card;
        $user->save();
        return $this->no_content('修改成功');
    }

    /**
     * 退出登陆
     * @Post("login_out")
     */
    public function login_out()
    {
        $token = JWTAuth::getToken();
        JWTAuth::invalidate($token);
        return $this->no_content('退出登陆成功');
    }

}