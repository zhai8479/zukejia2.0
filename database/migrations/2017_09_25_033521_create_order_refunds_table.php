<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderRefundsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_refunds', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('订单id');
            $table->tinyInteger('refund_type')->comment('退款类型: 1. 押金退款, 2. 房款退款')->default(1);
            $table->unsignedInteger('refund_housing_numbers')->comment('退房天数')->default(0);
            $table->string('refund_no', 100)->unique()->comment('退款编号');
            $table->string('external_refund_id', 100)->unique()->comment('外部退款编号')->nullable();
            $table->tinyInteger('refund_status')->comment('退款状态:1. 退款中 2. 退款成功 3. 退款失败')->default(1);
            $table->dateTime('refund_over_at')->comment('退款完成时间');
            $table->ipAddress('ip')->comment('发起者所在ip');
            $table->float('money')->comment('退款金额，单位为元');
            $table->unsignedInteger('user_id')->comment('用户id');
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
		Schema::drop('order_refunds');
	}

}
