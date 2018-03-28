<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderInvoiceLogsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_invoice_logs', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('订单编号');
            $table->string('invoice_no', 100)->unique()->comment('发票单号');
            $table->string('title', 100)->comment('发票抬头(个人名或公司名)');
            $table->string('taxpayer_no', 100)->comment('纳税人识别号(公司需要填写)')->nullable();
            $table->float('money')->comment('发票金额');
            $table->string('addressee_phone', 40)->comment('收件人电话');
            $table->string('addressee_name', 40)->comment('收件人姓名');
            $table->string('addressee_address', 255)->comment('收件人地址');
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
		Schema::drop('order_invoice_logs');
	}

}
