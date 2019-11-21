<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContactEmailToIbrandAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ibrand_addresses', function (Blueprint $table) {
//            $table->dropColumn('accept_name');
//            $table->dropColumn('mobile');
//            $table->string('contact_name')->after('user_id');
//            $table->string('contact_mobile')->after('contact_name');
            $table->string('contact_email')->after('mobile');
            $table->string('address_name')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ibrand_addresses', function (Blueprint $table) {
            $table->dropColumn('contact_email');
            $table->string('address_name')->nullable(false)->change();
            $table->string('address')->nullable(false)->change();
        });
    }
}
