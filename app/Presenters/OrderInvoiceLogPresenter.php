<?php

namespace App\Presenters;

use App\Transformers\OrderInvoiceLogTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class OrderInvoiceLogPresenter
 *
 * @package namespace App\Presenters;
 */
class OrderInvoiceLogPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new OrderInvoiceLogTransformer();
    }
}
