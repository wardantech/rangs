<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerAdvancedPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_advanced_payments', function (Blueprint $table) {
            $table->id();
            $table->string('adv_mr_no');
            $table->date('advance_receipt_date');
            $table->string('job_no');
            $table->unsignedInteger('branch_id')->index()->nullable();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->text('customer_address');
            $table->date('receive_date');
            $table->string('product_name');
            $table->string('product_sl');
            $table->float('advance_amount');
            $table->tinyInteger('pay_type');
            $table->text('remark');
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
        Schema::dropIfExists('customer_advanced_payments');
    }
}
