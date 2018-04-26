<?php
/**
 * @desc
 * @author zhan <grianchan@gmail.com>
 * @since 2017/9/13 14:34
 */

namespace App\Admin\Controllers;

use App\Models\City;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Form;

/**
 * Class DistrictController
 * @package App\Api\Controllers
 *
 * @Resource("District", uri="/district")
 *
 */
class AddressController extends BaseController
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
    public function getData(Request $request)
    {
        $q = $request->get('q');
        $model = new City();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->title];
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
        $model = new City();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->title];
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
        $model = new City();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->title];
        });
        return $tmp;
    }

    /**
     * 获取商圈列表
     *
     * @Get("Business_circle")
     *
     * @Parameter("district_id", description="区id")
     *
     * @param Request $request
     * @return array
     */
    public function Business_circle(Request $request)
    {
        $q = $request->get('q');
        $model = new City();
        $result = $model->where('parent_id', $q)->get();
        $tmp = ['data' => []];
        $result->reject(function($element)use(&$tmp){
            $tmp['data'][] = ['id' => $element->id, 'text' => $element->title];
        });
        return $tmp;
    }

    use ModelForm;
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('城市管理');
            $content->body(City::tree());
        });
    }

    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('城市管理');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    protected function form()
    {
        return Admin::form(City::class, function (Form $form) {

            $form->display('id', 'ID');
            $options = $this->city_list(0);
            $options[0] = '中国';
            $form->select('parent_id','所属区域')->options($options);
            $form->text('title','区域名');
        });
    }
}