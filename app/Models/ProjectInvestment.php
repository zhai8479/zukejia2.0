<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Models\ProjectInvestment
 *
 * @property int $id
 * @property int $project_id 项目id
 * @property string $no_num 项目流水号
 * @property int $user_id 用户id
 * @property int $status 投资状态    1.待支付 2. 还款中 3. 已完结 4. 未支付取消
 * @property int $now_issue_num 当前所在期数
 * @property string|null $pay_at 支付时间
 * @property string|null $end_at 完结时间
 * @property string|null $repayment_start_at 还款开始时间
 * @property string|null $repayment_end_at 还款结束时间
 * @property string|null $cancel_at 取消时间
 * @property string|null $deleted_at 删除时间
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereCancelAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereNoNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereNowIssueNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment wherePayAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereRepaymentEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereRepaymentStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ProjectInvestment whereUserId($value)
 * @mixin \Eloquent
 */
class ProjectInvestment extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = true;

    protected $fillable = ['project_id', 'no_num', 'user_id', 'status'];

    const STATUS_WAIT_PAY = 1;  // 等待支付
    const STATUS_REPAYMENT = 2; // 还款中
    const STATUS_OVER = 3;      // 完结
    const STATUS_CANCEL = 4;    // 取消
    const STATUS_CANCEL_NO_PAY = 5; // 超时取消

    public static $status_list = [
        1 => '等待支付',
        2 => '还款中',
        3 => '已完结',
        4 => '取消',
        5 => '超时取消'
    ];

}
