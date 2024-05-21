<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketRecommendationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ticket_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ticket_id')->index();
            $table->unsignedInteger('referrer_outlet_id')->index();
            $table->unsignedInteger('recommended_outlet_id')->index();
            $table->string('recommend_note')->nullable();
            $table->tinyInteger('type')->default(1)->commet("1=TeamLeader 2=Call Center	");
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
        Schema::dropIfExists('ticket_recommendations');
    }
}
