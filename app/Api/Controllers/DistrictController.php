<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/13 14:34
 */

namespace App\Api\Controllers;

use App\Models\ChainDistrict;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Method\Post;
use Dingo\Blueprint\Annotation\Parameter;
use Dingo\Blueprint\Annotation\Parameters;
use Dingo\Blueprint\Annotation\Request;
use Dingo\Blueprint\Annotation\Resource;
use Dingo\Blueprint\Annotation\Response;
use Dingo\Blueprint\Annotation\Transaction;
use Dingo\Blueprint\Annotation\Versions;
use Illuminate\Http\Request as HttpRequest;

/**
 * Class DistrictController
 * @package App\Api\Controllers
 *
 * @Resource("District", uri="/district")
 *
 */
class DistrictController extends BaseController
{

    /**
     * 获取省列表
     *
     * @Get("province_list")
     *
     */
    public function province_list()
    {
        return ChainDistrict::where('parent_id', 0)->get();
    }

    /**
     * 获取市列表
     *
     * @Get("city_list")
     *
     * @Parameter("province_id", description="省id")
     *
     * @param HttpRequest $request
     * @return $this
     */
    public function city_list(HttpRequest $request)
    {
        $this->validate($request, [
            'province_id' => 'required|integer|min:1'
        ]);
        $province_id = $request->input('province_id');
        return ChainDistrict::where('parent_id', $province_id)->get();
    }

    /**
     * 根据省代码，查询市列表
     *
     *
     * @Get("city_list_by_province_code")
     *
     * @Parameters({
     *      @Parameter("province_code", description="省代码", required=true, type="integer")
     * })
     *
     * @param HttpRequest $request
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function city_list_by_province_code(HttpRequest $request)
    {
        $this->validate($request, [
            'province_code' => 'required|integer|min:1|exists:chain_district,code'
        ]);
        $province_code = $request->input('province_code');
        $province_id = ChainDistrict::where('code', $province_code)->value('id');
        return ChainDistrict::where('parent_id', $province_id)->get();
    }
}
