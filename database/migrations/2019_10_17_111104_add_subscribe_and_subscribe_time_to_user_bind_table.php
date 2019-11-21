<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSubscribeAndSubscribeTimeToUserBindTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_user_bind', function (Blueprint $table) {
            $table->boolean('subscribe')->default(false)->after('language')->comment('是否关注');
            $table->timestamp('subscribe_at')->nullable()->after('subscribe')->comment('关注时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_user_bind', function (Blueprint $table) {
            $table->dropColumn('subscribe');
            $table->dropColumn('subscribe_at');
        });
    }
}
