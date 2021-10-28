<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnBuildingIdToUsageFormulars extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usage_formulas', function (Blueprint $table) {
            $table->integer('building_id')->default(0);
            $table->integer('house_type_id')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usage_formulas', function (Blueprint $table) {
            $table->integer('building_id')->default(0);
            $table->integer('house_type_id')->default(0);
        });
    }
}
