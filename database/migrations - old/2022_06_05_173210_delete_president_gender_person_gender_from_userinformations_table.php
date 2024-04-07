<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeletePresidentGenderPersonGenderFromUserinformationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userinformations', function (Blueprint $table) {
            $table->dropColumn('person_gender');
            $table->dropColumn('president_gender');
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
            $table->string('person_gender');
            $table->string('president_gender');
        });
    }
}
