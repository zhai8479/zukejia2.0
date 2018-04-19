<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class logs extends Model
{


    protected $table = 'logs';


    protected $fillable = ['mobile', 'code', 'status'];



}
