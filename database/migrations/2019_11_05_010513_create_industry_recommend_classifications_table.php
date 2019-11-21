<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIndustryRecommendClassificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('industry_recommend_classifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('industry_id');
            $table->unsignedInteger('nice_classification_parent_id');
            $table->unsignedInteger('nice_classification_id');
            $table->string('alias')->nullable();
            $table->integer('sort')->default(99);
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
        Schema::dropIfExists('industry_recommend_classifications');
    }
}
