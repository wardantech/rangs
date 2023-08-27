<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedLoanDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_loan_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('received_loan_id')->index();
            $table->unsignedInteger('loan_details_id')->index();
            $table->unsignedInteger('part_id')->index();
            $table->unsignedInteger('rack_id')->index();
            $table->json('bin_id')->index();
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
        Schema::dropIfExists('received_loan_details');
    }
}
