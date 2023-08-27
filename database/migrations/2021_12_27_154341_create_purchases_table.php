<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->date('purchase_date');
            $table->string('product_serial')->nullable();
            $table->string('invoice_number')->nullable();
            $table->unsignedInteger('customer_id')->index();
            $table->unsignedInteger('product_category_id')->index();
            $table->unsignedInteger('brand_id')->index();
            $table->unsignedInteger('brand_model_id')->index();
            $table->unsignedInteger('outlet_id')->index();
            $table->date('general_warranty_date')->nullable();
            $table->date('special_warranty_date')->nullable();
            $table->boolean('status')->nullable();
            $table->text('note')->nullable();
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
        Schema::dropIfExists('purchases');
    }
}
