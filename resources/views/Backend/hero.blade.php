@extends('Backend.layout.master')
@section('backend-content')

<div class="container" id="GeneralSection">

    <div class="row">
        <h4>
            <i class="fa-solid fa-globe"></i> {{ app()->getLocale() === 'ar' ? 'اعدادات البنر' : 'Hero Setttings' }}
        </h4>
        <p class="gray">
            اعدادات عامة
        </p>
    </div>

    <div class="row">
        <form action="{{ route('Section.update') }}" method="post" enctype="multipart/form-data">

            @csrf
            @method('PUT')
            <div class="row">

                <div class="form-group">
                    <div class="name-group">
                        <label for="language">{{ app()->getLocale() === 'ar' ? 'لغة العرض ' : 'hero language' }}</label>

                <select class="form-control" name="language">
                    <option value="ar">Arabic</option>
                    <option value="en">English</option>
                </select>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-box">
                        {{-- <img  src="{{ $site_logo }}" alt="{{  $websiteManager->site_logo }}" class="img-thumbnail image-preview-genral"> --}}
                
                        <div class="form-group">
                            <div class="name-group">
                                <label for="site_logo">{{ app()->getLocale() === 'ar' ? 'شعار الموقع' : 'Site Logo' }}</label>
                                <input class="form-control" type="file" name="right_image" accept="image/*">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border-box">
                        {{-- <img  src="{{ asset('FrontEnd/assets/images/logos/' . $websiteManager->site_favicon) }}" alt="{{ $websiteManager->site_favicon }}" class="img-thumbnail image-preview-genral"> --}}
                
                        <div class="form-group">
                            <div class="name-group">
                                <label for="site_logo">{{ app()->getLocale() === 'ar' ? 'ايقونه الموقع' : 'Site favicon' }}</label>
                                <input  class="form-control"type="file" name="left_image" accept="image/*">
                            </div>
                        </div>
                    </div>
    
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
