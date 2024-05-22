<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsSuggestionsOptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints_suggestions_option', function (Blueprint $table) {
            $table->id();
            $table->string('option_ar');
            $table->string('option_en');
            $table->unsignedBigInteger('parent_id');
            $table->foreign('parent_id')->references('id')->on('complaints_suggestions_parent')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints_suggestions_option');
    }
}
