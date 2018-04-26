<?php
/**
 * Created by PhpStorm.
 * Developer: <kelvenchi@perlface.net>
 * Company: EasyLifeHome Network Technology, HB, Ltd, co,.
 * Date: 2017/9/20
 * Time: 13:05
 */

namespace App\Admin\Controllers;

use App\Models\Apartment;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\City;
use App\Models\Tags;
use Tests\Models\Tag;
use Illuminate\Support\MessageBag;

class ApartmentController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('房源');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function user_apartment_index($user_id)
    {
        return Admin::content(function (Content $content) use ($user_id){

            $content->header('房源');
            $content->description('列表');

            $content->body($this->grid($user_id));
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('房源');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }



    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('房源');
            $content->description('创建');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid($user_id = null)
    {
        return Admin::grid(Apartment::class, function (Grid $grid) use ($user_id){
            if (null !== $user_id) $grid->model()->where('user_id', $user_id);

            $grid->model()->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->title('房屋标题');
            $grid->column('房屋地址')->display(function () {
                $temp = new City();
                $province = $temp->Find($this->province);
                $city = $temp->Find($this->city);
                $district = $temp->Find($this->district);
                $Business_circle = $temp->Find($this->Business_circle);
                $address =
                    $province->title . $province->suffix .
                    $city->title . $city->suffix;
                if ($district) {
                    $address .= $district->title . $district->suffix;
                }
                if ($Business_circle) {
                    $address .= $Business_circle->title . $Business_circle->suffix;
                }
                $address .= $this->address;
                return $address;
            });

            $grid->column('房屋面积/风格')->display(function () {
                $tag_name = Tags::query()->where('id', $this->decoration_style)->value('name');
                return $this->area . 'm²/' . $tag_name;
            });

            $grid->rental_type('价格规则')->display(function ($value) {
                if ($value == 0) return '短租';
                else if ($value == 1) return '长租';
                return '特价';
            });

            $grid->column('出租价格')->display(function () {
                return '￥' . $this->rental_price;
            });

            $grid->column('押金')->display(function () {
                return '￥' . $this->rental_deposit;
            });

            $grid->status('状态')->display(function ($value) {
                $result = '';
                switch ($value) {
                    case 1:
                        $result = '热销中';
                        break;
                    case 2:
                        $result = '整理中';
                        break;
                    case 3:
                        $result = '预租中';
                        break;
                    case 4:
                        $result = '出租中';
                        break;
                    default:
                        $result = '错误状态';
                }
                return $result;
            });

            $grid->click_num('点击率')->editable();

            $grid->created_at('创建时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });
            $grid->updated_at('发布时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });;
            $grid->actions(function($actions) use($grid) {
                $actions->disableDelete();
                $id = $actions->getKey();
                $actions->prepend('<a href="http://www.zukehouse.com/house_details?house_id=' . $id . ' "target="_blank" title="详情" style="padding-right: 5px"><i class="fa fa-eye" aria-hidden="true"></i></a>');
            });

            $grid->filter(function ($filter) {
                // 禁用id查
                $filter->disableIdFilter();

                // 租赁规则过滤器
                $filter->where(function ($query) {
                    $input = $this->input - 1;
                    $query->where('rental_type', $input);
                }, '价格规则')->select(['0' => '短租', '1' => '长租','2' => '特价']);

                // 出租类型过滤器
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('type', $input);
                }, '出租类型')->select(['1' => '整租', '2' => '独立单间', '3' => '合租', '4' => '酒店式公寓']);

                // 房屋状态过滤器
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('status', $input);
                }, '房屋状态')->select(['1' => '热销中', '2' => '整理中', '3' => '预租中', '4' => '出租中']);

                // 文本过滤器嫩绿
                $filter->where(function ($query) {
                    $input = $this->input;

                    $query->where('id', '=', $input)->orWhere('search_address', 'like', "%{$input}%");

                }, '编号或地址');
            });
            //如果带了用户id，房源的操作按钮会被屏蔽
      //      if (null !== $user_id)$grid->disableActions();
        });
    }

    /**
     * 新增或编辑
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Apartment::class, function (Form $form) {
            $form->tab('房屋地址', function ($form) {

                $form->select('province', '省')->options(function(){
                    $provinceModel = new City();
                    $province = $provinceModel->where('parent_id', '=', 0)->get(['title','id']);
                    $tmp = [];
                    $province->reject(function($element)use(&$tmp){
                        $tmp[$element->id] = $element->title;
                    });
                    return $tmp;
                })->load('city', '/admin/api/getData')->rules('required');

                $form->select('city', '市')->options(function () {
                    $cityModel = new City();
                    $province = $this->province;
                    $city = $cityModel->where('parent_id', '=', $province)->get(['title','id']);
                    $tmp = [];
                    $city->reject(function($element)use(&$tmp){
                        $tmp[$element->id] = $element->title;
                    });
                    return $tmp;
                })->load('district', '/admin/api/getData')->rules('required');

                $form->select('district', '区')->options(function () {
                    $districtModel = new City();
                    $city = $this->city;
                    $district = $districtModel->where('parent_id', '=', $city)->get(['title','id']);
                    $tmp = [];
                    $district->reject(function($element)use(&$tmp){
                        $tmp[$element->id] = $element->title;
                    });
                    return $tmp;
                })->load('business', '/admin/api/getData')->rules('required');

                $form->select('business', '商圈')->options(function () {
                    $businessModel = new City();
                    $district = $this->district;
                    $business = $businessModel->where('parent_id', '=', $district)->get(['title','id']);
                    $tmp = [];
                    $business->reject(function($element)use(&$tmp){
                        $tmp[$element->id] = $element->title;
                    });
                    return $tmp;
                });


                $form->select('status', '状态')->options([1 => '热销中', 2 => '整理中', 3 => '预租中', 4 => '已出租']);

                $form->radio('is_Commend', '是否推荐')->options(['是' => '是', '否' => '否'])->default('否');

                $form->number('click_num', '点击率')->rules('required|numeric|min:0');

                $form->text('address', '详细地址')->rules('required');

                $form->hidden('id');

//                $form->saveing(function (Form $form){
//
//                });

            })->tab('基本情况', function ($form) {
                $form->radio('type', '出租类型')->values([
                    1 => '整体出租',
                    2 => '独立单间',
                    3 => '合租房屋',
                    4 => '酒店式公寓'
                ])->default(1);

                $form->number('total_floor', '总楼层')->rules('required|numeric|min:1');
                $form->number('current_floor', '当前楼层')->rules('required|numeric|min:1');
                $form->number('room', '室')->rules('required|numeric|min:1');
                $form->number('hall', '厅')->rules('required|numeric|min:0');
                $form->number('bathroom', '卫')->rules('required|numeric|min:0');
                $form->number('kitchen', '厨')->rules('required|numeric|min:0');
                $form->number('balcony', '阳台')->rules('required|numeric|min:0');

                $form->number('area', '面积（m²）')->rules('required|numeric|min:0');

                $form->select('decoration_style','房屋风格')->options(function(){
                    $model = new Tags();
                    $decorationStyle = $model->where('type', '=', 1)
                        ->where('parent_id', '<>', 0)
                        ->get();
                    $tmp = [];
                    $decorationStyle->reject(function ($item)use(&$tmp){
                        $tmp[$item->id] = $item->name;
                    });
                    return $tmp;
                });

                $form->select('direction','房屋朝向')->options(function(){
                    $model = new Tags();
                    $decorationStyle = $model->where('type', '=', 2)
                        ->where('parent_id', '<>', 0)
                        ->get();
                    $tmp = [];
                    $decorationStyle->reject(function ($item)use(&$tmp){
                        $tmp[$item->id] = $item->name;
                    });
                    return $tmp;
                });

                $form->bed_line('single_bed', '单人床');
                $form->bed_line('double_bed', '双人床');
                $form->bed_line('tatami', '榻榻米');
                $form->bed_line('round_bed', '圆床');
                $form->bed_line('big_bed', '大床');

            })->tab('房屋描述', function ($form) {
                $form->text('title', '房屋标题')->rules('required');
                $form->text('user_id','房东信息');
                $form->textarea('desc', '个性描述')->rules('required');
                $form->textarea('inner_desc', '内部描述');
                $form->textarea('traffic_desc', '交通情况');
                $form->textarea('environment', '周边环境');
            })->tab('配套设施', function ($form) {
                $model = new Tags();
                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '卫浴')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });
                $form->checkbox('bathroom_utils', '卫浴')->options($tmp);

                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '电器')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });
                $form->checkbox('electrics', '电器')->options($tmp);

                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '床')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });
                $form->checkbox('bed', '床')->options($tmp);

                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '厨房')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });
                $form->checkbox('kitchen_utils', '厨房')->options($tmp);

                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '设备')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });
                $form->checkbox('facilities', '设备')->options($tmp);

                $bathroomUtils = $model->where('type', '=', 3)
                    ->where('parent_id', '<>', 0)
                    ->where('desc', '要求')
                    ->get();
                $tmp = [];
                $bathroomUtils->reject(function ($item)use(&$tmp){
                    $tmp[$item->id] = $item->name;
                });

                $form->checkbox('requires', '要求')->options($tmp);
            })->tab('真实拍照', function ($form) {

                $form->multipleImage('images','图片')->rules('required')->removable();

            })->tab('价格规则', function ($form) {
                $form->hidden('search_address');
                $form->hidden('keyword');

                $form->radio('rental_type', '价格规则')->options([0 => '短租', 1 => '长租',2 => '特价'])->default(1);
                $form->currency('rental_price', '租金')->symbol('￥')->rules('required');
                $form->currency('rental_deposit', '押金')->symbol('￥')->rules('required');
            });

            $form->ignore(['id']);

            $form->saving(function (Form $form) {

//                if (isset($error)) return back()->withInput()->with(compact('error'));
                if ($form->province) {
                    $model = new City();

                    $province = $model->where('id', $form->province)->first();
                    $city = $model->where('id', $form->city)->first();
                    $district = $model->where('id', $form->district)->first();

                    $address = $province->title . $province->suffix . $city->title . $city->suffix;

                    if ($district) {
                        $address .= $district->title . $district->suffix;
                    } else {
                        $form->district = 0;
                    }

                    $address .= $form->address;
                    $form->search_address = $address;
                    $title =$form->title;
                    $desc =$form->desc;
                    $inner_desc =$form->inner_desc;
                    $traffic_desc =$form->traffic_desc;
                    $environment =$form->environment;
                    $keyword = $address . $title . $desc . $inner_desc . $traffic_desc . $environment;
                    $form->keyword = $keyword;
                    if (! $form->district) $form->district = 0;
                }
            });
        });
    }
}
