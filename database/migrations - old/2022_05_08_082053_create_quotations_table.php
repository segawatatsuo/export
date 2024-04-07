<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('quotation_no')->nullable();
            $table->timestamp('date_of_issue')->nullable();
            $table->string('shipper')->nullable();
            $table->integer('consignee_no')->nullable();
            $table->string('consignee')->nullable();
            $table->string('port_of_loading')->nullable();
            $table->string('final_destination')->nullable();
            $table->string('sailing_on')->nullable();
            $table->string('arriving_on')->nullable();
            $table->string('expory')->nullable();

            $table->string('product_code')->nullable();
            $table->string('product_name')->nullable();

            $table->string('color')->nullable();
            $table->integer('quantity')->nullable();
            $table->integer('ctn')->nullable();
            $table->integer('unit_price')->nullable();
            $table->integer('amount')->nullable();

            $table->integer('quantity_total')->nullable();
            $table->integer('ctn_total')->nullable();
            $table->integer('amount_total')->nullable();
            $table->string('pdf')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotations');
    }
}
