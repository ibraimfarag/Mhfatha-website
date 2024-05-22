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
            $table->unsignedBigInteger('option_id'); // Add option_id foreign key
            $table->unsignedBigInteger('parent_id'); // Add parent_id foreign key
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('discount_id');
            $table->boolean('is_vendor');
            $table->longText('description');
            $table->enum('status', ['read', 'unread', 'under processer', 'closed'])->default('unread');
            $table->string('ticket_number')->unique()->default(function () {
                return 'TICKET' . str_pad(mt_rand(900000, 999999), 6, '0', STR_PAD_LEFT);
            });
            $table->text('attachments')->nullable();
            $table->string('additional_phone')->nullable();
            $table->timestamps();


            $table->foreign('option_id')->references('id')->on('complaints_suggestions_option');
            $table->foreign('parent_id')->references('id')->on('complaints_suggestions_parent');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('discount_id')->references('id')->on('discounts');
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
