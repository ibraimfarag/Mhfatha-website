@extends('Backend.layout.master')
@section('backend-content')

<div class="container" id="GeneralSection">

    <div class="row">
        <h4>
            <i class="fa-solid fa-globe"></i>   {{ app()->getLocale() === 'ar' ? 'اعدادات عامه' : 'Global Setttings' }}
        </h4>
        <p class="gray">
            اعدادات عامة  
        </p>
    </div>
    
    <div class="row">
        <form action="{{ route('Section.update') }}" method="post" enctype="multipart/form-data">
            
            @csrf
            @method('PUT')
            <div class="form-group">
                <div class="name-group">
            <!-- Section 1: General Settings -->
            <label for="site_title_ar">{{ app()->getLocale() === 'ar' ? 'عنوان الموقع (بالعربية)' : 'Site Title (Arabic)' }}</label>
            <input type="text" class="form-control" name="site_title[ar]" value="{{ old('site_title.ar', $websiteManager->site_title['ar']) }}">

                </div>
                <div class="name-group">

            <label for="site_title_en">{{ app()->getLocale() === 'ar' ? 'عنوان الموقع (بالإنجليزية)' : 'Site Title (English)' }}</label>
            <input type="text" class="form-control" name="site_title[en]" value="{{ old('site_title.en', $websiteManager->site_title['en']) }}">
                </div>
            </div>
            <div class="form-group">
                <div class="name-group">
            <label for="site_description_ar">{{ app()->getLocale() === 'ar' ? 'وصف الموقع (بالعربية)' : 'Site Description (Arabic)' }}</label>
            <textarea class="form-control" name="site_description[ar]">{{ old('site_description.ar', $websiteManager->site_description['ar']) }}</textarea>
                </div>
                </div>
                            <div class="form-group">

                <div class="name-group">

            <label for="site_description_en">{{ app()->getLocale() === 'ar' ? 'وصف الموقع (بالإنجليزية)' : 'Site Description (English)' }}</label>
            <textarea class="form-control" name="site_description[en]">{{ old('site_description.en', $websiteManager->site_description['en']) }}</textarea>

                </div>
            </div>
            <div class="form-group">
                <div class="name-group">
            <label for="site_logo">{{ app()->getLocale() === 'ar' ? 'شعار الموقع' : 'Site Logo' }}</label>
            <input class="form-control" type="file" name="site_logo">
                </div>
            </div>
            <div class="form-group">
                <div class="name-group">
            <label for="site_logo">{{ app()->getLocale() === 'ar' ? 'ايقونه الموقع' : 'Site favicon' }}</label>
            <input class="form-control" type="file" name="site_favicon">
                </div>
            </div>
            <!-- Repeat similar structure for other fields -->
            <div class="form-group">
                <div class="name-group">
                    <label for="site_meta_keywords_ar">{{ app()->getLocale() === 'ar' ? 'الكلمات الرئيسية (بالعربية)' : 'Site Meta Keywords (Arabic)' }}</label>
                    <textarea  id="site_meta_keywords" class="form-control" name="site_meta_keywords[ar]" >{{ old('site_meta_keywords.ar', $websiteManager->site_meta_keywords['ar'] ?? '') }}</textarea>
                </div>
            </div>
            <div class="form-group">

                <div class="name-group">
                    <label for="site_meta_keywords_en">{{ app()->getLocale() === 'ar' ? 'الكلمات الرئيسية (بالإنجليزية)' : 'Site Meta Keywords (English)' }}</label>
                    <textarea id="site_meta_keywords" class="form-control" name="site_meta_keywords[en]" >{{ old('site_meta_keywords.en', $websiteManager->site_meta_keywords['en'] ?? '') }}</textarea>
                </div>
            </div>
        
            
            <!-- Submit button -->
            <button type="submit" class="btn btn-primary button_dash">
                {{ app()->getLocale() === 'ar' ? 'تحديث ' : 'Update ' }}
            </button>
        </form>
    </div>
</div>

@endsection

@section('backend-js')

    
@endsection
