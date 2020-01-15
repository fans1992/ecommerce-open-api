<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBrandDataAndApplicantDataToIbrandOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_order_item', function (Blueprint $table) {
            $table->text('brand_data')->after('is_send')->nullable()->comment('商标信息');
            $table->text('applicant_data')->after('brand_data')->nullable()->comment('申请信息');
            $table->text('company_progress')->after('applicant_data')->nullable()->comment('公司进度');
            $table->text('official_progress')->after('company_progress')->nullable()->comment('官方进度');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_order_item', function (Blueprint $table) {
            $table->dropColumn('brand_data');
            $table->dropColumn('applicant_data');
            $table->dropColumn('company_progress');
            $table->dropColumn('official_progress');
        });
    }
}
