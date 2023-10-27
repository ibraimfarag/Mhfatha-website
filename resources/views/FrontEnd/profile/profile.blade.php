



            @extends('FrontEnd.profile.layout.master')
            @section('dash-content')
            
            <form method="POST" action="{{ route('profile-update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

            <div class="row" id="prfile">
                <div class="container">
                    <div class="row">
                       
                        <div class="col-md-6 offset-md-2">
                            <div class="card update-user">
                                <div class="card-header">{{ __('Edit Profile') }}</div>
                
                                <div class="card-body">
                                       
                
                                        <div class="form-group">
                                            <div class="name-group">
                                                <label for="first_name">
                                                    {{ app()->getLocale() === 'ar' ? 'الاسم الاول' : 'First Name' }}
                                                </label>
                                                <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}" required autofocus>
                                                @error('first_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            
                                            <div class="name-group">
                                                <label for="middle_name">
                                                    {{ app()->getLocale() === 'ar' ? 'اسم الاب' : 'Middle Name' }}
                                                </label>
                                                <input id="middle_name" type="text" class="form-control" name="middle_name" value="{{ old('middle_name', Auth::user()->middle_name) }}">
                                            </div>
                                            
                                            <div class="name-group">
                                                <label for="last_name">
                                                    {{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Last Name' }}
                                                </label>
                                                <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                                @error('last_name')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Gender -->
                                        <div class="form-group">
                                            <div class="name-group">
                                                <label for="birthday">
                                                    {{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Birthday' }}
                                                </label>
                                                <input id="birthday" type="date" class="form-control @error('birthday') is-invalid @enderror" name="birthday" value="{{ old('birthday', Auth::user()->birthday) }}" required>
                                                @error('birthday')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        
                                            <div class="name-group">
                                                <label for="gender">
                                                    {{ app()->getLocale() === 'ar' ? 'الجنس' : 'Gender' }}
                                                </label>
                                                <select id="gender" class="form-control" name="gender">
                                                    <option value="male" {{ old('gender', Auth::user()->gender) === 'male' ? 'selected' : '' }}>
                                                        {{ app()->getLocale() === 'ar' ? 'ذكر' : 'Male' }}
                                                    </option>
                                                    <option value="female" {{ old('gender', Auth::user()->gender) === 'female' ? 'selected' : '' }}>
                                                        {{ app()->getLocale() === 'ar' ? 'أنثى' : 'Female' }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <!-- City -->
                                        <div class="form-group">
                                            <div class="name-group">
                                                <label for="city">
                                                    {{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}
                                                </label>
                                                <select id="city" class="form-control @error('city') is-invalid @enderror" name="city" value="{{ old('city', Auth::user()->city) }}" required>
                           
                                                    <option value="riyadh">{{ app()->getLocale() === 'ar' ? 'الرياض' : 'Riyadh' }}</option>
                                                    <option value="makkah">{{ app()->getLocale() === 'ar' ? 'مكة المكرمة' : 'Makkah Al-Mukarramah' }}</option>
                                                    <option value="madinah">{{ app()->getLocale() === 'ar' ? 'المدينة المنورة' : 'Al-Madinah Al-Munawwarah' }}</option>
                                                    <option value="eastern">{{ app()->getLocale() === 'ar' ? 'المنطقة الشرقية' : 'Eastern Province' }}</option>
                                                    <option value="qassim">{{ app()->getLocale() === 'ar' ? 'القصيم' : 'Qassim' }}</option>
                                                    <option value="tabuk">{{ app()->getLocale() === 'ar' ? 'تبوك' : 'Tabuk' }}</option>
                                                    <option value="northern">{{ app()->getLocale() === 'ar' ? 'الحدود الشمالية' : 'Northern Borders' }}</option>
                                                    <option value="jazan">{{ app()->getLocale() === 'ar' ? 'جازان' : 'Jazan' }}</option>
                                                    <option value="hail">{{ app()->getLocale() === 'ar' ? 'حائل' : 'Hail' }}</option>
                                                    <option value="asir">{{ app()->getLocale() === 'ar' ? 'عسير' : 'Asir' }}</option>
                                                    <option value="aljouf">{{ app()->getLocale() === 'ar' ? 'الجوف' : 'Al-Jouf' }}</option>
                                                    <option value="najran">{{ app()->getLocale() === 'ar' ? 'نجران' : 'Najran' }}</option>
                                                    <option value="bahah">{{ app()->getLocale() === 'ar' ? 'الباحة' : 'Al Bahah' }}</option>
                                               
                                                    <!-- Continue with the remaining regions and cities -->
                                                </select>
                                                @error('city')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        
                                            <div class="name-group">
                                                <label for="region">
                                                    {{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Region' }}
                                                </label>
                                                <select id="region" class="form-control @error('region') is-invalid @enderror" name="region" value="{{ old('region', Auth::user()->region) }}" required>
                                                    <!-- Regions will be dynamically populated based on the selected city -->
                                                </select>
                                                @error('region')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <!-- Mobile -->
                                        <div class="form-group">
                                            <div class="name-group">
                                                <label for="mobile">
                                                    {{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Mobile' }}
                                                </label>
                                                <input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile" value="{{ old('mobile', Auth::user()->mobile) }}" required>
                                                @error('mobile')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                    
                                        
                                        <!-- Email -->
                                       
                                            <div class="name-group">
                                                <label for="email">
                                                    {{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}
                                                </label>
                                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                                @error('email')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary button_dash">
                                                {{ app()->getLocale() === 'ar' ? 'تحديث ' : 'Update ' }}
                                            </button>
                                        </div>                                    
                                </div>
                            </div>
                        </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <img id="image-preview" src="{{ asset('FrontEnd/assets/images/user_images/' . Auth::user()->photo) }}" alt="{{ Auth::user()->name }}" class="img-thumbnail image-preview" >
                                        </div>
                                        <div class="form-group">

                                            <div class="name-group">
                                                <label for="profile_image">
                                                    {{ app()->getLocale() === 'ar' ? 'صورة الملف الشخصي' : 'Profile Image' }}
                                                </label>
                                                <input id="profile_image" type="file" class="form-control @error('profile_image') is-invalid @enderror" name="profile_image">
                                                @error('profile_image')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                    </div>
                    </div>
                </div>
                </div>
            </form>
            
            
            @endsection