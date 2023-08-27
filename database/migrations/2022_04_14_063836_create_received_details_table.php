<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('received_id')->nullable();
            $table->unsignedBigInteger('part_id')->nullable();
            $table->unsignedBigInteger('part_category_id')->nullable();
            $table->integer('allocation_details')->nullable();
            $table->integer('stock_in_hand')->nullable();
            $table->integer('issued_quantity')->nullable();
            $table->integer('requisition_quantity')->nullable();
            $table->integer('receiving_quantity')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
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
        Schema::dropIfExists('received_details');
    }
}
