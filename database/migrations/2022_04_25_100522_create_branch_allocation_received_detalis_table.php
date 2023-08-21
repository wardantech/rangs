<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAllocationReceivedDetalisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_allocation_received_detalis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('branch_allocation_received_id')->nullable();
            $table->unsignedBigInteger('part_id')->nullable();
            $table->unsignedBigInteger('part_category_id')->nullable();
            $table->unsignedBigInteger('allocation_details_id')->nullable();
            $table->unsignedBigInteger('rack_id')->nullable();
            $table->longText('bin_id')->nullable();
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
        Schema::dropIfExists('branch_allocation_received_detalis');
    }
}
