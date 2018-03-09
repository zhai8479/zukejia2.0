<?php
/**
 * Created by kelven<kelven.chi@perlface.net>.
 * User: kelven
 * Date: 2017/11/8
 * Time: 15:33
 */

namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\BatchAction;

class BatchMark extends BatchAction
{
    protected $status;

    public function __construct($status = 1)
    {
        $this->status = $status;
    }

    public function script()
    {
        return <<<EOT

$('{$this->getElementClass()}').on('click', function() {

    $.ajax({
        method: 'post',
        url: '{$this->resource}/batch_mark',
        data: {
            _token:LA.token,
            ids: selectedRows(),
            status: {$this->status}
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