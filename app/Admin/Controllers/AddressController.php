<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/13 14:34
 */

namespace App\Admin\Controllers;

use App\Models\ChainDistrict;
use Illuminate\Http\Request;

/**
 * Class DistrictController
 * @package App\Api\Controllers
 *
 * @Resource("District", uri="/district")
 *
 */
class AddressController
{
    /**
     * 获取省列表
     *
     * @Get("province_list")
     * @Parameters({
     *      @Parameter("id", description="province_id", required=true, type="string")
     * })
     *
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function province(Request $request)
    {
        $q = $request->get('q');
        $model = new ChainDistrict();
        $result = $model->where('parent_id', 0)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->name];
        });
        return $tmp;
    }

    /**
     * 获取市列表
     *
     * @Get("city_list")
     *
     * @Parameter("city_id", description="市id")
     *
     * @param Request $request
     * @return array
     */
    public function city(Request $request)
    {
        $q = $request->get('q');
        $model = new ChainDistrict();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->name];
        });
        return $tmp;
    }

    /**
     * 获取区列表
     *
     * @Get("city_list")
     *
     * @Parameter("district_id", description="区id")
     *
     * @param Request $request
     * @return array
     */
    public function district(Request $request)
    {
        $q = $request->get('q');
        $model = new ChainDistrict();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->name];
        });
        return $tmp;
    }
}