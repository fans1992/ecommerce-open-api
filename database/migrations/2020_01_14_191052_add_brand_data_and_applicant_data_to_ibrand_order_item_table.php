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
            $table->string('brand_data')->after('is_send')->nullable()->comment('商标信息');
            $table->string('applicant_data')->after('brand_data')->nullable()->comment('申请信息');
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
        });
    }
}
