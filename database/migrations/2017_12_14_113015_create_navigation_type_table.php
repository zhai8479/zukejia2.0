<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Navigation;
use App\Models\Articles;

class CreateNavigationTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('navigation_type', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->comment('所属id');
            $table->integer('order')->comment('权重');
            $table->string('title')->comment('标题');
            $table->timestamps();
        });

        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('navigation_type_id')->comment('栏目id');
            $table->integer('user_id')->comment('发布者');
            $table->string('title')->comment('标题');
            $table->mediumtext('content')->comment('内容');
            $table->string('author')->comment('作者')->nullable();
            $table->string('img_url')->comment('文章图片')->nullable();
            $table->string('excerpt')->comment('文章摘要')->nullable();
            $table->integer('hits')->comment('点击量')->default(0);
            $table->integer('display')->comment('文章显示  0显示1不显示')->default(0);
            $table->string('keywords')->comment('关键词');
            $table->timestamps();
        });
        $this->information();
    }
    public function information()
    {
        // 创建导航
        Navigation::truncate();
        Navigation::insert([
            [
                'id' => 1,
                'parent_id' => 0,
                'order'     => 1,
                'title'     => '网站向导',
            ],
            [
                'id' => 2,
                'parent_id' => 0,
                'order'     => 2,
                'title'     => '资讯中心',
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'order'     => 101,
                'title'     => '关于我们',
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'order'     => 102,
                'title'     => '网站帮助',
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'order'     => 103,
                'title'     => '网站协议',
            ],
            [
                'id' => 6,
                'parent_id' => 2,
                'order'     => 201,
                'title'     => '平台公告',
            ],
            [
                'id' => 7,
                'parent_id' => 2,
                'order'     => 202,
                'title'     => '平台活动',
            ],
            [
                'id' => 8,
                'parent_id' => 2,
                'order'     => 203,
                'title'     => '媒体报道',
            ],
            [
                'id' => 9,
                'parent_id' => 1,
                'order'     => 105,
                'title'     => '新手帮助',
            ],
            [
                'id' => 10,
                'parent_id' => 1,
                'order'     => 106,
                'title'     => '常见问题',
            ],
            [
                'id' => 11,
                'parent_id' => 1,
                'order'     => 107,
                'title'     => '联系我们',
            ],
        ]);



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('navigation_type');
        Schema::dropIfExists('articles');
    }
}
