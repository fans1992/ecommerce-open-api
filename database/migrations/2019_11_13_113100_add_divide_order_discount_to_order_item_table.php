<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDivideOrderDiscountToOrderItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_order_item', function (Blueprint $table) {
            $table->integer('divide_order_discount')->default(0)->nullable()->after('adjustments_total');
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
            $table->dropColumn('divide_order_discount');
        });
    }
}
