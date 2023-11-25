<!DOCTYPE html>
<html lang="ar" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Common Meta Tags -->
    <title>{{ app()->getLocale() === 'ar' ? $site_title_ar : $site_title_en }} @yield('title')</title>
    <meta name="description" content="{{ app()->getLocale() === 'ar' ? $site_title_ar : $site_title_en }}">
    <meta name="keywords" content="{{ app()->getLocale() === 'ar' ? $site_meta_keywords_ar : $site_meta_keywords_en }}">

    <!-- Open Graph Meta Tags (Facebook, Twitter, LinkedIn, etc.) -->
    <meta property="og:title" content="{{ app()->getLocale() === 'ar' ? $site_description_ar : $site_description_en }}">
    <meta property="og:description" content="{{ app()->getLocale() === 'ar' ? $site_description_ar : $site_description_en }}">
    <meta property="og:image" content="URL to your Open Graph image">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <!-- Twitter Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ app()->getLocale() === 'ar' ? $site_title_ar : $site_title_en }} @yield('title')">
    <meta name="twitter:description" content="{{ app()->getLocale() === 'ar' ? $site_description_ar : $site_description_en }}">
    <meta name="twitter:image" content="URL to your Twitter image">

    <link rel="icon" href="{{ $site_favicon }}" type="image/x-icon">


    @include('FrontEnd.layouts.css')

  <!-- =======================================================
  * Template Name: Ma7fza
  * Updated: oct 18 2023 with Bootstrap v5.3.2
  * Author: ibrahimahmed.info
  ======================================================== -->
</head>
