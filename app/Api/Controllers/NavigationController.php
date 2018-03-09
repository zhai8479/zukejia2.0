<?php

namespace App\Api\Controllers;

use App\Models\Articles;
use App\Models\Navigation;
use Dingo\Api\Http\Request as HttpRequest;
use Dingo\Blueprint\Annotation\Method\Get;

/**
 * Class NavigationController
 * @package App\Api\Controllers
 * @Resource("Navigations", uri="/menu")
 */
class NavigationController extends BaseController
{
    /**
     * 根据默认值获得三个栏目菜单和文章名称
     * @Get("bottom")
     */
    public function bottom()
    {
        $navigations = Navigation::whereIn('id', [3, 4, 5])->get();
        foreach ($navigations as &$navigation) {
            $navigation->article_list = Articles::query()
                ->where('navigation_type_id', $navigation->id)
                ->take(2)
                ->get(['id', 'title']);
        }
        return $this->array_response($navigations);
    }

    /**
     * 获取帮助中心菜单名称
     * @Get("help_center_menu")
     * @return array
     */
    public function help_center_menu()
    {
        $navigations = Navigation::where('parent_id', 0)->get();
        $navigations->reject(function (&$nav) {
            $nav->childs = Navigation::where('parent_id', $nav->id)->get();
        });
        return $this->array_response($navigations);
    }

    /**
     * 获取资讯中心菜单名称
     * @Get("information_menu")
     * @return array
     */
    public function information_menu()
    {
        $navigations = Navigation::where('parent_id', 2)->get();
        return $this->array_response($navigations);
    }

    /**
     * 获取新手学堂栏目名和文章名
     * @Get("new_teach")
     * @return array
     */
    public function new_teach()
    {
        $navigations = Navigation::find(3);
        $navigations->article_list = Articles::where('navigation_type_id', 3)
            ->take(6)
            ->get(['id', 'title']);
        return $this->array_response($navigations);
    }

    /**
     * 新手常见问题列表
     * @Get("novice_common_problem_list")
     */
    public function novice_common_problem_list(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);
        return $this->common_problem_list(10, $request->input('length', 15));
    }

    /**
     * 账号常见问题列表
     * @Get("account_common_problem_list")
     */
    public function account_common_problem_list(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);
        return $this->common_problem_list(11, $request->input('length', 15));
    }

    /**
     * 项目常见问题列表
     * @Get("project_common_problem_list")
     */
    public function project_common_problem_list(HttpRequest $request)
    {
        $this->validate($request, [
            'length' => 'integer|min:1'
        ]);
        return $this->common_problem_list(12, $request->input('length', 15));
    }

    /**
     * 根据栏目id获取文章列表
     * @param $id
     * @param $length
     * @return array
     */
    protected function common_problem_list ($id, $length)
    {
        $navigation = Navigation::find($id);
        $navigation->articles = Articles::where('navigation_type_id', $id)
            ->take($length)
            ->get(['id', 'title']);
        return $this->array_response($navigation);
    }
}