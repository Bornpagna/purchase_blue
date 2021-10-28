<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTypeColumnConvertedDeliveryItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->decimal('qty_converted',25,4)->change();
            $table->decimal('price_converted',25,3)->change();
            $table->decimal('total_price_converted',25,2)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->decimal('qty_converted',25,4)->change();
            $table->decimal('price_converted',25,3)->change();
            $table->decimal('total_price_converted',25,2)->change();
        });
    }
}
