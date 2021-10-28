<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnWorkingTypeToBoqItemDefual extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('boq_item_defualts', function (Blueprint $table) {
            $table->integer('working_type')->default(0);
            $table->decimal('cost',25,4)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('boq_item_defualts', function (Blueprint $table) {
            $table->integer('working_type')->default(0);
            $table->decimal('cost',25,4)->default(0);
        });
    }
}
