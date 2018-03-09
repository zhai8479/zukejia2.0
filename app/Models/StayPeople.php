<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StayPeople extends Model
{
    use SoftDeletes;

    protected $table = 'stay_people';

    protected $guarded = [];

    public $timestamps = true;

    protected $dates = ['deleted_at'];
}
