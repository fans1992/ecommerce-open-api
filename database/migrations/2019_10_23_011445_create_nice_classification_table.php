<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNiceClassificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nice_classification', function (Blueprint $table) {
            $table->increments('id');
            $table->string('classification_name')->comment('商标分类名称');
            $table->string('classification_code')->comment('商标分类编号');
            $table->tinyInteger('status')->default(1);                          //状态：1 有效 ，0 失效
            $table->unsignedInteger('sort')->default(0);                       //排序
            $table->text('description')->nullable();                          //分类描述
            $table->string('path')->nullable()->default('/');
            $table->integer('level')->default(1);
            $table->nestedSet();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nice_classification');
    }
}
