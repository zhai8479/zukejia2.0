<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 10:52
 */

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\Text;

class Duration extends Text
{
    protected $view = 'admin.duration';

    protected static $js = [
        '/js/date_add.js',
        '/js/Moment.js'
    ];

    public function render()
    {
        $this->script = <<<EOT

Date.prototype.format = function(fmt) { 
     var o = { 
        "M+" : this.getMonth()+1,                 //月份 
        "d+" : this.getDate(),                    //日 
        "h+" : this.getHours(),                   //小时 
        "m+" : this.getMinutes(),                 //分 
        "s+" : this.getSeconds(),                 //秒 
        "q+" : Math.floor((this.getMonth()+3)/3), //季度 
        "S"  : this.getMilliseconds()             //毫秒 
    }; 
    if(/(y+)/.test(fmt)) {
            fmt=fmt.replace(RegExp.$1, (this.getFullYear()+"").substr(4 - RegExp.$1.length)); 
    }
     for(var k in o) {
        if(new RegExp("("+ k +")").test(fmt)){
             fmt = fmt.replace(RegExp.$1, (RegExp.$1.length==1) ? (o[k]) : (("00"+ o[k]).substr((""+ o[k]).length)));
         }
     }
    return fmt; 
}

if (!$('#start_at').val()) {
    var default_date = new Date();
    default_date = default_date.format("yyyy-MM-dd hh:mm:ss")
    $('#start_at').val(default_date);
}

if ($('input[name="end_at"]').val()) {
    var start_time = moment($('#start_at').val());
    var end_time = moment($('input[name="end_at"]').val());
    var year = end_time.diff(start_time, 'years');
    var month = end_time.diff(start_time, 'months');
    var temp = start_time.add(month, 'months');
    var day = end_time.diff(temp, 'days');
    $('#year_term').val(year);
    $('#month_term').val(month % 12);
    $('#day_term').val(day);
}

function EndAt() {
    var start_time = new Date($('#start_at').val());
    var year = $('#year_term').val();
    var month = $('#month_term').val();
    var day = $('#day_term').val();
    
    start_time = DateAdd("y", year, start_time);
    start_time = DateAdd("M", month, start_time);
    start_time = DateAdd("d", day, start_time);
    console.log(start_time);
    $('input[name="end_at"]').val(start_time.format("yyyy-MM-dd hh:mm:ss"));
}

$('#year_term').change(function(){
    EndAt();
});

$('#month_term').change(function(){
    EndAt();
});

$('#day_term').change(function(){
    EndAt();
});

EOT;

        return parent::render()->with([
            'year_value' => '年',
            'month_value' => '月',
            'day_value' => '日',
            'style' => 'width: 40px',
            'year_id' => 'year_term',
            'month_id' => 'month_term',
            'day_id' => 'day_term',
        ]);
    }
}
