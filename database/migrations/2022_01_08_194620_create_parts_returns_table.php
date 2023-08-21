<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartsReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('parts_returns', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('from_store_id')->index()->nullable();
            $table->unsignedInteger('to_store_id')->index()->nullable();
            $table->string('sl_number')->nullable();
            $table->double('total_quantity')->nullable();
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
        Schema::dropIfExists('parts_returns');
    }
}
