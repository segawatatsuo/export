<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('user_id');
            $table->string('pic_id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('country')->nullable();
            $table->string('company_name')->nullable();
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
        Schema::dropIfExists('pics');
    }
}
