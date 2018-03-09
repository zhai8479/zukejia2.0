<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class SignUpValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name' => 'required|string|max:20',
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/',
            'address' => 'required|string|max:255',
            'signUpTitle' => 'required|string|max:100',
            'type' => 'required|string|in:app,pc,wap,web',
            'ip' => 'required|ip'
        ],
        ValidatorInterface::RULE_UPDATE => [],
   ];
}
