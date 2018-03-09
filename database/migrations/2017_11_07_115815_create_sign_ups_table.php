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
            $table->integer('status')->default(0)->comment('标记是否查看 0:未看,1:已查看');
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
