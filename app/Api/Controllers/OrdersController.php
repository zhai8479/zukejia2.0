<?php

namespace App\Api\Controllers;

use App\Criteria\MyCriteria;
use App\Models\Apartment;
use App\Models\Order;
use App\Models\OrderCheckInUser;
use App\Models\OrderPay;
use App\Models\RentalRecord;
use App\Models\StayPeople;
use App\Models\UserMoney;
use App\Models\UserVoucher;
use App\Repositories\ApartmentRepository;
use App\Repositories\ApartmentRepositoryEloquent;
use App\Repositories\OrderCheckInUserRepository;
use App\Repositories\OrderCheckInUserRepositoryEloquent;
use App\Repositories\OrderRepositoryEloquent;
use App\Repositories\UserMoneyLogRepository;
use App\Repositories\UserMoneyLogRepositoryEloquent;
use App\Repositories\UserMoneyRepository;
use App\Repositories\UserMoneyRepositoryEloquent;
use App\Repositories\UserVoucherRepository;
use App\Repositories\UserVoucherRepositoryEloquent;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Resource;
use Dingo\Api\Http\Request as HttpRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OrderCreateRequest;
use App\Repositories\OrderRepository;
use App\Validators\OrderValidator;

/**
 * 订单控制器
 * @Resource("Order", uri="order")
 *
 * Class OrdersController
 * @package App\Api\Controllers
 */
class OrdersController extends BaseController
{

    /**
     * @var OrderRepositoryEloquent
     */
    protected $repository;

    /**
     * 房源
     * @var OrderCheckInUserRepositoryEloquent
     */
    protected $checkInUserRepository;

    /**
     * @var UserMoneyRepositoryEloquent
     */
    protected $userMoneyRepository;

    /**
     * @var UserMoneyLogRepositoryEloquent
     */
    protected $userMoneyLogRepository;

    /**
     * @var OrderValidator
     */
    protected $validator;

    public function __construct(OrderRepository $repository,
                                OrderValidator $validator,
                                OrderCheckInUserRepository $checkInUserRepository,
                                UserMoneyRepository $userMoneyRepository,
                                UserMoneyLogRepository $userMoneyLogRepository
    )
    {
        $this->repository = $repository;
        $this->checkInUserRepository = $checkInUserRepository;
        $this->userMoneyRepository = $userMoneyRepository;
        $this->userMoneyLogRepository = $userMoneyLogRepository;
        $this->validator  = $validator;
    }


