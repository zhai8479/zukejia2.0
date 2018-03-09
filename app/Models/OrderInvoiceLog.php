<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

class OrderInvoiceLog extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = [];

    public $timestamps = true;

}
