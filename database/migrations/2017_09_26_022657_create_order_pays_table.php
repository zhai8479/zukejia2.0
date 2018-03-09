<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderPaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_pays', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('订单id');
            $table->string('order_pay_no', 100)->comment('支付单号')->unique();
            $table->ipAddress('ip')->comment('执行操作ip地址');
            $table->tinyInteger('pay_channel')->comment('支付渠道: 1. 余额，2. 支付宝，3. 微信，4. 银行卡');
            $table->string('pay_account', 100)->comment('支付账号');
            $table->string('external_no', 100)->comment('外部支付单号')->nullable();
            $table->dateTime('pay_start_at')->comment('支付发起时间')->nullabe();
            $table->dateTime('pay_over_at')->comment('支付结束时间')->nullable();
            $table->tinyInteger('pay_status')->comment('支付状态: 1.待支付,2.支付中,3.支付完成')->default(1);
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
        Schema::dropIfExists('order_pays');
    }
}
