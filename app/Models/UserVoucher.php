<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class UserVoucher extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['user_id', 'name', 'desc', 'rules', 'scheme_id', 'start_time', 'end_time', 'is_use'];

    public $timestamps = true;

    public static $uses = [
        0 => '未使用',
        1 => '已使用'
    ];

    const NOT_USE = 0;

    const IS_USE = 1;

    public static $rule_types = [
        1 => '满减',
        2 => '租房类型'
    ];

    /**
     * 对数据进行处理
     * @param UserVoucher $model
     * @return array
     */
    public function indexListFilter($model){
        $scheme =\DB::table('vouchers_scheme')->where('id','=',$model->scheme);
        $returnArr =  [
            'id'                    => (int) $model->id,
            'desc'                  =>  $model->desc,
            'rules'                 =>  $model->rules,
            'scheme_id'             =>  $model->scheme_id,
            'is_use'                =>  $model->is_use,
        ];
        return $returnArr;
    }

}
