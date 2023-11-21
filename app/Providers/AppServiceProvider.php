<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\WebsiteManager;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $websiteManager = WebsiteManager::first();

        // Share the site_title Ar & En variables
        View::share('site_title_ar', $websiteManager->site_title['ar']);
        View::share('site_title_en', $websiteManager->site_title['en']);

        // Share the site_description Ar & En variable
        View::share('site_description_ar', $websiteManager->site_description['ar']);
        View::share('site_description_en', $websiteManager->site_description['en']);

    }
}
