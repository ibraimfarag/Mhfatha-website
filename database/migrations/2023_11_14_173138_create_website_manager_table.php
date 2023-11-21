<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWebsiteManagerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('website_managers', function (Blueprint $table) {
            $table->id();
            
            // Section 1: General
            $table->json('site_title')->nullable();
            $table->json('site_description')->nullable();
            $table->string('site_logo')->nullable();
            $table->string('site_favicon')->nullable();
            $table->json('site_meta_keywords')->nullable();
            $table->json('site_meta_description')->nullable();
            $table->boolean('site_status')->default(true);
            $table->string('map_distance')->nullable();
            $table->string('commission')->nullable();
            $table->string('company_mail')->nullable();
            $table->string('company_phone')->nullable();

            // Section 2: Hero Intro Sliders
            $table->json('hero_intro_sliders')->nullable();

            // Section 3: Who Are We
            $table->json('who_are_we_title')->nullable();
            $table->json('who_are_we_description')->nullable();
            $table->string('who_are_we_image')->nullable();
            $table->json('tell_public_story')->nullable();

            // Section 4: Advantages
            $table->json('advantages_title')->nullable();
            $table->json('advantages_description')->nullable();
            $table->string('advantages_image')->nullable();
            $table->json('advantages_tabs')->nullable();

            // Section 5: Download App
            $table->string('download_app_image')->nullable();
            $table->json('download_app_description')->nullable();
            $table->string('ios_link')->nullable();
            $table->string('android_link')->nullable();

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
        Schema::dropIfExists('website_managers');
    }
}
