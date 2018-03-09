<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
            $table->increments('id');
            $table->string('order_no', 100)->unique()->comment('订单号');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('rental_price')->comment('房租价格');
            $table->unsignedInteger('rental_deposit')->comment('押金');
            $table->unsignedInteger('apartment_id')->comment('房源id');
            $table->unsignedInteger('coupons_id')->comment('优惠券ID')->nullable();
            $table->unsignedInteger('coupons_money')->comment('优惠券抵扣金额,单位：分')->nullable();
            $table->unsignedInteger('activity_id')->comment('活动ID')->nullable();
            $table->unsignedInteger('activity_money')->comment('活动抵扣金额, 单位：分')->nullable();
            $table->unsignedInteger('pay_money')->comment('实际支付金额');
            $table->tinyInteger('status')->default(1)->comment('订单状态：1.已提交 2.已支付, 3.已退房  3.订单完成, 4.已支付取消订单, 5. 未支付取消');

            $table->date('start_date')->comment('租房开始时间');
            $table->date('end_date')->comment('租房结束时间');
            $table->integer('housing_numbers')->comment('入住数量,天数或月数');
            $table->tinyInteger('rent_type')->comment('租房类型: 1. 短租 2. 月租')->default(1);
            $table->boolean('need_invoice')->comment('是否需要发票')->default(false);
            $table->string('check_in_user_ids', 40)->comment('入住人id数组(字符串逗号隔开)');
            $table->ipAddress('ip')->comment('执行操作ip地址');
            $table->tinyInteger('pay_channel')->comment('支付渠道: 1. 余额，2. 支付宝，3. 微信，4. 银行卡')->nullable();
            $table->string('pay_account', 100)->comment('支付账号')->nullable();
            $table->string('external_no', 100)->comment('外部支付单号')->nullable();
            $table->dateTime('pay_start_at')->comment('支付发起时间')->nullabe();
            $table->dateTime('pay_over_at')->comment('支付结束时间')->nullable();
            $table->tinyInteger('pay_status')->comment('支付状态: 1.待支付,2.支付中,3.支付完成')->default(1);
            $table->boolean('is_refunds')->comment('是否发生退款')->default(false);
            $table->integer('refunds_total_money')->comment('退款总金额')->default(0);
            $table->string('order_pay_no', 100)->comment('支付单号')->nullable();
            $table->string('cancel_reason')->nullable()->comment('取消订单理由');
            $table->timestamp('cancel_at')->nullable()->comment('取消订单时间');
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('order_no');
            $table->index('status');
            $table->index('pay_status');
            $table->index('activity_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('orders');
	}

}
