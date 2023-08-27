<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('job_number');
            $table->unsignedInteger('purchase_id')->index()->nullable();
            $table->unsignedInteger('ticket_id')->index()->nullable();
            $table->unsignedInteger('outlet_id')->index()->nullable();
            $table->string('note')->nullable();
            $table->text('job_close_remark')->nullable();
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
        Schema::dropIfExists('jobs');
    }
}
