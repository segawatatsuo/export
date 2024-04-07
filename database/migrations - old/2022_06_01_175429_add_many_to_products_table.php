<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddManyToProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {

            $table->float('sort_order')->nullable();
            $table->string('brand_name')->nullable();
            $table->integer('spec')->nullable();
            $table->string('unit')->nullable();
            $table->integer('number_of_pieces')->nullable();
            $table->string('ean_code')->nullable();
            $table->string('sku_code')->nullable();
            $table->integer('manufacturing_delivery_date')->nullable();
            $table->integer('stock_delivery_date')->nullable();
            
            $table->integer('price_regular')->nullable();
            $table->integer('price_fedex')->nullable();
            $table->integer('price_air_1')->nullable();
            $table->integer('price_air_2')->nullable();
            $table->integer('price_ship')->nullable();
            $table->string('sds_file_upload')->nullable();
            $table->string('explosives_un')->nullable();
            $table->string('explosives_class')->nullable();
            
            $table->string('fedex_commercial_invoice_text')->nullable();
            $table->string('fedex_commercial_invoice_hs_code')->nullable();
            $table->integer('fedex_commercial_invoice_unit_value')->nullable();
                        
            $table->string('dangerous_goods_declaration_sipping_name')->nullable();
            $table->string('dangerous_goods_declaration_box_type')->nullable();
            $table->string('dangerous_goods_declaration_packing')->nullable();
            
            $table->integer('product_size_w')->nullable();
            $table->integer('product_size_d')->nullable();
            $table->integer('product_size_h')->nullable();
            
            $table->integer('product_weight')->nullable();
            
            $table->integer('input_number')->nullable();
            $table->integer('carton_size_w')->nullable();
            $table->integer('carton_size_d')->nullable();
            $table->integer('carton_size_h')->nullable();
                        
            $table->float('carton_weight_net')->nullable();
            $table->float('carton_weight_gross')->nullable();
            $table->float('carton_weight_m3')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
