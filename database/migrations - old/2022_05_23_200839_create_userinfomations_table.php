<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserinformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('Userinformations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();

            $table->string('consignee');
            $table->string('address_line1');
            $table->string('address_line2');
            $table->string('city');
            $table->string('state');
            $table->string('country_codes');
            $table->string('zip');
            $table->string('phone');
            $table->string('person');
            $table->string('person_gender');
            $table->string('bill_company_address_line1');
            $table->string('bill_company_address_line2');
            $table->string('bill_company_city');
            $table->string('bill_company_state');
            $table->string('bill_company_country');
            $table->string('bill_company_zip');
            $table->string('bill_company_phone');
            $table->string('president');
            $table->string('president_gender');
            $table->string('industry');
            $table->string('business_items');
            $table->string('customer_name');
            $table->string('fax');
            $table->string('fedex');
            $table->string('sns');
            $table->string('trading_term');
            $table->string('trading_history');
            $table->string('trading_rank');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('Userinformations');
    }
}
