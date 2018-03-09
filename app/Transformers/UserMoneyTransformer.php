<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserMoney;

/**
 * Class UserMoneyTransformer
 * @package namespace App\Transformers;
 */
class UserMoneyTransformer extends TransformerAbstract
{

    /**
     * Transform the \UserMoney entity
     * @param UserMoney|\UserMoney $model
     * @return array
     */
    public function transform(UserMoney $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */
            'user_id' => (int) $model->user_id,
            'money' => (int) $model->money,
            'freeze' => (int) $model->freeze,
            'created_at' => date_format($model->created_at, 'Y-m-d H:i:s'),
            'updated_at' => date_format($model->updated_at, 'Y-m-d H:i:s'),
        ];
    }
}
