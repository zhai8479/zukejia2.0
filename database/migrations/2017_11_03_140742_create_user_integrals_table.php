<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserIntegralsTable extends Migration
{

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_integrals', function(Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('用户id')->unique();
            $table->unsignedInteger('integral')->comment('用户积分数量')->default(0);
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
		Schema::drop('user_integrals');
	}

}
