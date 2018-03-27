<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/12/5
 * Time: 14:23
 */

namespace App\Admin\Extensions\Tools;


use Encore\Admin\Grid\Tools\BatchAction;

class EarlyRepayment extends BatchAction
{
    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->resource}/early_repayment',
        data: {
            _token:LA.token,
            ids: selectedRows()
        },
        success: function () {
            $.pjax.reload('#pjax-container');
            toastr.success('操作成功');
        }
    });
});

EOT;

    }
}