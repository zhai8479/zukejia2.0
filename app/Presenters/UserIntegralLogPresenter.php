<?php

namespace App\Presenters;

use App\Transformers\UserIntegralLogTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserIntegralLogPresenter
 *
 * @package namespace App\Presenters;
 */
class UserIntegralLogPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserIntegralLogTransformer();
    }
}
