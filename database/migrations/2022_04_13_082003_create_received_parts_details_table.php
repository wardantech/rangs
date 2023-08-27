<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedPartsDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_parts_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('received_parts_id')->index();
            $table->unsignedInteger('parts_return_details_id')->index();
            $table->tinyInteger('belong_to');
            $table->unsignedInteger('store_id');
            $table->unsignedInteger('rack_id');
            $table->json('bin_id');
            $table->unsignedInteger('part_category_id');
            $table->unsignedInteger('part_id');
            $table->integer('received_quantity');
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
        Schema::dropIfExists('received_parts_details');
    }
}
