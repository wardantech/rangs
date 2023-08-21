<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivedLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('received_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('loan_id')->index();
            $table->date('date');
            $table->unsignedInteger('to_store_id')->index();
            $table->unsignedInteger('from_store_id')->index();
            $table->integer('total_received_quantity');
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
        Schema::dropIfExists('received_loans');
    }
}
