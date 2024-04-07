<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            
            $table->string('product_code')->nullable();
            $table->string('jan_code')->nullable();
            $table->string('asin')->nullable();
            
            $table->string('maker_name')->nullable();
            
            $table->string('category')->nullable();
            $table->string('group')->nullable();
            $table->string('color')->nullable();
            $table->string('color_map')->nullable();
            $table->string('capacity')->nullable();
            $table->string('title_header')->nullable();
            $table->string('product_name')->nullable();

            $table->integer('price')->nullable();

            $table->string('packaging_height')->nullable();
            $table->string('packaging_vertical')->nullable();
            $table->string('packaging_width')->nullable();
            $table->string('packaging_weight')->nullable();
            $table->string('unit_of_length')->nullable();//単位(Cm inchなど)

            $table->string('units')->nullable();
            $table->string('unit_type')->nullable();

            $table->integer('stock')->nullable();
            $table->date('deadline')->nullable();

            $table->text('depiction1')->nullable();
            $table->text('depiction2')->nullable();

            $table->string('img1')->nullable();
            $table->string('img2')->nullable();
            $table->string('img3')->nullable();
            $table->string('img4')->nullable();
            $table->string('img5')->nullable();

            $table->string('group_j')->nullable();
            $table->string('color_j')->nullable();
            $table->string('product_name_j')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
