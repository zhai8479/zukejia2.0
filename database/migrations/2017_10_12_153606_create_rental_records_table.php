<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * 房租记录表
 * Class CreateRentalRecordTable
 */
class CreateRentalRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rental_records', function (Blueprint $table) {
            $table->increments('id');
            $table->date('start_date')->comment('起租时间');
            $table->date('end_date')->comment('结束时间');
            $table->unsignedInteger('apartment_id')->comment('房屋id');
            $table->unsignedInteger('order_id')->comment('订单id');
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
        Schema::dropIfExists('rental_records');
    }
}
