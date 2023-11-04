<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->decimal('percent', 5, 2);
            $table->string('category');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('discounts_status')->default('working'); // Default to 'working'
            $table->boolean('is_deleted')->default(0); // 1 for active, 0 for inactive

            $table->timestamps();
    
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
