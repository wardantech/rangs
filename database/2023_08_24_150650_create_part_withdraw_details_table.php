<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartWithdrawDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('part_withdraw_details', function (Blueprint $table) {
            $table->id();
            $table->integer('part_withdraw_id')->index();
            $table->integer('job_id')->index();
            $table->integer('inventory_stock_id')->index();
            $table->integer('part_id')->index();
            $table->integer('used_qnty')->default(0);
            $table->integer('required_qnty')->default(0);
            $table->integer('status')->default(0);
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
        Schema::dropIfExists('part_withdraw_details');
    }
}
