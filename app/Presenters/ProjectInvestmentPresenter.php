<?php

namespace App\Presenters;

use App\Transformers\ProjectInvestmentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ProjectInvestmentPresenter
 *
 * @package namespace App\Presenters;
 */
class ProjectInvestmentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ProjectInvestmentTransformer();
    }
}
