<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/14
 * Time: 17:00
 */

namespace App\Admin\Extensions;
use Encore\Admin\Form\Field\Text;


class LastBenefit extends Text
{
    protected $view = 'admin.last-benefit';

    protected static $js = [
        '/js/sprintf.js'
    ];

    public function render()
    {
        $this->script = <<<EOT

$('{$this->getElementClassSelector()}').attr('readonly', '');

function last_benefit() {
    var money = parseFloat($('#money').val().replace(',',''));
    var rental = parseFloat($('#rental_money').val().replace(',',''));
    var collect = parseFloat($('#collect_money').val().replace(',',''));
    money = isNaN(money) ? 0 : money;
    rental = isNaN(rental) ? 0 : rental;
    collect = isNaN(collect) ? 0 : collect; 
    var newVal = (rental - collect) * 0.8;
    var terms = Math.floor(money/collect);
    var days = Math.floor(money/collect * 30 % 30);
    var last_collect = sprintf('%.2f', money - terms * collect); 
    var last_newVal = (rental - collect) * 0.8 / 30 * days;
    last_collect = isNaN(last_collect) ? 0 : last_collect;
    last_newVal = isNaN(last_newVal) ? 0 : last_newVal;
    
    $('#last_principal').val(sprintf('%.2f', last_collect));
    $('#last_income').val(sprintf('%.2f', last_newVal));
    $('#last_pni').val(sprintf('%.2f', parseFloat(last_newVal) + parseFloat(last_collect)));
}

last_benefit();

$('#rental_money').keyup(function(){
    last_benefit();
});

$('#collect_money').keyup(function(){
    last_benefit();
});

EOT;

        return parent::render()->with([
            'last_principal' => '尾期本金',
            'last_income' => '尾期收益',
            'last_pni' => '尾期本息',
            'symbol' => '￥',
            'last_p_id' => 'last_principal',
            'last_i_id' => 'last_income',
            'last_pni_id' => 'last_pni'
        ]);
    }
}
