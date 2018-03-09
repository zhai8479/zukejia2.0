<?php

namespace App\Presenters;

use App\Transformers\UserIntegralTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserIntegralPresenter
 *
 * @package namespace App\Presenters;
 */
class UserIntegralPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserIntegralTransformer();
    }
}
