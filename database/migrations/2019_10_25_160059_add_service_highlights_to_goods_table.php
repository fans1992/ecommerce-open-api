<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddServiceHighlightsToGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_goods', function (Blueprint $table) {
            $table->string('service_highlights')->nullable()->after('tags')->comment('服务亮点');
            $table->decimal('official_price', 15, 2)->nullable()->after('cost_price')->comment('官费');
            $table->decimal('service_price', 15, 2)->nullable()->after('official_price')->comment('服务费');
        });

        Schema::table('ibrand_goods_product', function (Blueprint $table) {
            $table->decimal('official_price', 15, 2)->nullable()->after('sell_price')->comment('官费');
            $table->decimal('service_price', 15, 2)->nullable()->after('official_price')->comment('服务费');
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
            $table->dropColumn('service_highlights');
            $table->dropColumn('official_price');
            $table->dropColumn('service_price');
        });

        Schema::table('ibrand_goods_product', function (Blueprint $table) {
            $table->dropColumn('official_price');
            $table->dropColumn('service_price');
        });
    }
}
