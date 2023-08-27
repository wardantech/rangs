<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_stocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('receive_id')->nullable();
            $table->unsignedBigInteger('branch_received_details_id');
            $table->integer('accept_loan_request_details_id')->index()->nullable();
            $table->integer('parts_model_id')->index()->nullable();
            $table->integer('sale_id')->index()->nullable();
            $table->integer('part_id')->index()->nullable();
            $table->integer('store_id')->index()->nullable();
            $table->integer('bin_id')->index()->nullable();
            $table->integer('rack_id')->index()->nullable();
            $table->integer('vendor_id')->index()->nullable();
            $table->double('stock_in')->default(0);
            $table->double('stock_out')->default(0);
            $table->integer('belong_to')->nullable()->comment('1=central_wirehouse,2=outlet,3=service_center,4=vendor,5=technician');
            $table->integer('type')->nullable()->comment('1=stock-in-from-central-wirehouse,2=stock-out-to-central-wirehouse,3=stock-in-from-outlet,4=stock-out-to-outlet,5=stock-in-from-service-center,6=stock-out-to-service-center,7=stock-in-from-vendor,8=stock-out-to-vendor,9=stock-in-from-technician,10=stock-out-to-technician');
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
        Schema::dropIfExists('inventory_stocks');
    }
}
