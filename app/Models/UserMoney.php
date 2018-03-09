<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Encore\Admin\Admin;

class UserMoney extends Model implements Transformable
{
    use TransformableTrait;

    protected $table = 'user_money';

    public $timestamps = true;

    protected $fillable = ['user_id'];

    public function user()
    {
        $this->belongsTo(User::class, 'user_id', 'id');
    }
}
