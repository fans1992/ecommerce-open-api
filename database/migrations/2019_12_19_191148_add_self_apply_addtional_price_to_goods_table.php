<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSelfApplyAddtionalPriceToGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_goods', function (Blueprint $table) {
            $table->decimal('self_apply_additional_price', 15, 2)->nullable()->after('service_price')->comment('自助申请附加费');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_goods', function (Blueprint $table) {
            $table->dropColumn('self_apply_additional_price');
        });
    }
}
