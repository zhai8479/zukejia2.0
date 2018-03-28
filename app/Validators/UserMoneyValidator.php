<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class UserMoneyValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'user_id' => 'required|integer|min:1'
        ],
        ValidatorInterface::RULE_UPDATE => [
            'money' => 'numeric|min:0',
            'freeze' => 'numeric|min:0',
        ],
   ];
}
