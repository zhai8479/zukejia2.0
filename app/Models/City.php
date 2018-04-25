<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;


/**
 * App\Models\ChainDistrict
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 *
 *
 * @mixin \Eloquent
 */
class City extends Model
{
    use ModelTree, AdminBuilder;
    protected $table = 'city';
    protected $fillable = [];
}
