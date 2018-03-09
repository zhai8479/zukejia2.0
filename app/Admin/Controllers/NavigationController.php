<?php
namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Navigation;
use Encore\Admin\Form;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class NavigationController extends BaseController
{
    use ModelForm;
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('栏目管理');
            $content->body(Navigation::tree());
        });
    }
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('栏目管理');
            $content->description('编辑');

            $content->body($this->form()->edit($id));
        });
    }

    public function destroy($id)
    {
        if ($id <= 9) {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed_by_default'),
            ]);
        } else {
            if ($this->form()->destroy($id)) {
                return response()->json([
                    'status'  => true,
                    'message' => trans('admin.delete_succeeded'),
                ]);
            } else {
                return response()->json([
                    'status'  => false,
                    'message' => trans('admin.delete_failed'),
                ]);
            }
        }
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
        return Admin::form(Navigation::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('title','栏目名');
            $options = $this->navigation_type_list(0);
            $options[0] = '根目录';
            $form->select('parent_id','所属栏目')->options($options);
        });
    }
}