    /**
     * 获取订单列表
     *
     * - 搜索示例： /api/order/index?search=status:2;order_no:2017101292578&orderBy=id&sortedBy=desc&searchJoin=and
     *
     *  - 返回值示例：
     *      - current_page: 当前页数
     *      - data: 列表数据
     *          - array
     *              - apartment_info    房屋信息
     *              - status            订单状态
     *      - first_page_url: 首页url
     *      - last_page: 最后一页页数
     *      - last_page_url: 最后一页url
     *      - next_page_url: 下一页url
     *      - path: api地址
     *      - per_page： 当前页数据条数
     *      - prev_page_url: 前一页url
     *      - total: 总数据条数
     *
     *
     * ---
     *
     * @Get("index")
     *
     * @Parameters({
     *     @Parameter("search", description="搜索字段", type="string", required=false),
     *     @Parameter("orderBy", description="排序依据字段", type="string", required=false),
     *     @Parameter("sortedBy", description="排序方式 desc or asc", type="string", required=false),
     *     @Parameter("searchJoin", description="查询字段组合方式 and or ", type="string", required=false, default="and"),
     *     @Parameter("pageSize", description="页面数据条数", type="integer", default=15, required=false),
     *     @Parameter("page", description="页数", type="integer", default=1, required=false)
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function index(HttpRequest $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $this->repository->pushCriteria(MyCriteria::class);
        $pageSize = $request->input('pageSize', 15);
        $orders = $this->repository->paginate($pageSize);
        if ($orders) {
            foreach ($orders as $order) {
                $apartments = Apartment::query()->where('id',$order->apartment_id)->get();
                $result = [];
                $apartments->reject(function($item)use(&$result, $apartments){
                    $result[] = $item->indexListFilter($item);
                });
                $order->apartment_info = $result;
                $order->status_str = Order::$order_status[$order->status];
            }
        }
        ini_set('date.timezone','Asia/Shanghai');
//        $orders['service_time'] = date("Y-m-d H:i:s");

        $response = [];
        $tempList = json_decode( json_encode( $orders),true);
        $tempList['service_time'] = date("Y-m-d H:i:s");
        $tempList['out_time'] = 180000;
        $response['data'] =  $tempList;
        $response['code'] = 0;
        $response['msg'] = 'success';
        return $response;

      // return $this->array_response($orders);
    }

    /**
     * 创建一个订单
     *
     * check_in_users 示例：{"check_in_users": [{"real_name": "zhan", "id_card": "", "mobile": ""}, {"real_name": "zhan", "id_card": "", "mobile": ""}]}
     *
     * ---
     *
     * @Post("store")
     *
     * @Parameters({
     *     @Parameter("apartment_id", description="房源id", required=true, type="integer"),
     *     @Parameter("start_date", description="起租时间 Y-m-d 格式", required=true, type="date"),
     *     @Parameter("end_date", description="退房时间 Y-m-d 格式", required=true, type="date"),
     *     @Parameter("need_invoice", description="是否需要发票, 0或者1", required=false, type="bool"),
     *     @Parameter("check_in_users", description="入住人信息列表", required=true, type="array"),
     *     @Parameter("coupons_id", description="优惠卷id", required=false, type="integer")
     * })
     *
     * @param  OrderCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(OrderCreateRequest $request)
    {
        $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);
        $user = $this->auth->user();
        $input = $request->all();

        $apartment_id = $input['apartment_id'];
        $start_date = $input['start_date'];
        $end_date = $input['end_date'];
        // 填写默认信息
        $input['user_id'] = $user->id;
        //创建一个订单编号
        $input['order_no'] = $this->repository->generate_order_no();
        /**
         * 处理房源数据
         * @var Apartment $house
         * $rental_type 租房类型： 0. 短租, 1. 长租
         * $rental_price 租金 单位元
         * $rental_deposit 押金 单位元
         */
        $house = Apartment::find($apartment_id);
        if ($house->is_in_maintenance()) return $this->error_response('房源正在维护中，暂不接受出租');
        // 判断选择的日期是否已经被出租
        if (RentalRecord::check_room_is_rental($apartment_id, $start_date, $end_date)) {
            return $this->error_response('选择的日期已被出租');
        }

        $input['rent_type'] = $house->rental_type === 0?1:2;
        $housing_numbers = $this->repository->count_housing_numbers($input['start_date'], $input['end_date'], $input['rent_type']);
        $input['rental_price'] = $house->rental_price *  $housing_numbers;     // 租金
        $input['rental_deposit'] = $house->rental_deposit ;    // 押金

        // 优惠卷抵扣金额
        /**
         * @var $userVouchersRepository UserVoucherRepositoryEloquent
         */
        $userVouchersRepository = app(UserVoucherRepository::class);
        if ($request->has('coupons_id')) {
            $coupons_id = $request->input('coupons_id');
            $coupons = UserVoucher::where(
                [
                    ['user_id', '=', $user->id],
                    ['id', '=', $coupons_id],
                    ['is_use', '=', UserVoucher::NOT_USE]
                ])->first();
            if (empty($coupons)) return $this->error_response('代金卷已被使用');
            // 规则判定
            if (!$userVouchersRepository->check_rules($coupons->rules, $input['rental_price'], $house->rental_type)) return $this->error_response('代金卷不符合使用规则');
            $input['coupons_money'] = \DB::table('vouchers_schemes')->where('id', $coupons->scheme_id)->value('reduce');
        } else {
            $input['coupons_money'] = 0;
        }

        $input['activity_money'] = 0;       // 活动抵扣金额
        $input['pay_money'] = $this->repository->count_pay_money($input['rental_price'], $input['rental_deposit'], $input['coupons_money'], $input['activity_money']);
        $input['ip'] = $request->ip();
        $input['housing_numbers'] = $housing_numbers;
        $check_in_users = $input['check_in_users'];
        unset($input['check_in_users']);

