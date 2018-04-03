<?php

namespace App\Api\Controllers;

use App\Models\Banner;
use Dingo\Api\Http\Request;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;

/**
 * Class NavigationController
 * @package App\Api\Controllers
 * @Resource("Banners", uri="/banner")
 */
class BannerController extends BaseController {
    /**
     * 获取轮播列表
     *
     * @Get("index")
     * @param Request $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function index(Request $request){
        $this->validate($request, [
            'length' => 'int|min:1',
        ]);
        $length =$request->input('length',10);

        $banners = Banner::orderBy('order','desc')
            ->paginate($length, ['id', 'title', 'link','banner_url','order','created_at']);
        return $this->array_response($banners);
    }
}