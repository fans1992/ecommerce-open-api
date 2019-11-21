<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsHomeDisplayToGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_goods', function (Blueprint $table) {
            $table->boolean('is_home_display')->default(false)->after('is_new')->comment('首页展示');
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
            $table->dropColumn('is_home_display');
        });
    }
}
