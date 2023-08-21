<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartsReturnDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts_return_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('partsreturn_id')->index()->nullable();
            $table->unsignedInteger('parts_id')->index()->nullable();
            $table->unsignedInteger('model_id')->index()->nullable();
            $table->integer('stock_in_hand')->nullable();
            $table->integer('required_quantity')->nullable();
            $table->integer('belongs_to')->nullable();
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
        Schema::dropIfExists('parts_return_details');
    }
}
