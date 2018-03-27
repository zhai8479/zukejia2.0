<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 17:00
 */

namespace App\Admin\Extensions;
use Encore\Admin\Form\Field\Text;


class CustomBenefit extends Text
{
    protected $view = 'admin.custom-benefit';

    protected static $js = [
        '/js/sprintf.js'
    ];

    public function render()
    {
        $this->script = <<<EOT

$('{$this->getElementClassSelector()}').attr('readonly', '');

function benefit() {
    var money = parseFloat($('#money').val().replace(',',''));
    var rental = parseFloat($('#rental_money').val().replace(',',''));
    var collect = parseFloat($('#collect_money').val().replace(',',''));
    money = isNaN(money) ? 0 : money;
    rental = isNaN(rental) ? 0 : rental;
    collect = isNaN(collect) ? 0 : collect;
    var newVal = (rental - collect) * 0.8;
    
    $('#principal').val(sprintf('%.2f', collect));
    $('#income').val(sprintf('%.2f', newVal));
    $('#pni').val(sprintf('%.2f', collect + newVal));
}

benefit();

$('#rental_money').keyup(function(){
   benefit();
});

$('#collect_money').keyup(function(){
   benefit();
});

EOT;


        return parent::render()->with([
            'principal' => '每期本金',
            'income' => '每期收益',
            'pni' => '每期本息',
            'symbol' => '￥',
            'p_id' => 'principal',
            'i_id' => 'income',
            'pni_id' => 'pni'
        ]);
    }
}
