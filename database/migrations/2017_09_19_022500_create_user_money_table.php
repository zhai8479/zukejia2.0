<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserMoneyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_money', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique()->comment('账户的用户id');
            $table->bigInteger('money')->unsigned()->default(0)->comment('用户资金');
            $table->integer('freeze')->unigned()->default(0)->comment('用户冻结资金');
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
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
        Schema::dropIfExists('user_money');
    }
}
