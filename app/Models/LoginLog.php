<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    //
    protected $connection = 'connection-log_mysql';
    public $timestamps = true;
}
