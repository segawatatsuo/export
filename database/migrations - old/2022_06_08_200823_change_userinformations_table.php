<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUserinformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userinformations', function (Blueprint $table) {
            $table->string('consignee')->nullable()->change();
            $table->string('address_line1')->nullable()->change();
            $table->string('address_line2')->nullable()->change();
            $table->string('city')->nullable()->change();
            $table->string('state')->nullable()->change();
            $table->string('country_codes')->nullable()->change();
            $table->string('zip')->nullable()->change();
            $table->string('phone')->nullable()->change();
            $table->string('person')->nullable()->change();

            $table->string('bill_company_address_line1')->nullable()->change();
            $table->string('bill_company_address_line2')->nullable()->change();
            $table->string('bill_company_city')->nullable()->change();
            $table->string('bill_company_state')->nullable()->change();
            $table->string('bill_company_country')->nullable()->change();
            $table->string('bill_company_zip')->nullable()->change();
            $table->string('bill_company_phone')->nullable()->change();
            $table->string('president')->nullable()->change();

            $table->string('industry')->nullable()->change();
            $table->string('business_items')->nullable()->change();
            $table->string('customer_name')->nullable()->change();
            $table->string('fax')->nullable()->change();
            $table->string('fedex')->nullable()->change();
            $table->string('sns')->nullable()->change();
            $table->string('trading_term')->nullable()->change();
            $table->string('trading_history')->nullable()->change();
            $table->string('trading_rank')->nullable()->change();

            $table->string('initial')->nullable()->change();
            $table->string('website')->nullable()->change();
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('userinformations', function (Blueprint $table) {

        });
    }
}
