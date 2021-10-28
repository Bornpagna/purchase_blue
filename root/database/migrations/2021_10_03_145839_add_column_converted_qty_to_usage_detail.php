<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnConvertedQtyToUsageDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usage_details', function (Blueprint $table) {
            $table->integer('qty_converted')->nullable()->default(0);
            $table->integer('price_converted')->nullable()->default(0);
            $table->integer('total_price_converted')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usage_details', function (Blueprint $table) {
            $table->integer('qty_converted')->nullable()->default(0);
            $table->integer('price_converted')->nullable()->default(0);
            $table->integer('total_price_converted')->nullable()->default(0);
        });
    }
}
