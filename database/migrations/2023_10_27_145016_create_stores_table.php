<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('location');
            $table->string('phone');
            $table->string('url_map')->nullable();
            $table->string('photo')->nullable();
            $table->string('qr')->nullable();
            $table->decimal('total_payments', 10, 2)->default(0);
            $table->decimal('total_withdrawals', 10, 2)->default(0);
            $table->unsignedInteger('count_times')->default(0);
            $table->string('work_hours');
            $table->string('work_days');
            $table->string('city')->nullable(); // Add city field
            $table->unsignedBigInteger('region')->nullable(); // Add region field
            $table->decimal('latitude', 10, 8)->nullable();
$table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('status')->default(1); // 1 for active, 0 for inactive
            $table->boolean('is_deleted')->default(0); // 1 for active, 0 for inactive
            $table->boolean('verifcation')->default(0); // 1 for active, 0 for inactive
            $table->boolean('is_bann')->default(0); // 1 for active, 0 for inactive
            $table->text('bann_msg')->nullable();
            $table->timestamps();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->foreign('category_id')->references('id')->on('store_categories');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('region')->references('id')->on('regions');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
