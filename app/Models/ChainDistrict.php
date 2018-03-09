<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ChainDistrict
 *
 * @property int $id
 * @property string $name
 * @property int $parent_id
 * @property string $initial
 * @property string $initials
 * @property string $pinyin
 * @property string $extra
 * @property string $suffix
 * @property string $code
 * @property string $area_code
 * @property int $order
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereAreaCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereExtra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereInitial($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereInitials($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict wherePinyin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ChainDistrict whereSuffix($value)
 * @mixin \Eloquent
 */
class ChainDistrict extends Model
{
    protected $table = 'chain_district';

    protected $fillable = [];
}
