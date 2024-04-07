<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillCompanyToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('bill_company_address_line1');
            $table->string('bill_company_address_line2');
            $table->string('bill_company_city');
            $table->string('bill_company_state');
            $table->string('bill_company_country');
            $table->string('bill_company_zip');
            $table->string('bill_company_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            //
        });
    }
}
