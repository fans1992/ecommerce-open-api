<?php

use GuoJiangClub\Component\Order\Models\Agreement;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAgreementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

        Schema::create($prefix . 'order_agreement', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agreement_no')->unique()->comment('协议编号');
            $table->unsignedInteger('order_id')->comment('订单ID');
            $table->foreign('order_id')->references('id')->on('ibrand_order')->onDelete('cascade');
            $table->string('party_a_name')->comment('甲方');
            $table->string('invoice_type')->nullable()->comment('发票种类');
            $table->string('tax_no')->nullable()->comment('税号');
            $table->string('opening_bank')->nullable()->comment('开户行');
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
        $prefix = config('ibrand.app.database.prefix', 'ibrand_');

        Schema::dropIfExists($prefix . 'order_agreement');
    }
}
