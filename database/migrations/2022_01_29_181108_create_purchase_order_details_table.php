<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseOrderDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchase_order_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('purchase_order_id')->index()->nullable();
            $table->integer('parts_model_id')->index()->nullable();
            $table->integer('part_id')->index()->nullable();
            $table->integer('store_id')->index()->nullable();
            $table->integer('required_qnty')->nullable();
            $table->integer('issued_qnty')->nullable();
            $table->integer('belong_to')->nullable()->comment('1=central_wirehouse,2=outlet,3=service_center,4=vendor,5=technician');
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
        Schema::dropIfExists('purchase_order_details');
    }
}
