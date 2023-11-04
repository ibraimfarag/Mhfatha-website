@extends('FrontEnd.profile.layout.master')
@section('dash-content')

<div class="container" id="stores">

    <div class="container">
        <h3>{{ app()->getLocale() === 'ar' ? 'إنشاء متجر جديد' : 'Create a New Store' }}</h3>
        <form action="{{ route('stores.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <!-- Display validation errors, if any -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

            <!-- Store Name -->
            <div class="form-group">
                <div class="name-group">

                <label for="name">{{ app()->getLocale() === 'ar' ? 'اسم المتجر' : 'Store Name' }}</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}">
            </div>
    
            <!-- Location -->
            <div class="name-group">
                <label for="location">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Location' }}</label>
                <input type="text" name="location" id="location" class="form-control" value="{{ old('location') }}">
            </div>
            </div>
    
            <!-- Phone -->
            <div class="form-group">
                <div class="name-group">

                <label for="phone">{{ app()->getLocale() === 'ar' ? 'هاتف المتجر' : 'Phone' }}</label>
                <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone') }}">
            </div>
    
            </div>
    
            <!-- URL Map (Optional) -->
            <div class="form-group">
                <div class="name-group">

                <label for="url_map">{{ app()->getLocale() === 'ar' ? 'خريطة المتجر' : 'store on map' }}</label>
                <input type="text" name="url_map" id="url_map" class="form-control" value="{{ old('url_map') }}">
            </div>
    
            <!-- Photo (Optional) -->
            <div class="name-group">
                <div class="form-group">
                    <img id="image-preview" src="{{ asset('FrontEnd/assets/images/store_images/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="img-thumbnail image-preview" >
                </div>
                <div class="form-group">

                    <div class="name-group">
                        <label for="store_image">
                            {{ app()->getLocale() === 'ar' ? 'صورة المتجر' : 'store Image' }}
                        </label>
                        <input id="profile_image" type="file" class="form-control @error('store_image') is-invalid @enderror" name="store_image">
                        @error('store_image')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
            </div>
    


<!-- Work Days (Optional) -->
<div class="form-group">
    <label>{{ app()->getLocale() === 'ar' ? 'أيام العمل' : 'Work Days' }}</label><br>

    @php
        // Get the selected work days from the old input
        $selectedWorkDays = old('work_days', []);
    @endphp

    @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
        <label for="{{ $day }}">
            <input type="checkbox" id="{{ $day }}" name="work_days[]" value="{{ $day }}" 
                {{ in_array($day, $selectedWorkDays) ? 'checked' : '' }}>
            {{ app()->getLocale() === 'ar' ? 
                ($day === 'sunday' ? 'الأحد' : ($day === 'monday' ? 'الإثنين' : 
                ($day === 'tuesday' ? 'الثلاثاء' : ($day === 'wednesday' ? 'الأربعاء' : 
                ($day === 'thursday' ? 'الخميس' : ($day === 'friday' ? 'الجمعة' : 'السبت'))))))
                : ucfirst($day) }} 
            {{ app()->getLocale() === 'ar' ? 'من الساعة' : 'from' }} :
            <input type="text" name="{{ $day }}_from" value="{{ old($day.'_from') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 09:00' : 'e.g., 09:00' }}">
            {{ app()->getLocale() === 'ar' ? 'الى الساعة' : 'to' }} :
            <input type="text" name="{{ $day }}_to" value="{{ old($day.'_to') }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 17:00' : 'e.g., 17:00' }}">
        </label><br>
    @endforeach
</div>

    
         <!-- Status (Radio Buttons) -->
<div class="form-group">
    <label for="status">
        {{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}
    </label>
    <div class="status-radio">
        <label>
            <input type="radio" name="status" value="1" {{ old('status') === 1 ? 'checked' : '' }}>
            {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
        </label>
        <label>
            <input type="radio" name="status" value="0" {{ old('status') === 0 ? 'checked' : '' }}>
            {{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}
        </label>
    </div>
</div>
    
            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn btn-primary button_dash">
                    {{ app()->getLocale() === 'ar' ? 'إنشاء المتجر' : 'Create Store' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('sub-js')

@endsection
