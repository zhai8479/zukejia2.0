<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateVouchersTable
 *
 * 创建表包括:
 * - vouchers 代金卷主表
 * - user_vouchers 用户领取代金卷表
 * - vouchers_rules 代金卷规则表
 * - vouchers_schemes 代金卷方案表
 */
class CreateVouchersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('代金卷名称');
            $table->string('desc')->comment('代金卷描述')->nullable();
            $table->string('rules')->comment('规则id字符串')->nullable();
            $table->string('scheme_id')->comment('方案id');
            $table->timestamp('start_time')->comment('代金卷开始时间')->nullable();
            $table->timestamp('end_time')->comment('代金卷结束时间')->nullable();
            $table->unsignedInteger('effective_day')->nullable()->comment('有效天数,和有效期只选其一');
            $table->unsignedInteger('status')->length(1)->default(1)->comment('是否启用 0: 否, 1: 是');
            $table->timestamps();
        });
        DB::table('vouchers')->insert([
            ['id' => 1, 'name' => '住宿基金卷', 'rules' => '1', 'scheme_id' => 1, 'effective_day' => 30, 'status' => 1, 'created_at' => date_create()],
            ['id' => 2, 'name' => '住宿基金卷', 'rules' => '2', 'scheme_id' => 2, 'effective_day' => 30, 'status' => 1, 'created_at' => date_create()],
            ['id' => 3, 'name' => '住宿基金卷', 'rules' => '3', 'scheme_id' => 3, 'effective_day' => 30, 'status' => 1, 'created_at' => date_create()],
        ]);
        Schema::create('user_vouchers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('所属user_id');
            $table->string('name', 50)->comment('代金卷名称');
            $table->string('desc')->comment('代金卷描述')->nullable();
            $table->string('rules')->comment('规则id字符串')->nullable();
            $table->string('scheme_id')->comment('方案id');
            $table->timestamp('start_time')->comment('代金卷开始时间');
            $table->timestamp('end_time')->comment('代金卷结束时间');
            $table->unsignedInteger('is_use')->length(1)->default(1)->comment('是否已经使用 0: 否, 1: 是');
            $table->timestamps();
        });
        Schema::create('vouchers_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->length(1)->comment('规则类型 1: 满减 2: 房屋类型');
            $table->float('val')->comment('type=1时为要求满的金额(单位元) type=2时为需求的房屋类型(0: 短租, 1: 长租)');
            $table->timestamps();
        });
        DB::table('vouchers_rules')->insert([
            ['id' => 1, 'type' => 1, 'val' => 50000, 'created_at' => date_create()],
            ['id' => 2, 'type' => 1, 'val' => 40000, 'created_at' => date_create()],
            ['id' => 3, 'type' => 1, 'val' => 30000, 'created_at' => date_create()],
            ['id' => 4, 'type' => 2, 'val' => 0, 'created_at' => date_create()],
            ['id' => 5, 'type' => 2, 'val' => 1, 'created_at' => date_create()],
        ]);
        Schema::create('vouchers_schemes', function (Blueprint $table) {
            $table->increments('id');
            $table->float('reduce')->comment('减多少');
            $table->float('is_open')->comment('代金卷减值,单位元');
            $table->timestamps();
        });
        DB::table('vouchers_schemes')->insert([
            ['id' => 1, 'reduce' => 5000, 'created_at' => date_create()],
            ['id' => 2, 'reduce' => 2000, 'created_at' => date_create()],
            ['id' => 3, 'reduce' => 1500, 'created_at' => date_create()],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vouchers');
        Schema::dropIfExists('user_vouchers');
        Schema::dropIfExists('vouchers_rules');
        Schema::dropIfExists('vouchers_schemes');
    }
}
