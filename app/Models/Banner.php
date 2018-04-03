<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Banner
 * @property int  $id
 * @property string $title 标题
 * @property string/null $banner_url 轮播图地址
 * @property string/null $link 跳转链接
 * @property integer $order 权重
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Banner whereImgUrl($value)
 * @mixin \Eloquent
 */
class Banner extends Model
{
    public function getBannerUrlAttribute($url)
    {
        return url(env("APP_URL").'/uploads/'.$url);
    }
}
