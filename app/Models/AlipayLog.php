<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlipayLog extends Model
{


    protected $table = 'alipay_logs';


    public $timestamps = true;


    protected $fillable = ['log_text11'];
}
