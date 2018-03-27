<?php

namespace App\Transformers;

use App\Models\ProjectRepayment;
use League\Fractal\TransformerAbstract;

/**
 * Class ProjectInvestmentTransformer
 * @package namespace App\Transformers;
 */
class ProjectRepaymentTransformer extends TransformerAbstract
{

    /**
     * Transform the ProjectInvestment entity
     *
     * @param ProjectRepayment $model
     *
     * @return array
     */
    public function transform(ProjectRepayment $model)
    {
        $repayment = $model;
        $repayment->is_repayment_str = ProjectRepayment::$is_repayment_list[$repayment->is_repayment];
        return $repayment->toArray();
    }
}
