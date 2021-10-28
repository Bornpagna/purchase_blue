<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnToUsageDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usage_details', function (Blueprint $table) {
            $table->integer('zone_id')->nullable()->default(0);
            $table->integer('block_id')->nullable()->default(0);
            $table->integer('building_id')->nullable()->default(0);
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
            $table->integer('zone_id')->nullable()->default(0);
            $table->integer('block_id')->nullable()->default(0);
            $table->integer('building_id')->nullable()->default(0);
        });
    }
}
