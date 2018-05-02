<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Version
 * @property int  $id
 * @property string $type 更新类型
 * @property string $url 下载地址
 * @property string $version 版本号
 * @property string $message 更新说明
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class Version extends Model
{
    //
    protected $table = 'versions';
    public $timestamps = true;
    protected $guarded = [];
    // 更新类型
    public static $type = [
        1 => '非强制更新',
        2 => '强制更新'
    ];
    const TYPE_NOTMUST = 1;
    const TYPE_MUST= 2;

}
