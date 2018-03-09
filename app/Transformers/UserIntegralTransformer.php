<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\UserIntegral;

/**
 * Class UserIntegralTransformer
 * @package namespace App\Transformers;
 */
class UserIntegralTransformer extends TransformerAbstract
{

    /**
     * Transform the UserIntegral entity
     * @param App\Models\UserIntegral $model
     *
     * @return array
     */
    public function transform(UserIntegral $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
