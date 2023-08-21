<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePriceManagementTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('price_management', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('part_id')->index();
            $table->unsignedInteger('model_id')->index();
            $table->float('cost_price_usd');
            $table->float('cost_price_bdt');
            $table->float('selling_price_bdt');
            $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('price_management');
    }
}
