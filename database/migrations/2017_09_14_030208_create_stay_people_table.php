<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStayPeopleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stay_people', function (Blueprint $table) {
            $table->increments('id');
            $table->string('real_name', 40)->comment('入住人姓名');
            $table->string('id_card', 20)->comment('入住人身份证号');
            $table->string('mobile', 20)->nullable()->comment('手机号');
            $table->boolean('is_check_id_card')->default(false)->comment('身份证号是是否已经验证');
            $table->unsignedInteger('user_id')->comment('所属用户id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stay_people');
    }
}
