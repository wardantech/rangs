<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchAllocationReceivedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('branch_allocation_receiveds', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('belong_to');
            $table->unsignedInteger('store_id');
            $table->unsignedBigInteger('allocation_id');
            $table->unsignedBigInteger('employee_id')->nullable();
            $table->string('requisition_no')->nullable();
            $table->integer('allocate_quantity')->nullable();
            $table->tinyInteger('is_received')->default(0);
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
        Schema::dropIfExists('branch_allocation_receiveds');
    }
}
