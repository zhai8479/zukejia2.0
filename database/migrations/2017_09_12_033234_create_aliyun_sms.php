<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliyunSms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aliyun_sms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('to', 20)->comment('接收短信手机号码');
            $table->string('temp_id', 20)->comment('模板id');
            $table->string('data', 255)->comment('传输数据');
            $table->string('ip', 20)->comment('请求数据的ip地址');
            $table->text('result_info', 255)->comment('返回消息');
            $table->string('message_id', 50)->comment('消息id');
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
        Schema::dropIfExists('aliyun_sms');
    }
}
