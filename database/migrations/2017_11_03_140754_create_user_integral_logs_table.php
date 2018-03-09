<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIntegralLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_integral_logs', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->unsignedInteger('num')->comment('用户积分数量');
            $table->tinyInteger('type')->comment('操作类型 1: 注册送积分 2: 邀请好友注册送积分 3: 完成订单送积分 4: 抵扣订单支付积分 5: 管理员调整积分');
            $table->tinyInteger('in_out')->comment('收支类型 0: 支出, 1: 收入');
            $table->unsignedInteger('admin_id')->nullable()->comment('管理员id');
            $table->string('admin_note')->nullable()->comment('管理员备注');
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
		Schema::drop('user_integral_logs');
	}

}
