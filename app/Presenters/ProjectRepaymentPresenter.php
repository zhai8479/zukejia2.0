<?php

namespace App\Presenters;

use App\Models\ProjectRepayment;
use App\Transformers\ProjectRepaymentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProjectInvestmentPresenter
 *
 * @package namespace App\Presenters;
 */
class ProjectRepaymentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return ProjectRepaymentTransformer
     */
    public function getTransformer()
    {
        return new ProjectRepaymentTransformer();
    }
}
