<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobSubmissionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_submission_details', function (Blueprint $table) {
            $table->id();
            // $table->unsignedInteger('job_submission_id')->index()->nullable();
            $table->unsignedBigInteger('job_submission_id')->index()->nullable();
            $table->foreign('job_submission_id')->references('id')->on('job_submissions')->onDelete('cascade');
            $table->unsignedInteger('part_id')->index();
            $table->unsignedInteger('part_name')->nullable();
            $table->integer('used_quantity')->nullable();
            $table->integer('selling_price_bdt')->nullable();
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
        Schema::dropIfExists('job_submission_details');
    }
}
