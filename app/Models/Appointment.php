<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;


class Appointment extends Model implements Transformable
{
    use TransformableTrait;
    use SoftDeletes;
    public $timestamps = true;

    protected $fillable = ['name', 'mobile', 'sex', 'apartment_id','user_id', 'appointments_time', 'message'];
    protected $dates = ['deleted_at'];

}
