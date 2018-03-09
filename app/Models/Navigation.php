<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;

/**
 * App\Models\Navigation
 *
 * @property int $id
 * @property int $parent_id 所属id
 * @property int $order 权重
 * @property string $title 标题
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Navigation[] $children
 * @property-read \App\Models\Navigation $parent
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Navigation whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Navigation extends Model
{
    //
    use ModelTree, AdminBuilder;
    protected $table = 'navigation_type';

    protected $guarded = [];
}
