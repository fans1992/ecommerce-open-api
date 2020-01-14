<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPaymentMethodToOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_order', function (Blueprint $table) {
            $table->string('payment_method')->nullable()->after('pay_time')->comment('支付方式');
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
            $table->dropColumn('payment_method');
        });
    }
}
