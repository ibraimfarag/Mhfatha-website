<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComplaintsSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('complaints_suggestions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['Complaint', 'Suggestion']);
            $table->unsignedBigInteger('user_id');
            $table->boolean('is_vendor');
            $table->string('title');
            $table->text('description');
            $table->text('attachments')->nullable();
            $table->string('additional_phone')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaints_suggestions');
    }
}
