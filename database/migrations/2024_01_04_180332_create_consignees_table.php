<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsigneesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignees', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('user_id');
            $table->string('consignee')->nullable();
            $table->string('address_line1')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country_codes')->nullable();
            $table->string('post_code')->comment('zip')->nullable();
            $table->string('phone')->nullable();
            $table->string('name')->comment('person')->nullable();
            $table->string('default_destination')->comment('既定の住所');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consignees');
    }
}
