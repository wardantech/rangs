<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_models', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('sl_number');
            $table->unsignedInteger('purchase_id')->index()->nullable();
            $table->unsignedInteger('warranty_type_id')->index()->nullable();
            $table->unsignedInteger('job_priority_id')->index()->nullable();
            $table->unsignedInteger('service_type_id')->index()->nullable();
            $table->unsignedInteger('product_condition_id')->index()->nullable();
            $table->unsignedInteger('customer_id')->index()->nullable();
            $table->unsignedInteger('district_id')->index()->nullable();
            $table->unsignedInteger('thana_id')->index()->nullable();
            $table->json('fault_description_id');
            $table->json('accessories_list_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedInteger('product_receive_mode_id')->index()->nullable();
            $table->unsignedInteger('expected_delivery_mode_id')->index()->nullable();
            $table->string('customer_note');
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
        Schema::dropIfExists('job_models');
    }
}
