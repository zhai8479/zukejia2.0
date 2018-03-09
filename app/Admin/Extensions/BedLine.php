<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field\Text;
use Illuminate\Support\Facades\Redis;

class BedLine extends Text
{
    protected $view = 'admin.bed-line';

    protected static $js = [
        '/vendor/laravel-admin/number-input/bootstrap-number-input.js',
    ];

    public function render()
    {
//        $this->default((int) $this->default[0]);
        $values = json_decode($this->value, true);
        if (empty($values)) {
            $values = [0,0,0];
        }

        $this->script = <<<EOT

$('{$this->getElementClassSelector()}:not(.initialized)')
    .addClass('initialized')
    .bootstrapNumber({
        upClass: 'success',
        downClass: 'primary',
        center: true
    });
    
    $('{$this->getElementClassSelector()}:eq(0)').val({$values[0]});
    $('{$this->getElementClassSelector()}:eq(1)').val({$values[1]});
    $('{$this->getElementClassSelector()}:eq(2)').val({$values[2]});

EOT;

        parent::prepend('')->defaultAttribute('style', 'width: 100px');
        parent::prepend('')->defaultAttribute('name', $this->id . '[]');

        return parent::render();
    }
}