<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/16
 * Time: 10:52
 */

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\Text;

class FinancialTerm extends Text
{
    protected $view = 'admin.financial-term';

    protected static $js = [
        '/vendor/laravel-admin/AdminLTE/plugins/input-mask/jquery.inputmask.bundle.min.js',
        '/js/sprintf.js'
    ];

    public function render()
    {
        $options = json_encode($this->options);

        $this->script = <<<EOT

$('{$this->getElementClassSelector()}').inputmask($options);

function input_money() {
    var money = parseFloat($('#money').val().replace(',',''));
    var rental = parseFloat($('#rental_money').val().replace(',',''));
    var collect = parseFloat($('#collect_money').val().replace(',',''));
    var terms = Math.floor(money/collect);
    var days = Math.floor(money/collect * 30 % 30);
    var newVal = (rental - collect) * 0.8;
    
    terms = collect ? terms : 0;
    days = collect ? days : 0;
    $('#terms').val(terms);
    $('#days').val(days);
    
    
    if (rental > money || collect > money) {
        var zValue = sprintf('%.2f', 0);
        $('#collect').val(zValue);
        $('#rental').val(zValue);
        $('#rental_money').val(0);
        $('#collect_money').val(0);
        $('#terms').val(0);
        $('#days').val(0);
        $('#principal').val(zValue);
        $('#income').val(zValue);
        $('#pni').val(zValue);
        $('#last_principal').val(zValue);
        $('#last_income').val(zValue);
        $('#last_pni').val(zValue);
    }
}

function getTerms() {
    var money = parseFloat($('#money').val().replace(',',''));
    var collect = parseFloat($('#collect_money').val().replace(',',''));
    var terms = Math.floor(money/collect);
    var days = Math.floor(money/collect * 30 % 30);
    terms = collect ? terms : 0;
    days = collect ? days : 0;
    $('#terms').val(terms);
    $('#days').val(days);
}

input_money();

$('#money').keyup(function(){
    input_money();
    benefit();
    last_benefit();
});

$('#collect_money').keyup(function(){
    getTerms();
});

EOT;

        $this->defaultAttribute('value', json_encode($this->value));
        return parent::render()->with([
            'term' => '期',
            'day' => '天',
            'style' => 'width: 40px',
            't_id' => 'terms',
            'd_id' => 'days'
        ]);
    }
}
