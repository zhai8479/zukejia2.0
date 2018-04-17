<?php

namespace App\Api\Controllers;

use App\Models\ChainDistrict;
use App\Models\Tags;
use App\Presenters\ApartmentPresenter;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;
use Illuminate\Http\Request;

use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use Illuminate\Http\Request as HttpRequest;
use App\Repositories\ApartmentRepository;
use App\Validators\ApartmentValidator;
use App\Models\Apartment;

/**
 * 房源控制器
 *
 * @Resource("Apartment", uri="house")
 *
 * Class ApartmentsController
 * @package App\Api\Controllers
 */
class ApartmentsController extends BaseController
{

    /**
     * @var ApartmentRepository
     */
    protected $repository;

    /**
     * @var ApartmentValidator
     */
    protected $validator;

    public function __construct(ApartmentRepository $repository, ApartmentValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }


    /**
     * 获取房源列表
     *
     * @Get("index")
     * @Versions({"v1"})
     * @Parameters({
     *      @Parameter("city", description="城市 integer"),
     *      @Parameter("district", description="区 integer"),
     *      @Parameter("start_date", description="时间范围开始值 格式1971-01-01  string"),
     *      @Parameter("end_date", description="时间范围结束值 格式1971-01-01 string"),
     *      @Parameter("type", description="出租类型 array"),
     *      @Parameter("direction", description="朝向 integer"),
     *      @Parameter("decoration_style", description="装修风格 integer"),
     *      @Parameter("start_price", description="价格范围开始值 float"),
     *      @Parameter("end_price", description="价格范围结束值 float"),
     *      @Parameter("room", description="房间数量 array"),
     *      @Parameter("facilities", description="设施 array"),
     *      @Parameter("rental_type", description="0-短租 1-长租 2-特价"),
     *      @Parameter("page", description="当前页 integer"),
     *      @Parameter("pageSize", description="页面大小 integer"),
     *      @Parameter("keyword",description="关键词  string")
     * })
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     *
     */
    public function index(HttpRequest $request)
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));

        $page = $request->input('page') ? (int)$request->input('page') : 1;
        $pageSize = $request->input('pageSize') ? (int)$request->input('pageSize') : 15;
        $city = $request->input('city') ? (int)$request->input('city') : 0;
        $district = $request->input('district') ? (int)$request->input('district') : 0;
        $startDate = $request->input('start_date') ? (string)$request->input('start_date') : '';
        $endDate = $request->input('end_date') ? (string)$request->input('end_date') : '';
        $type = $request->input('type') ? $request->input('type') : 0;
        $rental_type = $request->input('rental_type')?$request->input('rental_type'):0;
        $direction = $request->input('direction') ? (int)$request->input('direction') : 0;
        $decorationStyle = $request->input('decoration_style') ? (int)$request->input('decoration_style') : 0;
        $startPrice = $request->input('start_price') ? (float)$request->input('start_price') : 0.00;
        $endPrice = $request->input('end_price') ? (float)$request->input('end_price') : 0.00;
        $roomtemp = $request->input('room') ? $request->input('room')  : 0;
        $facilitiestemp = $request->input('facilities') ? $request->input('facilities') : 0;
        $keyword = $request->input('keyword');
        $user_id = $request->input( 'user_id');
        if ($page < 0) return response()->json([
            'error' => ['code' => -1, 'message' => 'page不能小于0']
        ]);

        $qr = Apartment::query();

        // 搜索条件
        if ($keyword) $qr = $qr->where('keyword','like',"%{$keyword}%");
        if($user_id) $qr = $qr->where('user_id',$user_id);
        if ($city) $qr = $qr->where('city', $city);
        if ($district) $qr = $qr->where('district', $district);
        if ($startDate && $endDate) $qr = $qr->whereBetween('updated_at', [$startDate, $endDate]);
        if ($type) $qr = $qr->whereIn('type', $type);
        if ($rental_type) $qr = $qr->whereIn('rental_type', $rental_type);
        if ($decorationStyle) $qr = $qr->where('decoration_style', $decorationStyle);
        if ($direction) $qr = $qr->where('direction', $direction);
        if ($endPrice) $qr = $qr->whereBetween('rental_price', [$startPrice, $endPrice]);
        $room = $roomtemp;
        if(strpos( $roomtemp,',')) {
            $room = explode(",", $roomtemp);
        }
        if ($room) {
            $otherRoom = '';
            if(is_array( $room)) {
                foreach ($room as $key => $value) {
                    if ($value == -1) {
                        $otherRoom = $value;
                        unset($room[$key]);
                    }
                }
            }
            if(is_array( $room)) {
                $qr = $qr->whereIn('room', $room);
            }
            else {
                $qr = $qr->where('room', $room);
            }
            if ($otherRoom) $qr = $qr->where('room', '>', 4);
        }
        $facilities = $facilitiestemp;

        if(strpos( $facilitiestemp,',')) {
            $facilities = explode(",", $facilitiestemp);
        }
        if ($facilities) {
            $raw = '';
            if(is_array( $facilities)) {
                foreach ($facilities as $value) {
                    if ($value == $facilities[0]) $raw = "FIND_IN_SET({$value},CONCAT(bathroom_utils,',',electrics,',',bed,',',kitchen_utils,',',facilities,',',requires))";
                    else $raw .= " AND FIND_IN_SET({$value},CONCAT(bathroom_utils,',',electrics,',',bed,',',kitchen_utils,',',facilities,',',requires))";
                }
            }
            else
            {
                $raw = "FIND_IN_SET({$facilities},CONCAT(bathroom_utils,',',electrics,',',bed,',',kitchen_utils,',',facilities,',',requires))";
            }
            $qr = $qr->whereRaw($raw);
        }
        $count = $qr->count();
        $totalPages = ceil($count / $pageSize) ;
        $apartments = $qr->limit($pageSize)->offset(($page - 1) * $pageSize)->get();

        $result = [];
        $apartments->reject(function($item)use(&$result, $apartments){
            $result[] = $item->indexListFilter($item);
        });
        return response()->json([
            'data' => $result,
            'code' => '0',
            'msg' => 'success',
            'pageinfo' => [
                'curPage' => $page,
                'pageSize' => $pageSize,
                'totalPages' => $totalPages
            ]
        ]);
    }




    /**
     * 获取房源详情
     *
     * - id 房源id
     * - province 省
     * - city 市
     * - district 区
     * - address 具体地址
     * - status 状态
     * - title 标题
     * - desc 个性描述
     * - inner_desc 内部描述
     * - traffic_desc 交通情况
     * - environment 周边环境
     * - type 房屋类型
     * - room 房间数量
     * - hall 大厅数量
     * - bathroom 卫生间数量
     * - kitchen 厨房数量
     * - balcony 阳台数量
     * - area 房屋面积
     * - decoration_style 装修风格
     * - direction 房屋朝向
     * - bathroom_utils 卫浴
     * - electrics 电器
     * - bed 床
     * - kitchen_utils 厨房用品
     * - facilities 设备
     * - requires 要求
     * - images 图片地址
     * - search_address 详细地址
     * - rental_type 价格规则
     * - rental_price 房价
     * - rental_deposit 押金
     * - created_at 创建时间
     * - updated_at 最近发布时间
     *
     * @Get("show")
     *
     * @Parameters({
     *     @Parameter("id", description="房源id")
     * })
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $apartment = $this->repository->find($id);
        return $this->array_response([$apartment],'success');

    }

    /**
     * 获取城市信息
     *
     * - c_name     城市姓名
     * - c_id       城市ID
     * - pinyin     汉语拼音
     * - initial    首字母
     * - initials   每个汉字首字母
     * - number     城市中的房源总数
     *
     * @Get("city")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function city(HttpRequest $request)
    {
        $open = $request->input('open') ? $request->input('open')  : 0;
        $strwhere = '';
        $strwhere1= '';
        if($open>0)
        {
            $strwhere = ' AND is_open_shop = 1';
            $strwhere1= ' WHERE city.is_open_shop = 1';
        }
        $apartment = \DB::select("
            SELECT
                city. NAME AS c_name,
                city.id AS c_id,
                city.pinyin,
                city.initial,
                city.initials,
                COUNT(apartment.id) AS house_number
            FROM
                (
                    SELECT
                        *
                    FROM
                        chain_district
                    WHERE
                        id IN (1, 2, 3, 4) ".$strwhere."
                ) AS city
            LEFT JOIN apartment ON city.id = apartment.city
            GROUP BY
                city.id
            UNION
            SELECT
                city.`name` AS c_name,
                city.id AS c_id,
                city.pinyin,
                city.initial,
                city.initials,
                COUNT(apartment.id) AS house_number
            FROM
                (
                    SELECT
                        *
                    FROM
                        chain_district
                    WHERE
                        parent_id = 0
                    AND id NOT IN (1, 2, 3, 4)
                ) AS province
            INNER JOIN chain_district AS city ON city.parent_id = province.id
            LEFT JOIN apartment ON city.id = apartment.city
            ".$strwhere1."
            GROUP BY
                city.id
	    ");

        return $this->array_response($apartment,'success');

    }

    /**
     * 获取行政区域信息
     *
     * - id
     * - name 行政区名字
     * - pinyin 拼音
     * - initial 第一个字首字母
     * - initials 每个字首字母
     *
     * @Get("district")
     *
     * @Parameters({
     *     @Parameter("city_id", description="城市ID")
     * })
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function district(HttpRequest $requesst)
    {
        $cityId = $requesst->input('city_id') ? $requesst->input('city_id') : 2009;
        $model = new ChainDistrict();
        $result = $model
            ->where('parent_id', $cityId)
            ->get([
                'id',
                'name',
                'pinyin',
                'initial',
                'initials'
            ]);

        return $this->array_response([$result],'success');

    }

    /**
     * 获取装修风格
     *
     * - id
     * - name 风格名称
     *
     * @Get("decoration_style")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function decorationStyle()
    {
        $model = new Tags();
        $result = $model
            ->where('type',1)
            ->where('name', '<>', '装修风格')
            ->get([
                'id',
                'name'
            ]);

        return $this->array_response([$result],'success');
    }

    /**
     * 获取房屋朝向
     *
     * - id
     * - name 房屋朝向
     *
     * @Get("direction")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function direction()
    {
        $model = new Tags();
        $result = $model
            ->where('type',2)
            ->where('name', '<>', '房屋朝向')
            ->get([
                'id',
                'name'
            ]);

        return $this->array_response([$result],'success');

    }

    /**
     * 获取房屋类型
     *
     * - value
     * - name 房屋类型
     *
     * @Get("type")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function type()
    {
        $result = [
            ['name' => '整体出租', 'value' => 1],
            ['name' => '独立单间', 'value' => 2],
            ['name' => '合租房屋', 'value' => 3],
            ['name' => '酒店式公寓', 'value' => 4],
        ];

        return $this->array_response([$result],'success');

    }

    /**
     * 获取房屋户型
     *
     * - value 值
     * - name 房屋户型
     *
     * @Get("room")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function room()
    {
        $result = [
            ['name' => '一居', 'value' => '1'],
            ['name' => '二居', 'value' => '2'],
            ['name' => '三居', 'value' => '3'],
            ['name' => '四居', 'value' => '4'],
            ['name' => '其它', 'value' => '-1'],
        ];

        return $this->array_response([$result],'success');

    }

    /**
     * 获取配套设施
     *
     * - id
     * - name 配套设施
     *
     * @Get("facilities")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function facilities()
    {
        $model = new Tags();
        $result = [
            $model->where('name', '淋浴')->get(['name', 'id'])->first(),
            $model->where('name', '空调')->get(['name', 'id'])->first(),
            $model->where('name', '电视')->get(['name', 'id'])->first(),
            $model->where('name', '有线网络')->get(['name', 'id'])->first(),
            $model->where('name', '无线网络')->get(['name', 'id'])->first(),
            $model->where('name', '允许做饭')->get(['name', 'id'])->first(),
            $model->where('name', '暖气')->get(['name', 'id'])->first(),
            $model->where('name', '独立卫生间')->get(['name', 'id'])->first()
        ];

        return $this->array_response([$result],'success');

    }

    /**
     * 获取推荐列表
     *
     * - id
     * - name 推荐列表
     *
     * @Get("get_recommend_list")
     *
     */
    public function getRecommendList()
    {
        $qr = new Apartment();

        $apartments = $qr->where('status', '4')->get();
        $apartments = $qr->where('status', '4')->limit(6)->get();

        $result = [];
        $apartments->reject(function($item)use(&$result, $apartments){
            $result[] = $item->indexListFilter($item);
        });

        return $this->array_response([$result],'success');
    }

    /**
     * 获取热门列表
     *
     * - id
     * - name 热门列表
     *
     * @Get("get_hot_list")
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHotList()
    {
        $qr = new Apartment();

        $apartments = $qr->where('status', '5')->limit(6)->get();

        $result = [];
        $apartments->reject(function($item)use(&$result, $apartments){
            $result[] = $item->indexListFilter($item);
        });
        return $this->array_response([$result],'success');
    }

    /**
     * 获取不同类型房源列表
     *
     * @Get("get_rental_list")
     *
     * @Parameters({
     *     @Parameter("rental_type", description="0-短租 1-长租 2-特价")
     * })
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRentalList(HttpRequest $request)
    {
        $rentalType = $request->input('rental_type');
        $qr = new Apartment();

        $apartments = $qr->where('rental_type', $rentalType)->limit(9)->get();
        $result = [];
        $apartments->reject(function($item)use(&$result, $apartments){
            $result[] = $item->indexListFilter($item);
        });
        return $this->array_response([$result],'success');
    }

    /**
     * 获取城市信息
     *
     * - address     城市姓名
     * - city_id     城市ID
     * - city_name   城市名称
     *
     * @GET("city_key")
     *
     * @param HttpRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_city_by_key(HttpRequest $request)
    {
        $key = $request->input('city_key');
        $cities = \DB::select("
            SELECT
                apartment.search_address AS address,
                apartment.city AS city_id,
                city.name AS city_name,
                province.name AS province_name
            FROM
                apartment
            JOIN chain_district AS city ON city.id = apartment.city
            JOIN chain_district AS province ON province.id = apartment.province
            WHERE
                apartment.city IN (
                    SELECT
                        city.id AS city
                    FROM
                        (
                            SELECT
                                *
                            FROM
                                chain_district
                            WHERE
                                id IN (
                                    SELECT
                                        city
                                    FROM
                                        apartment
                                    WHERE
                                        search_address LIKE \"%{$key}%\"
                                )
                        ) AS city
                    WHERE
                        search_address LIKE \"%{$key}%\"
                    GROUP BY
                        city.id
                )
            LIMIT 0,10
	    ");

        return $this->array_response([$cities],'success');
    }
    /**
     * 获取更多类型信息列表
     */
    public function more(){
        $model = new Tags();
        $decorationStyle = $model
            ->where('type',1)
            ->where('name', '<>', '装修风格')
            ->get([
                'id',
                'name'
            ]);
        $direction = $model
            ->where('type',2)
            ->where('name', '<>', '房屋朝向')
            ->get([
                'id',
                'name'
            ]);
        $facilities = [
            $model->where('name', '淋浴')->get(['name', 'id'])->first(),
            $model->where('name', '空调')->get(['name', 'id'])->first(),
            $model->where('name', '电视')->get(['name', 'id'])->first(),
            $model->where('name', '有线网络')->get(['name', 'id'])->first(),
            $model->where('name', '无线网络')->get(['name', 'id'])->first(),
            $model->where('name', '允许做饭')->get(['name', 'id'])->first(),
            $model->where('name', '暖气')->get(['name', 'id'])->first(),
            $model->where('name', '独立卫生间')->get(['name', 'id'])->first()
        ];
        $type = [
            ['name' => '整体出租', 'value' => 1],
            ['name' => '独立单间', 'value' => 2],
            ['name' => '合租房屋', 'value' => 3],
            ['name' => '酒店式公寓', 'value' => 4],
        ];
        $room = [
            ['name' => '一居', 'value' => '1'],
            ['name' => '二居', 'value' => '2'],
            ['name' => '三居', 'value' => '3'],
            ['name' => '四居', 'value' => '4'],
            ['name' => '其它', 'value' => '-1'],
        ];
        return $this->array_response(['type'=>$type,'direction'=>$direction,'decorationStyle'=>$decorationStyle,'room'=>$room,'facilities'=>$facilities],'success');
    }
}
