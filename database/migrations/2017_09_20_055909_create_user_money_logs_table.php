<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMoneyLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_money_logs', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->smallInteger('type')->unsigned()->comment('操作类型');
            $table->smallInteger('in_out')->unsigned()->comment('收入和支付类型 0: 收入 1：支出');
            $table->unsignedInteger('money')->comment('操作金额');
            $table->unsignedInteger('admin_id')->nullable()->comment('操作管理员id');
            $table->string('description', 255)->nullable()->comment('操作描述');
            $table->string('admin_note', 255)->nullable()->comment('管理员操作备注');
            $table->timestamps();
            $table->index(['user_id', 'type', 'in_out']);
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'in_out']);
            $table->index('in_out');
            $table->index('user_id');
            $table->index('type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('user_money_logs');
	}

}
