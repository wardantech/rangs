<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCashTransectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cash_transections', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->unsignedBigInteger('outlet_id')->index();
            $table->unsignedBigInteger('deposit_id')->nullable();
            $table->unsignedBigInteger('expense_id')->nullable();
            $table->unsignedBigInteger('revenue_id')->nullable();
            $table->enum('purpose', ['withdraw', 'deposit', 'recevied_payment', 'given_payment']);
            $table->string('type')->nullable();
            $table->double('current_balance')->default(0);
            $table->double('cash_in')->default(0);
            $table->double('cash_out')->default(0);
            $table->string('cheque_number')->nullable();
            $table->text('remarks')->nullable();
            $table->text('balance_transfer')->nullable();
            $table->integer('belong_to')->nullable()
                    ->comment('1=central_wirehouse,2=outlet,3=service_center,4=vendor,5=technician');
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
        Schema::dropIfExists('cash_transections');
    }
}
