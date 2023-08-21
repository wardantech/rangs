<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllocationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allocation_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('allocation_id')->index()->nullable();
            $table->unsignedInteger('parts_id')->index()->nullable();
            $table->unsignedInteger('model_id')->index()->nullable();
            $table->integer('requisition_quantity')->nullable();
            $table->integer('issued_quantity')->nullable();
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
        Schema::dropIfExists('allocation_details');
    }
}
