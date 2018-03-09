<?php

namespace App\Presenters;

use App\Transformers\UserMoneyTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class UserMoneyPresenter
 *
 * @package namespace App\Presenters;
 */
class UserMoneyPresenter extends FractalPresenter
{
    /**
     * Transformer
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new UserMoneyTransformer();
    }
}
