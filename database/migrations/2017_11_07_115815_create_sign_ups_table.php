<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSignUpsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('sign_ups', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20)->comment('姓名');
            $table->string('mobile', 20)->comment('电话');
            $table->integer('area')->comment('面积');
            $table->string('address', 255)->nulable()->comment('地址');
            $table->string('signUpTitle', 100)->comment('报名标题');
            $table->string('type', 10)->comment('报名类型');
            $table->ipAddress('ip')->comment('ip地址');
            $table->string('community')->comment('小区/楼盘');
            $table->integer('status')->default(0)->comment('标记是否查看 0:未看,1:已查看');
            $table->timestamps();
		});
        Schema::create('appointments', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name',20)->comment('姓名');
            $table->string('mobile', 20)->comment('电话');
            $table->integer('sex')->default(0)->comment('性别 0:男,1:女');
            $table->date('appointments_time')->comment('看房时间');
            $table->string('message')->comment('留言');
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
		Schema::drop('sign_ups');
	}

}
