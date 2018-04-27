<?php

namespace App\Api\Controllers;

use App\Models\Articles;
use App\Models\Navigation;
use Dingo\Api\Http\Request;
use Dingo\Blueprint\Annotation\Method\Get;
use Dingo\Blueprint\Annotation\Resource;

/**
 * Class NavigationController
 * @package App\Api\Controllers
 * @Resource("Articles", uri="/information")
 */
class ArticlesController extends BaseController {
    /**
     * 获取文章详细信息
     * @Get("show")
     * @param $id
     * @return array
     */
    public function show($id){
        $article =Articles::find($id);
        return $this->array_response($article);
    }

    /**
     * 根据栏目名获取关于我们详细信息
     * @Get("show")
     * @param Request $request
     * @return array
     */
    public function showme(Request $request){
        $this->validate($request, [
            'navigation_type_id' =>'required|int'
        ]);
        $article =Articles::where('navigation_type_id',$request->input('navigation_type_id'))
            ->first();
        return $this->array_response($article);
    }

    /**
     * 获取文章列表
     *
     * @Get("index")
     * @param Request $request
     * @return array
     * @throws \InvalidArgumentException
     */
    public function index(Request $request){
        $this->validate($request, [
            'length' => 'int|min:1',
            'navigation_type_id' =>'required|int'
        ]);
        $length =$request->input('length',10);

        $articles = Articles::where('navigation_type_id', $request->input('navigation_type_id'))
            ->orderBy('id','desc')
            ->paginate($length, ['id', 'title', 'author','img_url', 'excerpt','created_at']);
        return $this->array_response($articles);
    }
}