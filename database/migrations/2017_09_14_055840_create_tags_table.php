<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tag', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id')->comment('父ID');
            $table->smallInteger('type')->comment('标签类型:1-装修风格 2-朝向 3-基础设施 4-文章类型');
            $table->string('name')->comment('标签名字');
            $table->smallInteger('value')->comment('标签值');
            $table->string('desc')->comment('描述');
            $table->timestamps();
        });
        if (file_exists(__DIR__ . '/tag.sql')) {
            $file = fopen(__DIR__ . '/tag.sql', 'r');
            while (!feof($file)) {
                $sql = fgets($file);
                if (!empty($sql)) DB::statement($sql);
            }
            fclose($file);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tag');
    }
}
