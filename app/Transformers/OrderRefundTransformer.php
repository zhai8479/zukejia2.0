<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\OrderRefund;

/**
 * Class OrderRefundTransformer
 * @package namespace App\Transformers;
 */
class OrderRefundTransformer extends TransformerAbstract
{

    /**
     * Transform the \OrderRefund entity
     * @param \OrderRefund $model
     *
     * @return array
     */
    public function transform(OrderRefund $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
