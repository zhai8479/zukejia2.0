<?php

namespace App\Presenters;

use App\Transformers\ApartmentTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class ApartmentPresenter
 *
 * @package namespace App\Presenters;
 */
class ApartmentPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new ApartmentTransformer();
    }
}
