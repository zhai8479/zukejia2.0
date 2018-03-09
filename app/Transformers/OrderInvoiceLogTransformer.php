<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;
use App\Models\OrderInvoiceLog;

/**
 * Class OrderInvoiceLogTransformer
 * @package namespace App\Transformers;
 */
class OrderInvoiceLogTransformer extends TransformerAbstract
{

    /**
     * Transform the \OrderInvoiceLog entity
     * @param \OrderInvoiceLog $model
     *
     * @return array
     */
    public function transform(OrderInvoiceLog $model)
    {
        return [
            'id'         => (int) $model->id,

            /* place your other model properties here */

            'created_at' => $model->created_at,
            'updated_at' => $model->updated_at
        ];
    }
}
