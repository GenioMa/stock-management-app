<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductPurchaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_purchase', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('qte');
            $table->double('purchasing_price', 20, 2);
            $table->integer("product_id")->unsigned();
            $table->foreign('product_id')->references('id')->on('products');
            $table->integer("supplier_id")->unsigned();
            $table->foreign('supplier_id')->references('id')->on('suppliers');
            $table->integer('archive')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_purchase');
    }
}
