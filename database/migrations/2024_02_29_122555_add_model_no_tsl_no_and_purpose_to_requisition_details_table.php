<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddModelNoTslNoAndPurposeToRequisitionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requisition_details', function (Blueprint $table) {
            $table->string('model_no')->nullable()->after('required_quantity');
            $table->string('tsl_no')->nullable();
            $table->tinyInteger('purpose')->nullable()->commet("1=On Payment, 2=Under Warranty, 3=Stock");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requisition_details', function (Blueprint $table) {
            //
        });
    }
}
