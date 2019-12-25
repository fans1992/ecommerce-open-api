<?php

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
        });
    }
}
