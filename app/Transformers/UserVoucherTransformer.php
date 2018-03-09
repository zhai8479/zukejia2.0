<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserVoucher;

/**
 * Class UserVoucherTransformer
 * @package namespace App\Transformers;
 */
class UserVoucherTransformer extends TransformerAbstract
{

    /**
     * Transform the UserVoucher entity
     * @param UserVoucher $model
     *
     * @return array
     */
    public function transform(UserVoucher $model)
    {
        $rules = $model->rules;
        $scheme_id = $model->scheme_id;
        $rule_arr = explode(',', $rules);
        $rule_val_arr = [];
        foreach ($rule_arr as $key => $rule_id) {
            $rule = \DB::table('vouchers_rules')->find($rule_id);
            $rule_val_arr[$key] = [
                'val' => $rule->val,
                'type_str' => UserVoucher::$rule_types[$rule->type],
                'type' => $rule->type
            ];
        }
        $scheme = \DB::table('vouchers_schemes')->find($scheme_id);
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'user_id' => $model->user_id,
            'desc' => $model->desc,
            'rules' => $model->rules,
            'rules_detail' => $rule_val_arr,
            'scheme_id' => $model->scheme_id,
            'reduce' => $scheme->reduce,
            'start_time' => $model->start_time,
            'end_time' => $model->end_time,
            'is_use' => $model->is_use,
            'created_at' => $model->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $model->updated_at->format('Y-m-d H:i:s')
        ];
    }
}
