<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class UserMoneyLogValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'user_id' => 'required|integer|min:1|exists:users,id',
            'type' => 'required|integer|min:1',
            'in_out' => 'required|integer|in:0,1',
            'money' => 'required|numeric|min:0',
            'admin_id' => 'integer|min:1',
            'description' => 'string|max:255',
            'admin_note' => 'string|max:255',
        ],
        ValidatorInterface::RULE_UPDATE => [],
   ];
}
