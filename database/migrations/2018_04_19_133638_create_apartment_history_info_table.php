<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateApartmentHistoryInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apartment_history_info', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('apartment_id')->comment('房源编号');
            $table->integer('order_id')->comment('订单编号');
            $table->smallInteger('type')->comment('类别');
            $table->integer('create_user_id')->comment('创建者编号');
            $table->smallInteger('apartment_status_id')->comment('状态编号');
            $table->string('status_info')->comment('状态信息说明');
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
        Schema::dropIfExists('apartment_history_info');
    }
}
