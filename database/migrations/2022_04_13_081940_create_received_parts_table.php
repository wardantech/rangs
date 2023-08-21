<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedPartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_parts', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('parts_return_id')->index()->nullable();
            $table->unsignedInteger('from_store_id')->index()->nullable();
            $table->unsignedInteger('to_store_id')->index()->nullable();
            $table->double('total_requested_quantity')->nullable();
            $table->double('total_receiving_quantity')->nullable();
            $table->string('belong_to')->nullable();
            $table->string('status')->nullable();
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
        Schema::dropIfExists('received_parts');
    }
}
