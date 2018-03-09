<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserMoneyLog;

/**
 * Class UserMoneyLogTransformer
 * @package namespace App\Transformers;
 */
class UserMoneyLogTransformer extends TransformerAbstract
{

    /**
     * Transform the \UserMoneyLog entity
     * @param \UserMoneyLog $model
     *
     * @return array
     */
    public function transform(UserMoneyLog $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
