<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


class SignUp extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = true;

    protected $fillable = ['name', 'mobile', 'address', 'type', 'signUpTitle', 'ip','area'];

}
