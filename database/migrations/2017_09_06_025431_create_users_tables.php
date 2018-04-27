<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 40)->unique()->nullable()->comment('用户名');
            $table->string('mobile', 20)->unique()->comment('手机号');
            $table->string('password', 100)->comment('密码');
            $table->string('email', 100)->unique()->nullable()->comment('邮箱');
            $table->string('real_name', 40)->nullable()->comment('真实姓名');
            $table->string('id_card', 30)->unique()->nullable()->comment('身份证号');
            $table->integer('sex')->length(1)->default(0)->comment('性别: 0: 未知，1：男， 2：女');
            $table->date('birthday')->nullable()->comment('生日');
//            $table->integer('country')->nullable()->comment('国家代码');
//            $table->integer('province')->nullable()->comment('省代码');
//            $table->integer('city')->nullable()->comment('市代码');
            $table->integer('blood_type')->nullable()->comment('血型');
            $table->integer('education')->nullable()->comment('学历');
            $table->string('profession', 40)->nullable()->comment('职位');
            $table->string('ip', 20)->comment('注册ip');
            $table->integer('from_platform')->nullable()->comment('注册平台来源');
            $table->string('avatar_url', 255)->nullable()->comment('用户头像');
            $table->string('recommend_code', 100)->nullable()->comment('用户唯一邀请码');
            $table->unsignedInteger('from_user_id')->comment('邀请者id')->nullable();
            $table->integer('province_id')->nullable()->comment('省编号');
            $table->integer('city_id')->nullable()->comment('市编号');
            $table->integer('district_id')->nullable()->comment('区编号');
            $table->timestamps();
        });
        DB::update("alter table users AUTO_INCREMENT = 10000");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
