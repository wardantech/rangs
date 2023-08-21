<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventorysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventorys', function (Blueprint $table) {
            $table->id();
            $table->string('model_id');
            $table->string('invoice_number');
            $table->string('lc_number');
            $table->string('sending_date');
            $table->integer('source_id');
            $table->integer('store_id');
            $table->integer('part_id');
			$table->date('order_date');
			$table->date('receive_date');
            $table->json('bin_id');
            $table->integer('rack_id');
            $table->integer('vendor_id')->index()->nullable();
            $table->double('quantity');
			$table->double('usd', 10, 2);
			$table->double('bdt', 10, 2);
			$table->double('selling_price', 10, 2);
            $table->text('description');
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
        Schema::dropIfExists('inventorys');
    }
}
