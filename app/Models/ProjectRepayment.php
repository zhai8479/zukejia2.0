<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ProjectRepayment
 *
 * @property int $id
 * @property int $investment_id
 * @property int $user_id 用户id
 * @property int $issue_num 期数
 * @property int $money 金额
 * @property int $principal 本金
 * @property int $interest 利息
 * @property int $is_repayment 是否还款 1. 未还款 2. 已还款
 * @property string $estimate_time 预计还款时间
 * @property string|null $real_time 实际还款时间
 * @property string|null $deleted_at 删除时间
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereEstimateTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereInterest($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereInvestmentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereIsRepayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereIssueNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment wherePrincipal($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereRealTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectRepayment whereUserId($value)
 * @mixin \Eloquent
 */
class ProjectRepayment extends Model
{
    public $timestamps = true;

    protected $fillable = ['investment_id', 'user_id', 'issue_num', 'money', 'principal', 'interest', 'estimate_time'];

    protected $table = 'project_repayments';

    public static $is_repayment_list = [
        1 => '未还款',
        2 => '已还款'
    ];

    const NOT_REPAYMENT = 1;
    const IS_REPAYMENT = 2;

    public function getMoneyAttribute($value)
    {
        return $value / 100;
    }

    public function setMoneyAttribute($value)
    {
        $this->attributes['money'] = $value * 100;
    }

    public function getPrincipalAttribute($value)
    {
        return $value / 100;
    }

    public function setPrincipalAttribute($value)
    {
        $this->attributes['principal'] = $value * 100;
    }

    public function getInterestAttribute($value)
    {
        return $value / 100;
    }

    public function setInterestAttribute($value)
    {
        $this->attributes['interest'] = $value * 100;
    }
}
