<?php

namespace App\Validators;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;
use \Prettus\Validator\Contracts\ValidatorInterface;
use \Prettus\Validator\LaravelValidator;

class OrderValidator extends LaravelValidator
{

    protected $rules = [
        ValidatorInterface::RULE_CREATE => [
            'apartment_id' => 'required|integer|exists:apartment,id',
            'coupons_id' => 'integer|exists:user_vouchers,id',
            'activity_id' => '',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d',
            'need_invoice' => 'boolean',
            'check_in_users' => 'required|array',
            'check_in_users.*' => 'required|array',
            'check_in_users.*.id' => 'required|integer',
        ],
        ValidatorInterface::RULE_UPDATE => [],
   ];
}
