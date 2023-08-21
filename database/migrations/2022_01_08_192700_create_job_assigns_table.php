<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobAssignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_assigns', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedInteger('user_id')->index()->nullable();
            $table->unsignedInteger('employee_id')->index()->nullable();
            $table->unsignedInteger('job_id')->index()->nullable();
            $table->unsignedInteger('ticket_id')->index()->nullable();
            $table->unsignedInteger('outlet_id')->index()->nullable();
            $table->string('note')->nullable();
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
        Schema::dropIfExists('job_assigns');
    }
}
