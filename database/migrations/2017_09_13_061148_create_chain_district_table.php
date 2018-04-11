<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 创建中国省市区数据表，并执行 district-full.sql 文件
 *
 * Class CreateChainDistrictTable
 */
class CreateChainDistrictTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chain_district', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->smallInteger('parent_id')->length(5);
            $table->char('initial', 3);
            $table->string('initials', 30);
            $table->string('pinyin', 50);
            $table->string('extra', 50);
            $table->string('suffix', 15);
            $table->char('code', 30);
            $table->string('area_code', 30);
            $table->tinyInteger('order')->length(2);
            $table->tinyInteger('levels')->comment('层级');
        });
        if (file_exists(__DIR__ . '/district-full.sql')) {
            $file = fopen(__DIR__ . '/district-full.sql', 'r');
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
        Schema::dropIfExists('chain_district');
    }
}
