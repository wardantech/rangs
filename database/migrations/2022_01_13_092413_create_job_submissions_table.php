<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_submissions', function (Blueprint $table) {
            $table->id();
            // $table->unsignedInteger('job_id')->index()->nullable();
            $table->foreign('job_id')->references('id')->on('jobs')->onDelete('cascade');
            $table->unsignedBigInteger('team_leader_user_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->date('submission_date')->nullable();
            $table->double('service_amount')->nullable();
            $table->text('job_start_image')->nullable();
            $table->string('spare')->nullable();
            $table->text('spare_image')->nullable();
            $table->string('customer_ack')->nullable();
            $table->text('customer_ack_image')->nullable();
            $table->string('money_receipt')->nullable();
            $table->text('money_receipt_image')->nullable();
            $table->string('warranty_card')->nullable();
            $table->text('warranty_card_image')->nullable();
            $table->text('remark')->nullable();
            $table->double('subtotal_for_spare')->default(0);
            $table->double('subtotal_for_servicing')->default(0);
            $table->double('fault_finding_charges')->default(0);
            $table->double('repair_charges')->default(0);
            $table->double('vat')->default(0);
            $table->double('other_charges')->default(0);
            $table->double('discount')->default(0);
            $table->double('advance_amount')->default(0);
            $table->double('total_amount:')->default(0);
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
        Schema::dropIfExists('job_submissions');
    }
}
