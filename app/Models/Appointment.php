<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;


class Appointment extends Model implements Transformable
{
    use TransformableTrait;

    public $timestamps = true;

    protected $fillable = ['name', 'mobile', 'sex', 'apartment_id','user_id', 'appointments_time', 'message'];

}
