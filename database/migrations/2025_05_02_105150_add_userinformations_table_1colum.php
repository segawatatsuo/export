<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserinformationsTable1colum extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('userinformations', function (Blueprint $table) {
            $table->string('bill_company_name')->nullable()->after('person');
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
            $table->dropColumn('bill_company_name');
        });
    }
}
