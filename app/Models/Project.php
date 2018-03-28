<?php

namespace App\Models;

use Encore\Admin\Facades\Admin;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * App\Models\Project
 *
 * @property int $id
 * @property string $name 项目名称
 * @property int $type_id 类型id
 * @property int $money 项目价格
 * @property int $status 项目状态 1. 进行中 2. 还款中 3. 已完结
 * @property int $issue_total_num 项目总期数
 * @property int $issue_day_num 项目除期外的天数
 * @property string $issue_explain 期说明
 * @property int $rental_money 租房价格
 * @property int $collect_money 收房价格
 * @property int $weight 权重
 * @property string $characteristic 项目特点
 * @property string $house_address 房屋地址
 * @property string $house_status 房屋状况 1. 优秀 2. 良好 3. 差
 * @property int|null $house_id 房屋id
 * @property float $house_area 房屋面积
 * @property string $house_competitive_power 房屋竞争力
 * @property int $house_management_status 经营状况 1. 筹备中 2. 装修中 3. 运营中 4. 暂停运营 5. 下架
 * @property string $house_property_certificate 房产证号
 * @property string $house_id_card 房主身份证号
 * @property string $house_residence 房主户口本编号
 * @property string|null $house_contract_img_ids 房主合同等资料文件图片
 * @property string|null $risk_assessment 风险评估
 * @property string|null $safeguard_measures 保障措施
 * @property string|null $guarantor
 * @property string $start_at 开始时间
 * @property string $end_at 结束时间
 * @property int $is_show 控制是否显示在前端 1. 显示, 2隐藏
 * @property int|null $contract_file_id 合同文件id
 * @property string|null $contract_file_name 合同文件名称
 * @property int|null $admin_id 创建者id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereAdminId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCharacteristic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCollectMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereContractFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereContractFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereEndAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereGuarantor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseCompetitivePower($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseContractImgIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseIdCard($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseManagementStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHousePropertyCertificate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseResidence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereHouseStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereIsShow($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereIssueDayNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereIssueExplain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereIssueTotalNum($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereRentalMoney($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereRiskAssessment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereSafeguardMeasures($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereStartAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Project whereWeight($value)
 * @mixin \Eloquent
 */
class Project extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = true;

    protected $fillable = [];

    const IS_SHOW = 1;
    const NOT_SHOW = 2;

    const STATUS_WAIT_START = 0;    //待开始
    const STATUS_PROCESS = 1;   // 进行中
    const STATUS_REPAYMENT = 2; // 还款中
    const STATUS_OVER = 3;      // 已完结
    const STATUS_OVERTIME = 4;  // 项目超时

    public static $status_list = [
        0 => '待开始',
        1 => '进行中',
        2 => '还款中',
        3 => '已完结',
        4 => '已超时',
    ];

    public static $house_status_list = [
        1 => '优秀',
        2 => '良好',
        3 => '差'
    ];

    public static $house_management_status_list = [
        1 => '筹备中',
        2 => '装修中',
        3 => '运营中',
        4 => '暂停运营',
        5 => '下架',
    ];

    /**
     * 判断是否在进行中
     * @return boolean
     */
    public function is_process()
    {
        if ($this->status != self::STATUS_PROCESS) return false;
        $now_date_str = date('Y-m-d H:i:s');
        if ($now_date_str >= $this->start_at && $now_date_str <= $this->end_at) return true;
        return false;
    }

    /**
     * @return mixed|string
     */
    public function status_str()
    {
        return self::$status_list[$this->status];
    }

    /**
     * 判断当前所在时间
     * @param $start_time
     * @param $end_time
     * @return int
     */
    public static function check_time_is_start($start_time, $end_time)
    {
        $now_date_str = date('Y-m-d H:i:s');
        if ($now_date_str >= $start_time && $now_date_str <= $end_time) return 0;   // 进行中
        if ($now_date_str < $start_time) return -1;                                      // 未开始
        if ($now_date_str > $end_time) return 1;                                        // 已结束
    }

    /**
     * 判断是否为最后一期
     */
    public function is_end_issue($num)
    {
        $total_num = $this->issue_total_num;
        if ($this->issue_day_num > 0) $total_num ++;
        if ($num >= $total_num) return true;
        return false;
    }

    public function house_status_str()
    {
        return self::$house_status_list[$this->house_status];
    }


    public function house_management_status_str()
    {
        return self::$house_management_status_list[$this->house_management_status];
    }

    public function setIssueTotalNumAttribute($value)
    {
        $this->attributes['issue_total_num'] = $value[0];
        $this->attributes['issue_day_num'] = $value[1];
    }

    public function setHouseContractImgIdsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['house_contract_img_ids'] = json_encode($value);
        }
    }

    public function getHouseContractImgIdsAttribute($value)
    {
        return json_decode($value, true);
    }

    public function setMoneyAttribute($value)
    {
        $this->attributes['money'] = $value ;
    }

    public function getMoneyAttribute($value)
    {
        return $value ;
    }

    public function setRentalMoneyAttribute($value)
    {
        $this->attributes['rental_money'] = $value ;
    }

    public function getRentalMoneyAttribute($value)
    {
        return $value;
    }

    public function setCollectMoneyAttribute($value)
    {
        $this->attributes['collect_money'] = $value ;
    }

    public function getCollectMoneyAttribute($value)
    {
        return $value ;
    }
}
