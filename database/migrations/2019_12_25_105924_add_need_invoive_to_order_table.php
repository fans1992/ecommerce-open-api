<?php

use GuoJiangClub\Component\Order\Models\BrandApplicant;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNeedInvoiveToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_order', function (Blueprint $table) {
            $table->boolean('need_invoice')->default(true)->after('note')->comment('需要开发票');
            $table->string('applicant_status')->nullable()->after('need_invoice')->comment('申请人状态');
            $table->text('applicant_data')->nullable()->after('applicant_status')->comment('申请人信息');
            $table->text('extra')->nullable()->after('applicant_data')->comment('其他额外的数据');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_order', function (Blueprint $table) {
            $table->dropColumn('need_invoice');
            $table->dropColumn('applicant_status');
            $table->dropColumn('applicant_data');
            $table->dropColumn('extra');
        });
    }
}
