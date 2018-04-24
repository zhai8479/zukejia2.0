<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderCheckInUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_check_in_users', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('order_id')->comment('订单号');
            $table->string('real_name')->comment('真实姓名');
            $table->string('id_card')->comment('身份证号');
            $table->string('mobile')->comment('手机号')->nullable();
            $table->unsignedInteger('user_id')->comment('用户id');
            $table->tinyInteger('stay_people_id')->comment('入住人id');
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
        Schema::drop('order_check_in_users');
    }
}
