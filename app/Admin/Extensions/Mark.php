<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 9:16
 */

namespace App\Admin\Extensions;
use App\Models\SignUp;
use Encore\Admin\Admin;


class Mark
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    protected function script()
    {
        $str = "<a class='fa fa-check-square-o grid-check-row' data-id_read='{$this->id}' >";
        return <<<SCRIPT

$('.grid-check-row[data-id="{$this->id}"]').on('click', function () {

    $.ajax({
        url: '/api/sign_up/mark',
        type: 'POST',
        dataType: 'json',
        data: {
            id: "{$this->id}",
            status: 1
        },
    })
    .done(function(data) {
        var str = "{$str}";
        if (data.code === 0) {
            $('.grid-check-row[data-id="{$this->id}"]').hide();
            $('.grid-check-row[data-id="{$this->id}"]').parent().append(str);
        }
    })
    .fail(function() {
        console.log("error");
    })
    .always(function() {
        console.log("complete");
    });

});

SCRIPT;
    }

    protected function render()
    {
        Admin::script($this->script());
        $item = SignUp::find($this->id);
        if ($item->status == 0) return "<a class='fa fa-square-o grid-check-row' data-id='{$this->id}' >";
        else return "<a class='fa fa-check-square-o grid-check-row' data-id_read='{$this->id}' >";
    }

    public function __toString()
    {
        return $this->render();
    }
}