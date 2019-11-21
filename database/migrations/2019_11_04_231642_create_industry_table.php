<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndustryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('行业名');                           //行业的名字
            $table->boolean('status')->default(true);                          //状态：1 有效 ，0 失效
            $table->unsignedInteger('sort')->default(0);                       //排序
            $table->text('description')->nullable();                          //行业描述
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
        Schema::dropIfExists('industry');
    }
}
