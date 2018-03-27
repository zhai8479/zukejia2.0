<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApartmentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('房源的用户id');
            $table->smallInteger('province')->comment('省');
            $table->smallInteger('city')->comment('市');
            $table->smallInteger('district')->comment('区');
            $table->string('address')->comment('地址');
            $table->string('search_address')->comment('详细地址');
            $table->tinyInteger('status')->comment('状态 1-热销 2-预租 3-出租');
            $table->string('title')->comment('标题');
            $table->text('desc')->comment('个性描述');
            $table->text('inner_desc')->comment('内部描述');
            $table->text('traffic_desc')->comment('交通情况');
            $table->text('environment')->comment('周边描述');
            $table->tinyInteger('type')->comment('1-整体出租 2-独立单间 3-合租 4-酒店式公寓');
            $table->tinyInteger('room')->comment('房间数量');
            $table->tinyInteger('hall')->comment('厅数量');
            $table->tinyInteger('bathroom')->comment('卫生间数量');
            $table->tinyInteger('kitchen')->comment('厨房数量');
            $table->tinyInteger('balcony')->comment('阳台数量');
            $table->unsignedTinyInteger('area')->comment('面积');
            $table->tinyInteger('decoration_style')->comment('装修风格');
            $table->tinyInteger('direction')->comment('朝向');
            $table->string('bathroom_utils')->comment('配套设施-卫浴');
            $table->string('electrics')->comment('配套设施-电器');
            $table->string('bed')->comment('配套设施-床');
            $table->string('kitchen_utils')->comment('配套设施-厨房');
            $table->string('requires')->comment('配套设施-要求');
            $table->string('facilities')->comment('配套设施-设备');
            $table->text('images')->comment('图片');
            $table->tinyInteger('rental_type')->comment('0-短租 1-长租 2-特价');
            $table->decimal('rental_price',14,2)->comment('租金');
            $table->decimal('rental_deposit',14,2)->comment('押金');
            $table->string('single_bed')->comment('单人床');
            $table->string('double_bed')->comment('双人床');
            $table->string('tatami')->comment('榻榻米');
            $table->string('round_bed')->comment('圆床');
            $table->string('big_bed')->comment('大床');
            $table->text('keyword')->comment('关键词组');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apartment');
    }
}
