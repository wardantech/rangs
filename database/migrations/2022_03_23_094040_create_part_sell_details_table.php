<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartSellDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_sell_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('partSell_id')->index()->nullable();
            $table->unsignedInteger('part_id')->index()->nullable();
            $table->integer('quantity');
            $table->float('selling_price');
            $table->float('amount');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('part_sell_details');
    }
}
