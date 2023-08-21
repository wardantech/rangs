<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcceptLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('accept_loan_requests', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('belong_to');
            $table->unsignedInteger('outlate_id')->index()->nullable();
            $table->unsignedInteger('store_id')->index()->nullable();
            $table->unsignedInteger('loan_id')->index()->nullable();
            $table->integer('issue_quantity');
            $table->integer('status');
            $table->integer('receive_id')->nullable();
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
        Schema::dropIfExists('accept_loan_requests');
    }
}
