<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProjectType
 *
 * @property int $id
 * @property string $name 类型名称
 * @property int $max_money 最大金额
 * @property int $min_money 最小金额
 * @property int $repayment_type 还款方式 1. 等额本息 2. 先息后本
 * @property int $guarantee_type 担保方式 1. 银行担保 2. 公司担保 3. 其他担保
 * @property int $interest_day 计息延后天数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereGuaranteeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereInterestDay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereMaxMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereMinMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereRepaymentType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProjectType extends Model
{
    public $timestamps = true;

    protected $table = 'project_types';

    public static $repayment_type_list = [
        1 => '等额本息',
        2 => '先息后本'
    ];

    public static $guarantee_type_list = [
        1 => '银行担保',
        2 => '公司担保',
        3 => '其他担保'
    ];
}
