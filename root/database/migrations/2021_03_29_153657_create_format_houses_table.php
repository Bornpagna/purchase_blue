<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFormatHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->unique();
            $table->string('house_no');
            $table->string('house_desc')->nullable();
            $table->integer('house_type');
            $table->integer('pro_id');
            $table->integer('zone_id')->default(0);
            $table->integer('block_id')->default(0);
            $table->integer('street_id');
            $table->integer('status')->default(1)->comment('1=Start,2=Finish,3=Stop');
            $table->integer('created_by')->default(0);
            $table->integer('updated_by')->default(0);
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
        // Schema::dropIfExists('houses');
    }
}
