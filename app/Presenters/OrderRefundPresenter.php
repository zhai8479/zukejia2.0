<?php

namespace App\Presenters;

use App\Transformers\OrderRefundTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class OrderRefundPresenter
 *
 * @package namespace App\Presenters;
 */
class OrderRefundPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new OrderRefundTransformer();
    }
}
