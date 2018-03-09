<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Articles
 *
 * @property int $id
 * @property int $navigation_type_id 栏目id
 * @property int $user_id 发布者
 * @property string $title 标题
 * @property string $content 内容
 * @property string|null $author 作者
 * @property string|null $img_url 文章图片
 * @property string|null $excerpt 文章摘要
 * @property int $hits 点击量
 * @property int $display 文章显示  0显示1不显示
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereAuthor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereDisplay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereExcerpt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereHits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereImgUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereNavigationTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Articles whereUserId($value)
 * @mixin \Eloquent
 */
class Articles extends Model
{
    //
    protected $table = 'articles';
    protected $guarded = ['excerpt'];
    // 是否显示
    const EXCERPT_YES = 0;
    const EXCERPT_NO = 1;

    public static $excerpts = [
        0 => '显示',
        1 => '不显示',
    ];
    public function getImgUrlAttribute($url)
    {
        return url(env("APP_URL").'/uploads/'.$url);
    }
}
