<?php

namespace App\Validators;

use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class AppointmentValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'name' => 'required|string|max:20',
            'apartment_id' =>'required|int|max:50',
            'mobile' => 'required|string|regex:/^1[34578][0-9]{9}$/',
            'sex' => 'int|max:255',
            'appointments_time' => 'required|date',
            'message' => 'string|max:50',
        ],
        ValidatorInterface::RULE_UPDATE => [],
   ];
}
