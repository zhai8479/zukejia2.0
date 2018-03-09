<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Grid\Tools\AbstractTool;
use Encore\Admin\Grid;
class ChargeButton extends AbstractTool
{
    /**
     * Create a new ChargeButton instance.
     *
     * @param Grid $grid
     */
    public function __construct(Grid $grid)
    {
        $this->grid = $grid;
    }
    /**
     * Render ChargeButton.
     *
     * @return string
     */
    public function render()
    {
        $charge = trans('充值');

        return <<<EOT
<div class="btn-group pull-right" style="margin-right: 10px">
    <a href="{$this->grid->resource()}/charge" class="btn btn-sm btn-success">
        <i class="fa fa-save"></i>&nbsp;&nbsp;{$charge}
    </a>
</div>
EOT;
    }
}