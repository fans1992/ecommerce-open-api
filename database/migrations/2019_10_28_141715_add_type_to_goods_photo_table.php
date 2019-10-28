<?php

use GuoJiangClub\EC\Open\Backend\Store\Model\GoodsPhoto;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTypeToGoodsPhotoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_goods_photo', function (Blueprint $table) {
            $table->string('type')->default(GoodsPhoto::PHOTO_TYPE_PRODUCT_DETAIL)->after('flag')->comment('图片展示类型');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_goods_photo', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
