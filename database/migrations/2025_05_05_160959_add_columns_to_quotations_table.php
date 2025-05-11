<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->string('consignees_name')->nullable()->comment('お届け先名前');
            $table->string('consignees_address_line1')->nullable();
            $table->string('consignees_address_line2')->nullable();
            $table->string('consignees_city')->nullable();
            $table->string('consignees_state')->nullable();
            $table->string('consignees_country_codes')->nullable();
            $table->string('consignees_postal_code')->nullable();
            $table->string('consignees_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn('consignees_name');
            $table->dropColumn('consignees_address_line1');
            $table->dropColumn('consignees_address_line2');
            $table->dropColumn('consignees_city');
            $table->dropColumn('consignees_state');
            $table->dropColumn('consignees_country_codes');
            $table->dropColumn('consignees_postal_code');
            $table->dropColumn('consignees_phone');
        });
    }
}
