<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->date('birthday');
            $table->string('city')->nullable();
            $table->unsignedBigInteger('region')->nullable();
            $table->string('mobile');
            $table->string('photo')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_vendor')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->string('device_token')->nullable();
            $table->string('platform')->nullable();
            $table->string('platform_device')->nullable();
            $table->string('platform_version')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_banned')->default(0);
            $table->boolean('is_deleted')->default(0);
            $table->boolean('is_temporarily')->default(0);
            $table->json('messages')->nullable();
            $table->json('notifications')->nullable();
            $table->string('lang')->default('en')->after('notifications'); // Assuming 'en' is the default language
            $table->rememberToken();
            $table->timestamps();
            $table->foreign('region')->references('id')->on('regions');
            $table->foreign('city')->references('id')->on('cities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
