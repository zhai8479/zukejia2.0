<?php

namespace App\Admin\Controllers;

use App\Traits\OSS;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Version;
use App\Library\OSSHelp;

class VersionContorller extends BaseController
{
    use ModelForm;

    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('APP版本管理');
            $content->body($this->grid());
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
            $content->header('发布新版本');
            $content->description('发布');
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
        return Admin::grid(Version::class, function (Grid $grid){

            $grid->model()->orderBy('created_at', 'desc');

            $grid->id('编号');

            $grid->type('更新类型')->display(function ($value) {
                if ($value == '1') return '非强制更新';
                else  return '强制更新';
            });
            $grid->version('版本号');
            $grid->url('app下载地址');
            $grid->message('更新说明');
            $grid->created_at('创建时间');
            $grid->actions(function ($action) {
                $action->disableEdit();
                $action->disableDelete();
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
        return Admin::form(Version::class, function (Form $form) {

            $form->display('id', 'ID');
//            $form->number('type','更新类型')->rules('required');
            $form->select('type', '更新类型')->options(['1' => '非强制更新', '2' => '强制更新'])->rules('required');
            $form->text('version','版本号')->rules('required');
            $form->file('url','上传APP')->move('','zukehouse-app-release.apk')->rules('required');
            $form->text('message', '更新说明')->rules('required');
            $oss = new OSSHelp(false);
            $path = getcwd() . '/uploads/file/';
            $oss->uploadFile('zkj-static','sdk/zukehouse-app-release.apk',$path.'zukehouse-app-release.apk');
        });
    }


}
