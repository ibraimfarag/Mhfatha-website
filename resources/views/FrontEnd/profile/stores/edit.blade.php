@extends('FrontEnd.profile.layout.master')
@section('dash-content')
<div class="container">
    <a href="{{ url()->previous() }}" class="btn btn-secondary button_dash float-left mt-2">
        {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }} <i class="fa-solid fa-arrow-left"></i> 
    </a>
    <form action="{{ route('stores.update', ['store' => $store->id, 'lang' => app()->getLocale()]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
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

        <div class="row">
            <div class="col-4">
                <h3>{{ app()->getLocale() === 'ar' ? 'تحرير المتجر' : 'Edit Store' }}</h3>
                
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                <!-- Store Name -->
                <div class="form-group">
                    <div class="name-group">

                    <label for="name">{{ app()->getLocale() === 'ar' ? 'اسم المتجر' : 'Store Name' }}</label>
                    <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $store->name) }}">
                    </div>
                </div>

                <!-- Location -->
                <div class="form-group">
                    <div class="name-group">
                    <label for="location">{{ app()->getLocale() === 'ar' ? 'العنوان' : 'Location' }}</label>
                    <input type="text" name="location" id="location" class="form-control" value="{{ old('location', $store->location) }}">
                    </div>
                </div>

            

                <!-- Phone -->
                    <div class="form-group">
                        <div class="name-group">
                        <label for="phone">{{ app()->getLocale() === 'ar' ? 'هاتف المتجر' : 'Phone' }}</label>
                        <input type="text" name="phone" id="phone" class="form-control" value="{{ old('phone', $store->phone) }}">
                        </div>
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
            <div class="col-3">

                <img id="image-preview" src="{{ asset('FrontEnd/assets/images/store_images/' . $store->photo) }}" alt="{{ $store->name }}" class="img-thumbnail image-preview" >


            </div>
            {{-- /* ------------------------------ Bann section ------------------------------ */ --}}
@if (Auth::user()->is_admin)
<div class="col-4">
    <h3>{{ app()->getLocale() === 'ar' ? 'تحرير الحظر' : 'Edit Bann' }}</h3>

    <!-- Bann Status (Radio Buttons) -->
    <div class="form-group">
        <label for="is_bann">{{ app()->getLocale() === 'ar' ? 'حالة الحظر' : 'Bann Status' }}</label>
        <div class="status-radio">
            <label>
                <input type="radio" name="is_bann" value="1" {{ old('is_bann', $store->is_bann) == 1 ? 'checked' : '' }}>
                {{ app()->getLocale() === 'ar' ? 'محظور' : 'Banned' }}
            </label>
            <label>
                <input type="radio" name="is_bann" value="0" {{ old('is_bann', $store->is_bann) == 0 ? 'checked' : '' }}>
                {{ app()->getLocale() === 'ar' ? 'غير محظور' : 'Not Banned' }}
            </label>
        </div>
    </div>

    <!-- Ban Message (Text Area) -->
    <div class="form-group">
        <label for="bann_msg">{{ app()->getLocale() === 'ar' ? 'رسالة الحظر' : 'Ban Message' }}</label>
        <textarea name="bann_msg" id="bann_msg" class="form-control">{{ old('bann_msg', $store->bann_msg) }}</textarea>
    </div>
</div>
@endif
</div>
        </div>
    
<!-- Work Days (Optional) -->
<div class="form-group">
    <label>{{ app()->getLocale() === 'ar' ? 'أيام العمل' : 'Work Days' }}</label><br>

    @php
    // Get the selected work days from the controller
    $workDays = json_decode($store->work_days, true); // Convert to an array
    @endphp

    @foreach(['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'] as $day)
        @php
        $dayInfo = $workDays[$day] ?? ['from' => '', 'to' => ''];
        @endphp

        <label for="{{ $day }}">
            <input type="checkbox" id="{{ $day }}" name="work_days[]" value="{{ $day }}" {{ !empty($dayInfo['from']) || !empty($dayInfo['to']) ? 'checked' : '' }} onchange="toggleTimeInput(this)">
            {{ app()->getLocale() === 'ar' ? ($day === 'sunday' ? 'الأحد' : ($day === 'monday' ? 'الإثنين' : ($day === 'tuesday' ? 'الثلاثاء' : ($day === 'wednesday' ? 'الأربعاء' : ($day === 'thursday' ? 'الخميس' : ($day === 'friday' ? 'الجمعة' : 'السبت')))))) : ucfirst($day) }} 
            {{ app()->getLocale() === 'ar' ? 'من الساعة' : 'from' }} :
            <input type="text" name="{{ $day }}_from" value="{{ old($day.'_from', $dayInfo['from']) }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 09:00' : 'e.g., 09:00' }}"{{ empty($dayInfo['from']) ? 'disabled' : '' }}>
            {{ app()->getLocale() === 'ar' ? 'الى الساعة' : 'to' }} :
            <input type="text" name="{{ $day }}_to" value="{{ old($day.'_to', $dayInfo['to']) }}" placeholder="{{ app()->getLocale() === 'ar' ? 'مثال: 17:00' : 'e.g., 17:00' }}"{{ empty($dayInfo['to']) ? 'disabled' : '' }}>
        </label><br>
    @endforeach
</div>

        <!-- Status (Radio Buttons) -->
        <div class="form-group">
            <label for="status">{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</label>
            <div class="status-radio">
                <label>
                    <input type="radio" name="status" value="1" {{ old('status', $store->status) == 1 ? 'checked' : '' }}>
                    {{ app()->getLocale() === 'ar' ? 'نشط' : 'Active' }}
                </label>
                <label>
                    <input type="radio" name="status" value="0" {{ old('status', $store->status) == 0 ? 'checked' : '' }}>
                    {{ app()->getLocale() === 'ar' ? 'غير نشط' : 'Inactive' }}
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit" class="btn btn-primary button_dash">
                {{ app()->getLocale() === 'ar' ? 'حفظ التغييرات' : 'Save Changes' }}
            </button>
        </div>
    </form>
</div>
@endsection
@section('sub-js')

<script>
    function toggleTimeInput(checkbox) {
        var inputFrom = checkbox.nextSibling.nextSibling;
        var inputTo = inputFrom.nextSibling.nextSibling;
    
        if (!checkbox.checked) {
            inputFrom.disabled = true;
            inputTo.disabled = true;
        } else {
            inputFrom.disabled = false;
            inputTo.disabled = false;
        }
    }
    </script>

  
      
@endsection