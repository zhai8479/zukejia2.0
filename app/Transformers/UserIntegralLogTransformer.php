<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserIntegralLog;

/**
 * Class UserIntegralLogTransformer
 * @package namespace App\Transformers;
 */
class UserIntegralLogTransformer extends TransformerAbstract
{

    /**
     * Transform the UserIntegralLog entity
     * @param App\Models\UserIntegralLog $model
     *
     * @return array
     */
    public function transform(UserIntegralLog $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
