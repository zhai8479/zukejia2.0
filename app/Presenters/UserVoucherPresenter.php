<?php

namespace App\Presenters;

use App\Transformers\UserVoucherTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserVoucherPresenter
 *
 * @package namespace App\Presenters;
 */
class UserVoucherPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserVoucherTransformer();
    }
}
