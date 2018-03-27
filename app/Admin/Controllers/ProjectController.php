<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Upload;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\MessageBag;

class ProjectController extends Controller
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

            $content->header('标的');
            $content->description('列表');

            $content->body($this->grid());
        });
    }

    /**
     * 显示项目详情
     * @param integer   $id 项目id
     * @return Content
     */
    public function show($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('标的');
            $content->description('详情');

            $content->body(Admin::form(Project::class, function (Form $form) use ($id) {
                $project = Project::find($id);
                $project->status = Project::$status_list[$project->status];
                $project->house_status = Project::$house_status_list[$project->house_status];
                $project->house_management_status = Project::$status_list[$project->house_management_status];

                $form->display('id', 'ID')->value($project->id);
                $form->display('name', '项目名称')->value($project->name);
                $form->display('type_id', '类型id')->value($project->type_id);
                $form->display('money', '项目价格')->value($project->money);
                $form->display('status', '项目状态')->value($project->status);
                $form->display('issue_total_num', '项目总期数')->value($project->issue_total_num);
                $form->display('issue_day_num', '项目除期外的天数')->value($project->issue_day_num);
                $form->display('issue_explain', '期说明')->value($project->issue_explain);
                $form->display('rental_money', '租房价格')->value($project->rental_money);
                $form->display('collect_money', '收房价格')->value($project->collect_money);
                $form->display('characteristic', '项目特点')->value($project->characteristic);
                $form->display('house_address', '房屋地址')->value($project->house_address);
                $form->display('house_status', '房屋状况')->value($project->house_status);
                $form->display('house_id', '房屋id')->value($project->house_id);
                $form->display('house_area', '房屋面积')->value($project->house_area);
                $form->display('house_competitive_power', '房屋竞争力')->value($project->house_competitive_power);
                $form->display('house_management_status', '经营状况')->value($project->house_management_status);
                $form->display('house_property_certificate', '房产证号')->value($project->house_property_certificate);
                $form->display('house_id_card', '房主身份证号')->value($project->house_id_card);
                $form->display('house_residence', '房主户口本编号')->value($project->house_residence);
                $form->multipleImage('house_contract_img_ids', '房主资料图片')->readOnly()->value($project->house_contract_img_ids);
                $form->file('contract_file_name', '合同文件')->readOnly();
                $form->display('risk_assessment', '风险评估')->value($project->risk_assessment);
                $form->display('safeguard_measures', '保障措施')->value($project->safeguard_measures);
                $form->display('guarantor', '担保方')->value($project->guarantor);
                $form->display('created_at', '注册时间')->readOnly()->value($project->created_at);

                $form->disableReset();
                $form->disableSubmit();
            }));
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

            $content->header('标的');
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

            $content->header('标的');
            $content->description('创建');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Project::class, function (Grid $grid) {
            $grid->model()->orderBy('weight', 'asc')->orderBy('id', 'desc');
            $grid->id('编号')->sortable();
            $grid->type_id('标的类型')->display(function ($value) {
                $projectTypeModel = ProjectType::find($value);
                return $projectTypeModel->name;
            });
            $grid->name('标的名称');
            $grid->status('状态')->display(function ($status) {
                return Project::$status_list[$status];
            });
            $grid->money('标的价格')->display(function ($money) {
                return $money . ' 元';
            })->sortable();
            $grid->column('客户收益')->display(function () {
                return ($this->rental_money - $this->collect_money) * 100 . ' 元/期';
            });
            $grid->rental_money('租房价格')->display(function ($money) {
                return $money . ' 元';
            })->sortable();
            $grid->collect_money('收房价格')->display(function ($money) {
                return $money . ' 元';
            })->sortable();
            $grid->issue_total_num('期数')->sortable();
            $grid->issue_day_num('天数')->sortable();
            $grid->house_address('房屋地址');

            $grid->start_at('开始时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });
            $grid->end_at('结束时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });
            $grid->weight('权重')->editable();
            $grid->created_at('上标时间')->display(function($value){
                return date('Y-m-d', strtotime($value));
            });

            $grid->is_show('前端显示')->display(function ($value) {
                return $value == 1 ? '显示' : '不显示';
            });

            $grid->disableRowSelector();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $show_project_url = "/admin/project/show/{$actions->getKey()}";
                $actions->append("<a href=\"$show_project_url\"><i class=\"fa fa-eye\">详情</i></a>");
            });
            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('status', '状态')
                    ->select(Project::$status_list);
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Project::class, function (Form $form) {

            $form->tab('标的信息', function (Form $form) {
                if ($form->input('id')) {
                    $form->display('id', 'ID');
                }
                $form->select('type_id', '标的类型')->options(function () {
                    $projectType = \DB::table('project_types')
                        ->select(['name', 'id'])
                        ->get();
                    $tmp = [];
                    $projectType->reject(function ($element) use (&$tmp) {
                        $tmp[$element->id] = $element->name;
                    });
                    return $tmp;
                })->rules('required');

                $form->text('name', '标的名称')->rules('required');

                $form->currency('money', '标的价格')->symbol('元')->rules('required');
                $form->currency('rental_money', '租赁价格')->symbol('元')->rules('required');
                $form->currency('collect_money', '收房价格')->symbol('元')->rules('required');

                // 计算得出
                $form->financial_term('issue_total_num', '期数');
                $form->benefit('benefit', '每期本金');
                $form->last_benefit('last_benefit', '尾期本金');

                $notation = '服务收益计算方式为：
收益期限=投资总额/原始租赁价格（收益期每期为30天，多余部分按照每天计算，例如：50000/1300=38.46期，换算期限为38期，剩余天数按照0.46*30=14天，则客户的收益期限为38期零14天）
等额本息方式=每期本金+溢价金额*80%（例如：50000/1300=38.46期，换算期限为38期，剩余天数按照0.46*30=14天，则客户的收益期限为38期零14天，38期收益为每期本金+溢价金额*80%，14天收益为（每期本金+溢价金额*80%）/30*14）
收益还款期时间=每月15号、30号为收益还款期';
                $form->textarea('issue_explain', '期限说明')->value($notation);
                $form->number('weight', '权重');
                $form->textarea('characteristic', '项目特点');
           //     $form->image('contract_file_name', '合同文件');
            })->tab('房屋信息', function (Form $form) {
                $form->text('house_address', '房屋地址')->rules('required');
                $form->select('house_status', '房屋状态')->options([1 => '优秀', 2 => '良好', 3 => '差']);
                $form->number('house_area', '面积（m²）');
                $form->number('house_id','房屋编号');
                $form->textarea('house_competitive_power', '房屋核心竞争力');
                $form->select('house_management_status', '房屋状态')->options([1 => '筹备中', 2 => '装修中', 3 => '运营中', 4 => '暂停运营', 5 => '下架'])->rules('required');
            })->tab('房主信息', function (Form $form) {
                $form->text('house_property_certificate', '房产证号')->rules('required');
                $form->text('house_id_card', '身份证号')->rules('required|numeric');
                $form->text('house_residence', '户口本编号')->rules('required');
       //         $form->multipleImage('house_contract_img_ids','图片')->rules('required')->removable();
            })->tab('风险评估', function (Form $form) {
                $form->text('risk_assessment', '风险评估');
                $form->text('safeguard_measures', '保障措施');
                $form->text('guarantor', '担保方');
            })->tab('定时发标', function (Form $form) {
                $form->datetime('start_at', '开始时间')->format('YYYY-MM-DD HH:mm:ss');
                $form->datetime('end_at', '结束时间')->format('YYYY-MM-DD HH:mm:ss');
                $form->hidden('admin_id')->default(Admin::user()->id);
                $form->hidden('status')->default(Project::STATUS_WAIT_START);
            });

            $form->saving(function (Form $form) {
                //如果默认开始时间为空，则设定为当前时间
                $start_at = $form->start_at;
                if (empty($start_at)) {
                    $form->input('start_at',date('Y-m-d H:i:s'));
                };
                // 判断开始时间和结束时间, 直接设定状态
                $time_status = Project::check_time_is_start($form->input('start_at'), $form->input('end_at'));
                if ($time_status === 0) {
                    $form->input('status', Project::STATUS_PROCESS);
                }
                if ($time_status === -1) {
                    $form->input('status', Project::STATUS_WAIT_START);
                }
                if ($time_status === 1) {
                    $error = new MessageBag([
                        'title' => '表单错误',
                        'message' => '项目结束时间不允许小于当前时间'
                    ]);
                }
                if ($form->input('rental_money') < $form->input('collect_money')) {
                    $error = new MessageBag([
                        'title' => '表单错误',
                        'message' => '出租价格不允许小于收房价格'
                    ]);
                }
                if ($form->input('end_at') < $form->input('start_at')) {
                    $error = new MessageBag([
                        'title' => '表单错误',
                        'message' => '项目结束时间不允许小于开始时间'
                    ]);
                }

                if (isset($error)) return back()->withInput()->with(compact('error'));
            });

            $form->ignore(['benefit', 'last_benefit','duration']);
        });
    }
}