        \DB::beginTransaction();
        try {
            /**
             * 创建订单
             * @var Order $order
             */
            $order = $this->repository->create($input);

            if ($request->has('coupons_id')){
                $coupons_id = $request->input('coupons_id');

                // 处理优惠卷信息
                $affect = \DB::table('user_vouchers')
                    ->where('id', $coupons_id)
                    ->where('is_use', UserVoucher::NOT_USE)
                    ->where('user_id', $user->id)
                    ->update(['is_use' => UserVoucher::IS_USE]);
                if ($affect != 1) throw new \Exception('代金卷使用失败');
            }
            if(is_array($check_in_users)) {
                // 处理入住者信息
                $order_check_user_ids = [];
                foreach ($check_in_users as $key => $in_user) {
                    $check_in_user_info = [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'stay_people_id' => $in_user['stay_people_id'],
                        /* 'real_name' => $in_user['real_name'],
                         'id_card' => $in_user['id_card'],
                         'mobile' => $in_user['mobile'],*/
                    ];
                    /**
                     * @var OrderCheckInUser $order_check_user
                     */
                    $order_check_user = $this->checkInUserRepository->create($check_in_user_info);
                    $order_check_user_ids[] = $order_check_user->id;
                }
            }
            if(is_string($check_in_users))
            {
                $json = json_decode($check_in_users,true);
                // 处理入住者信息
                $order_check_user_ids = [];
                for ($i = 0; $i < count($json); $i++) {
                    $check_in_user_info = [
                        'order_id' => $order->id,
                        'user_id' => $user->id,
                        'stay_people_id' => $json[$i]['stay_people_id'],
                        /* 'real_name' => $in_user['real_name'],
                         'id_card' => $in_user['id_card'],
                         'mobile' => $in_user['mobile'],*/
                    ];
                    /**
                     * @var OrderCheckInUser $order_check_user
                     */
                    $order_check_user = $this->checkInUserRepository->create($check_in_user_info);
                    $order_check_user_ids[] = $order_check_user->id;
                }
            }
            $order->check_in_user_ids = $order_check_user_ids;
            $order->save();
            \DB::commit();
            return $this->array_response($order->toArray(), '订单创建成功');
        } catch (\Exception $exception) {
            \Log::error('db error', [__FILE__, __FUNCTION__, __LINE__, $exception]);
            \DB::rollBack();
            return $this->error_response($exception->getMessage());
        }


    }

    /**
     * 根据id获取订单详情
     *
     * 返回值：
     * + id 主键
     * + order_no 订单号码
     * + user_id 用户id
     * + rental_price 租金
     * + rental_deposit 押金
     * + apartment_id 房源id
     * + pay_money 实际需要支付金额
     * + status 订单状态
     * + start_date 租房开始时间
     * + end_date 租房结束时间
     * + housing_numbers 租房天数/月数
     * + rent_type 租房类型 1. 短租 2. 长租
     * + need_invoice 是否需要发票
     * + ip 使用ip
     * + pay_channel 支付渠道
     * + pay_account 支付账号
     * + external_no 外部订单号
     * + pay_start_at 支付发起时间
     * + pay_over_at 支付完成时间
     * + pay_status 支付状态
     * + is_refunds 是否发生退款
     * + refunds_total_money 总退款金额
     * + created_at 订单创建时间
     * + updated_at 订单更改时间
     * + order_pay_no 支付单号
     * + check_in_users array 入住者列表
     *
     * @Get("show")
     *
     * @Parameters({
     *      @Parameter("id", description="订单id", required=true)
     * })
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $this->repository->pushCriteria(MyCriteria::class);
        $order = $this->repository->find($id);
        if ($order) {
            $order->check_in_users =  StayPeople::whereIn('id', OrderCheckInUser::where('order_id', $order->id)->pluck('stay_people_id'))->get();
            $order->apartment_info = Apartment::find($order->apartment_id);
            $order->status_str = Order::$order_status[$order->status];
            $order->service_time =date("Y-m-d H:i:s");
            $order->out_time =180000;
        }
        return $this->array_response($order);
    }

    /**
     * 取消未支付订单
     *
     * @Post("cancel_no_pay")
     *
     * @Parameters({
     *      @Parameter("id", description="订单id", required=true)
     * })
     * @param HttpRequest $request
     * @return Order|array
     */
    public function cancel_no_pay(HttpRequest $request)
    {
        $user = $this->auth->user();
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('orders', 'id')
                ->where('user_id',$user->id)
            ],
            'cancel_reason' => 'string|max:255'
        ]);
        /**
         * @var Order $order
         */
        $order = $this->repository->find($request->id);    // 订单
        if ($order->status != 1) return $this->error_response('订单状态不为未支付，无法取消');
        $order->status = 6; // 设置为未支付取消状态
        if ($request->input('cancel_reason')) $order->cancel_reason = $request->input('cancel_reason');
        $order->cancel_at = date_create();
        $order->save();
        return $this->array_response($order);
    }

    /**
     * 订单支付
     *
     * @POST("pay")
     *
     * @Parameters({
     *     @Parameter("id", description="订单id", required=true, type="integer"),
     *     @Parameter("pay_type", description="支付方式 1. 余额，2. 支付宝，3. 微信，4. 银行卡", required=true, type="integer")
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function pay(HttpRequest $request)
    {
        // 1. 根据订单id，获取订单信息
        // 2. 根据订单信息，获取待支付金额
        // 3. 根据支付途径，选择不同的支付方式
        //   - 若支付方式为账户余额支付，则判断余额是否充足
        //   - 余额充足，进行支付操作: 1. 添加消费日志user_money_logs, 2. 减少用户资金 user_money 3. 填写支付记录 order_pays 4. 更改订单状态 orders
        $user = $this->auth->user();
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('orders', 'id')->where('user_id', $user->id)
            ],
            'pay_type' => 'required|integer|max:5|min:1',   // 支付方式
        ]);
        $pay_type = $request->pay_type;
        $order_id = $request->id;
        /**
         * @var Order $order
         */
        $order = $this->repository->find($request->id); // 订单类
        if ($order->status !=1) return $this->error_response('订单状态不正确');

        /**
         * @var $created_at Carbon
         */
        $created_at = $order->created_at;

        $created_at = $created_at->timestamp;

        if ($created_at < time() - 180000) return $this->error_response('已超过支付时间');


        if (RentalRecord::check_room_is_rental($order->apartment_id, $order->start_date, $order->end_date)) {
            return $this->error_response('选择的日期已被出租');
        }
        $pay_money = $order->pay_money; // 待支付金额
        if ($pay_type == 1) {
            // 余额支付
            $userMoney = $this->userMoneyRepository->firstOrCreate(['user_id' => $user->id]);
        //    $money = $userMoney->money;
    //        \Log::debug('money', [$money, $pay_money]);
            // 执行支付操作
            try {
                $this->userMoneyRepository->pay($pay_money, $user->id, '支付房租与押金');
            } catch (\Exception $exception) {
                return $this->error_response($exception->getMessage(), 100, [$exception]);
            }
            // 支付成功后增加支付记录
            $pay_start_at = $pay_over_at = date('Y-m-d H:i:s');
            $pay_order_no = $this->repository->generate_order_no();
            OrderPay::create([
                'order_id' => $order_id,
                'order_pay_no' => $pay_order_no,
                'ip' => $request->ip(),
                'pay_channel' => 1,
                'pay_account' => '',
                'pay_start_at' => $pay_start_at,
                'pay_over_at' => $pay_over_at,
                'pay_status' => 3,
            ]);
            // 记录房子被租数据
            RentalRecord::create([
                'apartment_id' => $order->apartment_id,
                'start_date' => $order->start_date,
                'end_date' => $order->end_date,
                'order_id' => $order->id,
            ]);
            $order->pay_channel = 1;
            $order->pay_start_at = $pay_start_at;
            $order->pay_over_at = $pay_over_at;
            $order->pay_account = '';
            $order->pay_status = 3;
            $order->order_pay_no = $pay_order_no;
            $order->status = 2;
            $order->save();
            return $this->no_content('支付成功');
        } elseif ($pay_type == 2){  //支付方式为支付宝支付
            try{
                //订单支付渠道改为支付宝
                $order->pay_channel = 2;
                $order->save();
                //调用支付宝支付
                $alipay = app('alipay.web');
                $alipay->setOutTradeNo($order->order_no);
                $alipay->setTotalFee($order->pay_money);
                $alipay->setSubject('支付房租与押金');
                $alipay->setBody('支付房租与押金');
                $alipay->setQrPayMode('5'); //该设置为可选，添加该参数设置，支持二维码支付。
                $aLipayLink = $alipay->getPayLink();
            }catch (\Exception $exception) {
                return $this->error_response($exception->getMessage(), 100, [$exception]);
            }
            $response = [];
            $response['aLiPayLink'] = $aLipayLink;
            return $this->array_response($response);

        } else {
            return $this->error_response("暂不支持其他支付方式");
        }
    }



    /**
     * 订单手机支付
     *
     * @POST("mobile_pay")
     *
     * @Parameters({
     *     @Parameter("id", description="订单id", required=true, type="integer"),
     *     @Parameter("pay_type", description="支付方式 1. 余额，2. 支付宝，3. 微信，4. 银行卡", required=true, type="integer")
     * })
     * @param HttpRequest $request
     * @return array
     */
    public function mobile_pay(HttpRequest $request)
    {
        // 1. 根据订单id，获取订单信息
        // 2. 根据订单信息，获取待支付金额
        // 3. 根据支付途径，选择不同的支付方式
        //   - 若支付方式为账户余额支付，则判断余额是否充足
        //   - 余额充足，进行支付操作: 1. 添加消费日志user_money_logs, 2. 减少用户资金 user_money 3. 填写支付记录 order_pays 4. 更改订单状态 orders
        $user = $this->auth->user();
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('orders', 'id')->where('user_id', $user->id)
            ],
            'pay_type' => 'required|integer|max:5|min:1',   // 支付方式
        ]);
        $pay_type = $request->pay_type;
        $order_id = $request->id;
        /**
         * @var Order $order
         */
        $order = $this->repository->find($request->id); // 订单类
        if ($order->status !=1) return $this->error_response('订单状态不正确');

        /**
         * @var $created_at Carbon
         */
        $created_at = $order->created_at;

        $created_at = $created_at->timestamp;

        if ($created_at < time() - 180000) return $this->error_response('已超过支付时间');


        if (RentalRecord::check_room_is_rental($order->apartment_id, $order->start_date, $order->end_date)) {
            return $this->error_response('选择的日期已被出租');
        }
        $pay_money = $order->pay_money; // 待支付金额
        if ($pay_type == 1) {
            // 余额支付
            $userMoney = $this->userMoneyRepository->firstOrCreate(['user_id' => $user->id]);
            //    $money = $userMoney->money;
            //        \Log::debug('money', [$money, $pay_money]);
            // 执行支付操作
            try {
                $this->userMoneyRepository->pay($pay_money, $user->id, '支付房租与押金');
            } catch (\Exception $exception) {
                return $this->error_response($exception->getMessage(), 100, [$exception]);
            }
            // 支付成功后增加支付记录
            $pay_start_at = $pay_over_at = date('Y-m-d H:i:s');
            $pay_order_no = $this->repository->generate_order_no();
            OrderPay::create([
                'order_id' => $order_id,
                'order_pay_no' => $pay_order_no,
                'ip' => $request->ip(),
                'pay_channel' => 1,
                'pay_account' => '',
                'pay_start_at' => $pay_start_at,
                'pay_over_at' => $pay_over_at,
                'pay_status' => 3,
            ]);
            // 记录房子被租数据
            RentalRecord::create([
                'apartment_id' => $order->apartment_id,
                'start_date' => $order->start_date,
                'end_date' => $order->end_date,
                'order_id' => $order->id,
            ]);
            $order->pay_channel = 1;
            $order->pay_start_at = $pay_start_at;
            $order->pay_over_at = $pay_over_at;
            $order->pay_account = '';
            $order->pay_status = 3;
            $order->order_pay_no = $pay_order_no;
            $order->status = 2;
            $order->save();
            return $this->no_content('支付成功');
        } elseif ($pay_type == 2){  //支付方式为支付宝支付
            try{
              /*  //订单支付渠道改为支付宝
                $order->pay_channel = 2;
                $order->save();
                //调用支付宝支付
                // 创建支付单。
                $alipay = app('alipay.mobile');
                $alipay->setOutTradeNo($order->order_no);
                $alipay->setTotalFee($order->pay_money);
                $alipay->setSubject('支付房租与押金');
                $alipay->setBody('支付房租与押金');
                $aLipayPara = $alipay->getPayPara();*/


                require_once('/app/Api/alipay/aop/AopClient.php');
                require_once('/app/Api/alipay/aop/request/AlipayTradeAppPayRequest.php');
                $aop = new \AopClient();

                //**沙箱测试支付宝开始
                $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
                //实际上线app id需真实的
                $aop->appId = "2018011001750693";
                $aop->rsaPrivateKey = '填写工具生成的商户应用私钥';
                $aop->format = "json";
                $aop->charset = "UTF-8";
                $aop->signType = "RSA";
                $aop->alipayrsaPublicKey = '填写从支付宝开放后台查看的支付宝公钥';
                $bizcontent = json_encode([
                    'body'=>'支付房租与押金',
                    'subject'=>'支付房租与押金',
                    'out_trade_no'=>$order->order_no,//此订单号为商户唯一订单号
                    'total_amount'=> $order->pay_money,//保留两位小数
                    'product_code'=>$order->apartment_id
                ]);
                //**沙箱测试支付宝结束
                //实例化具体API对应的request类,类名称和接口名称对应,当前调用接口名称：alipay.trade.app.pay
                $request = new \AlipayTradeAppPayRequest();
                //支付宝回调
                $request->setNotifyUrl("http://a1.zukehouse.com/order_alipay_notify");
                $request->setBizContent($bizcontent);
                //这里和普通的接口调用不同，使用的是sdkExecute
                $response = $aop->sdkExecute($request);



            }catch (\Exception $exception) {
                return $this->error_response($exception->getMessage(), 100, [$exception]);
            }
            $response = [];
            $response['aLipayPara'] = $aLipayPara;
            return $this->array_response($response);

        } else {
            return $this->error_response("暂不支持其他支付方式");
        }
    }

    /**
     * 已支付订单取消
     *
     * 只有未到达入住时间的订单可以进行取消
     * 退还路线: 1. 增加退款记录order_refunds 2. 修改订单状态与退款状态值和退款数额 3. 执行退款操作 4. 退款完毕后更改退款记录表状态值
     *
     * @Post("cancel_is_pay")
     *
     * @Parameters({
     *      @Parameter("id", description="订单id", required=true, type="integer")
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function cancel_is_pay(HttpRequest $request)
    {
        $user = $this->auth->user();
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('orders', 'id')->where('user_id', $user->id)
            ],
            'cancel_reason' => 'string|max:255'
        ]);
        $order_id = $request->id;
        $user_id = $user->id;
        /**
         * @var Order $order
         */
        $order = $this->repository->find($order_id); // 订单类
        if ($order->status != 2) return $this->error_response('订单状态不正确');
        if ($order->start_date . ' 14:00:00' < date('Y-m-d H:i:s')) return $this->error_response('已达到入住时间，无法执行取消操作');
        \DB::beginTransaction();
        try {
            // 退押金
            $this->userMoneyRepository->refund($user_id, $order->id, 1, 0, $order->rental_deposit, '退还押金');
            // 退租金
            $this->userMoneyRepository->refund($user_id, $order->id, 2, $order->housing_numbers, $order->rental_price, '退还租金');

            // 修改订单状态
            $order->status = 5;
            $order->is_refunds = true;
            $order->refunds_total_money = $order->rental_deposit + $order->rental_price;
            if ($request->input('cancel_reason')) $order->cancel_reason = $request->input('cancel_reason');
            $order->cancel_at = date_create();
            $order->save();

            // 删除被锁定的日期
            RentalRecord::where('order_id', $order_id)->delete();

            \DB::commit();
        } catch (\Exception $exception) {
            \Log::error('refund error', [$exception]);
            \DB::rollBack();
            return $this->error_response('退款失败，请联系管理员');
        }

        return $this->no_content('退款成功');
    }

    /**
     * 用户退房操作
     *
     * 只有状态为2， 当前时间为入住时间内，才允许退房操作
     * 根据时间条件来判断，应该退还多少钱(租金)
     * 退还路线: 1. 增加退款记录order_refunds 2. 修改订单状态与退款状态值和退款数额 3. 执行退款操作 4. 退款完毕后更改退款记录表状态值
     *
     * @Post("check_out")
     *
     * @Parameters({
     *      @Parameter("id", description="订单id", required=true, type="integer")
     * })
     *
     * @Post("check_out")
     * @param HttpRequest $request
     * @return array
     */
    public function check_out(HttpRequest $request)
    {
        $user = $this->auth->user();
        $this->validate($request, [
            'id' => [
                'required',
                Rule::exists('orders', 'id')->where('user_id', $user->id)
            ],
        ]);
        $order_id = $request->id;
        $user_id = $user->id;
        /**
         * @var Order $order
         */
        $order = $this->repository->find($order_id); // 订单类
        if ($order->status != 2) return $this->error_response('订单状态不正确');
        $now_time = date('Y-m-d H:i:s');

        if ($order->start_date . ' 14:00:00' > $now_time
            || date('Y-m-d H:i:s', strtotime("{$order->end_date} +1 day 12 hours")) < $now_time
        ) {
            return $this->error_response('未处于入住状态，无法执行退房操作');
        }
        if ($order->rent_type == 2) return $this->error_response('长租房无法提前退房');
        // 计算还可以退房的天数
        $refund_day_number = $this->repository->count_day(date('Y-m-d'), $order->end_date);
        // 若没有过今天的14点，则增加一天的退款
        if (date('H') < 14) $refund_day_number ++;
        $refund_money = ($order->rental_price / $order->housing_numbers) * $refund_day_number;  // 需要退款金额
        try {
            // 退款
            $this->userMoneyRepository->refund($user_id, $order_id, 2, $refund_day_number, $refund_money, '退款部分租金');

            // 修改订单状态
            $order->status = 3;     // 已退房
            $order->is_refunds = true;
            $order->refunds_total_money = $refund_money;
            $order->save();

            // 解除锁定
            RentalRecord::where('order_id', $order_id)->delete();

        }catch (\Exception $exception) {
            return $this->error_response('退款失败，请联系管理员');
        }

        return $this->no_content('退房成功');
    }

    /**
     * 阿姨查房操作
     *
     * 查房有通过和不通过两个路线
     * - 通过后退还押金
     * - 不通过时需要更改房屋状态为维修中
     *
     * @Post("rounds")
     *
     * @Parameters({
     *      @Parameter("id", description="订单id", required=true, type="integer")
     * })
     *
     * @Post("rounds")
     * @param HttpRequest $request
     */
    public function rounds(HttpRequest $request)
    {
        // todo 查房操作
    }

    /**
     * 查看房源出租日期情况
     *
     * @Get("apartment_use_status")
     *
     * @Parameters({
     *     @Parameter("start_date", description="查询开始时间"),
     *     @Parameter("end_date", description="查询结束时间")
     * })
     *
     * @param HttpRequest $request
     * @return array
     */
    public function apartment_use_status(HttpRequest $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $list = RentalRecord::where(function ($query) use ($start_date, $end_date){
                $query->whereBetween('start_date', [$start_date, $end_date]);
            })
            ->orWhere(function ($query) use ($start_date, $end_date) {
                $query->whereBetween('end_date', [$start_date, $end_date]);
            })
            ->get();
        return $this->array_response($list);
    }

}
