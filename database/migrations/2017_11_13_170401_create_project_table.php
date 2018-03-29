<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique()
                ->comment('项目名称');
            $table->unsignedInteger('type_id')
                ->comment('类型id');
            $table->float('money',15,2)
                ->comment('项目价格');
            $table->tinyInteger('status')
                ->comment('项目状态 1. 进行中 2. 还款中 3. 已完结');
            $table->unsignedInteger('issue_total_num')
                ->comment('项目总期数');
            $table->unsignedInteger('issue_day_num')->default(0)
                ->comment('项目除期外的天数');
            $table->string('issue_explain')
                ->comment('期说明');
            $table->float('rental_money')
                ->comment('租房价格');
            $table->float('collect_money')
                ->comment('收房价格');
            $table->integer('weight')->default(0)
                ->comment('权重');
            $table->string('characteristic')
                ->comment('项目特点');
            $table->string('house_address')
                ->comment('房屋地址');
            $table->string('house_status')
                ->comment('房屋状况 1. 优秀 2. 良好 3. 差');
            $table->integer('house_id')->nullable()
                ->comment('房屋id');
            $table->float('house_area')
                ->comment('房屋面积');
            $table->string('house_competitive_power')
                ->comment('房屋竞争力');
            $table->integer('house_management_status')
                ->comment('经营状况 1. 筹备中 2. 装修中 3. 运营中 4. 暂停运营 5. 下架');
            $table->string('house_property_certificate')
                ->comment('房产证号');
            $table->string('house_id_card')
                ->comment('房主身份证号');
            $table->string('house_residence')
                ->comment('房主户口本身份证号');
            $table->string('house_contract_img_ids')->nullable()
                ->comment('房主合同等资料文件图片');
            $table->string('risk_assessment')->nullable()
                ->comment('风险评估');
            $table->string('safeguard_measures')->nullable()
                ->comment('保障措施');
            $table->string('guarantor')->nullable()
                ->commnet('担保方');
            $table->timestamp('start_at')
                ->comment('开始时间');
            $table->timestamp('end_at')
                ->comment('结束时间');
            $table->tinyInteger('is_show')->default(1)
                ->comment('控制是否显示在前端 1. 显示, 2隐藏');
            $table->unsignedInteger('contract_file_id')->nullable()
                ->comment('合同文件id');
            $table->string('contract_file_name')->nullable()
                ->comment('合同文件名称');
            $table->unsignedInteger('admin_id')->nullable()
                ->comment('创建者id');
            $table->timestamps();
        });
        // 项目类型
        Schema::create('project_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100)->unique()
                ->comment('类型名称');
            $table->float('max_money',15,2)
                ->comment('最大金额');
            $table->float('min_money',15,2)
                ->comment('最小金额');
            $table->tinyInteger('repayment_type')
                ->comment('还款方式 1. 等额本息 2. 先息后本');
            $table->tinyInteger('guarantee_type')
                ->comment('担保方式 1. 银行担保 2. 公司担保 3. 其他担保');
            $table->integer('interest_day')
                ->comment('计息延后天数');
            $table->timestamps();
        });
        // 项目投资表
        Schema::create('project_investments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_id')
                ->comment('项目id');
            $table->string('no_num', 100)->unique()
                ->comment('项目流水号');
            $table->unsignedInteger('user_id')
                ->comment('用户id');
            $table->tinyInteger('status')
                ->comment('投资状态    1.待支付 2. 还款中 3. 已完结 4. 未支付取消');
            $table->unsignedInteger('now_issue_num')->default(0)
                ->comment('当前所在期数');
            $table->timestamp('pay_at')->nullable()
                ->comment('支付时间');
            $table->timestamp('end_at')->nullable()
                ->comment('完结时间');
            $table->timestamp('repayment_start_at')->nullable()
                ->comment('还款开始时间');
            $table->timestamp('repayment_end_at')->nullable()
                ->comment('还款结束时间');
            $table->timestamp('cancel_at')->nullable()
                ->comment('取消时间');
            $table->timestamp('deleted_at')->nullable()
                ->comment('删除时间');
            $table->timestamps();
        });
        // 项目还款表
        Schema::create('project_repayments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('investment_id')
                ->commnet('投资id');
            $table->unsignedInteger('user_id')
                ->comment('用户id');
            $table->tinyInteger('issue_num')
                ->comment('期数');
            $table->float('money')
                ->comment('金额');
            $table->float('principal',15,2)
                ->comment('本金');
            $table->float('interest',15,2)
                ->comment('利息');
            $table->tinyInteger('is_repayment')->default(1)
                ->comment('是否还款 1. 未还款 2. 已还款');
            $table->timestamp('estimate_time')
                ->comment('预计还款时间');
            $table->timestamp('real_time')->nullable()
                ->comment('实际还款时间');
            $table->timestamp('deleted_at')->nullable()
                ->comment('删除时间');
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
        Schema::dropIfExists('projects');
        Schema::dropIfExists('project_types');
        Schema::dropIfExists('project_investments');
        Schema::dropIfExists('project_repayments');
    }
}
