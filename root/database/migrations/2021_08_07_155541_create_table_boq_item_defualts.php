<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableBoqItemDefualts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('boq_item_defualts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('boq_id');
            $table->integer('house_id');
            $table->integer('item_id');
            $table->string('unit');
            $table->float('qty_std')->default(0);
            $table->float('qty_add')->default(0);
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
        //
    }
}
