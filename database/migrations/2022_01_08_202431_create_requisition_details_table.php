<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('requisition_id')->index()->nullable();
            $table->unsignedInteger('parts_id')->index()->nullable();
            $table->unsignedInteger('model_id')->index()->nullable();
            $table->integer('stock_in_hand');
            $table->integer('required_quantity');
            $table->integer('belong_to');
            $table->integer('issued_quantity')->default('0');
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
        Schema::dropIfExists('requisition_details');
    }
}
