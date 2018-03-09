<?php

namespace App\Presenters;

use App\Transformers\UserMoneyLogTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserMoneyLogPresenter
 *
 * @package namespace App\Presenters;
 */
class UserMoneyLogPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserMoneyLogTransformer();
    }
}
