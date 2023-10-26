@extends('FrontEnd.layouts.master')
@section('title', app()->getLocale() === 'ar' ? '- التسجيل ' : '- Register')
@section('content')


    <!-- ======= register Section ======= -->
    <section id="register" class="register register-background ">
        <div class="container" data-aos="fade-up">
  
       
            {{-- <h1>{{ app()->getLocale() === 'ar' ? 'مرحبًا بك في موقعنا' : 'Welcome to our website' }}</h1> --}}
  
  
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
            <div class="col-lg-5">
              {{-- <div class="register-img">
                <img src="{{ asset('/FrontEnd/assets/images/auth/register.png') }}" class="img-fluid grad" alt="">
              </div> --}}
                  
             



              <div class="register-form">
                <form action="{{ route('register_post') }}" method="post">
                    @csrf
                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                    <div class="form-group">
                        <div class="name-group">
                            <label for="first_name">{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }}</label>
                            <input id="firstName"  name="first_name" placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم الأول' : 'First Name' }}" />
                            {{-- @error('first_name') <!-- Check if there's an error for the first_name field -->
                            <span class="text-danger">{{ $message }}</span> <!-- Display the error message -->
                        @enderror --}}
                        </div>
                        <div class="name-group">
                            <label for="middleName">{{ app()->getLocale() === 'ar' ? 'الاسم الأوسط' : 'Middle Name' }}</label>
                            <input id="middleName" name="middle_name" type="text" placeholder="{{ app()->getLocale() === 'ar' ? 'الاسم الأوسط' : 'Middle Name' }}" />
                        </div>
                        <div class="name-group">
                            <label for="lastName">{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Family Name' }}</label>
                            <input id="lastName" name="last_name" type="text" placeholder="{{ app()->getLocale() === 'ar' ? 'اسم العائلة' : 'Family Name' }}" />
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="gender">{{ app()->getLocale() === 'ar' ? 'الجنس' : 'sex' }}</label>
                        <input type="radio" id="male" name="gender" value="male">
                        <label for="male">{{ app()->getLocale() === 'ar' ? 'ذكر' : 'Male' }}</label>
                        <input type="radio" id="female" name="gender" value="female">
                        <label for="female">{{ app()->getLocale() === 'ar' ? 'انثى' : 'Female' }}</label>
                    </div>
                    
                    <div class="form-group">
                        <label for="birthday">{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'birthday' }}</label>
                        <input type="date" id="male" name="birthday" >
                      
                    </div>
                    
                    <div class="form-group">
                    <div class="name-group">
                        <label for="city">{{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Region' }}:</label>
                        <select id="city" name="city">
                           
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
                            
                    </div>
                    <div class="name-group">
                        <label for="region">{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}:</label>
                        <select id="region" name="region">
                            <!-- Regions will be dynamically populated based on the selected city -->
                        </select>
                    </div>
                    </div>
                    <div class="form-group">
                        <div class="name-group">
                            <label for="phoneNumber">{{ app()->getLocale() === 'ar' ? 'رقم الجوال' : 'Phone Number' }}</label>
                         
                            <input id="phoneNumber" name="mobile" type="tel" style=" direction:ltr;" placeholder=" {{ app()->getLocale() === 'ar' ? 'ادخل رقم الجوال ' : 'Phone Number' }}" value="" pattern='^(1-)?[0-9]{3}-[0-9]{3}-[0-9]{4}' oninput='formatPhoneNum(this)' />
                         
                        </div>
                        
                        
                    <div class="name-group">
                        <label for="email">{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</label>
                        <input id="email" name="email" type="email" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}" />
                    </div>
                    </div>
                    <div class="form-group">
                    <div class="name-group">
                        <label for="password">{{ app()->getLocale() === 'ar' ? 'كلمة السر' : 'Password' }}</label>
                        <input id="password"  name="password" type="password" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة السر' : 'Password' }}" />
                    </div>
                    <div class="name-group">
                        <label for="confirmPassword">{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة السر' : 'Confirm Password' }}</label>
                        <input id="confirmPassword" name="password_confirmation"  type="password" placeholder="{{ app()->getLocale() === 'ar' ? 'تأكيد كلمة السر' : 'Confirm Password' }}" />
                    </div>
                    </div>
                    <div class="form-group">
                        <label for="userType">{{ app()->getLocale() === 'ar' ? 'تسجيل كـ' : 'Register As' }}</label>
                        <select id="userType" name="is_vendor">
                            <option value="1">{{ app()->getLocale() === 'ar' ? 'تاجر' : 'Merchant' }}</option>
                            <option value="0">{{ app()->getLocale() === 'ar' ? 'مستخدم' : 'User' }}</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <button type="submit">{{ app()->getLocale() === 'ar' ? 'تسجيل' : 'Register' }}</button>
                    </div>
                </form>          </div>
                @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
            </div>
            <div class="col-lg-7 section-titles">
                <h5 class="pt-0 pt-lg-5">نحن نسعى إلى توفير منصة تقنية موثوقة تربط التجار بجمهورهم بأسلوب سهل وفعال. نحن ملتزمون بالاستدامة والامتثال للقوانين واللوائح السارية في المملكة العربية السعودية.</h5>
                <p class="gray">فريق "محفظة" يعمل بجدية لتقديم خدمة استثنائية لجميع مستخدمينا وشركائنا التجار. نحن نؤمن بأن التكنولوجيا يمكن أن تسهم في تطوير وتيسير العمليات التجارية وتحسين تجربة التسوق عبر الإنترنت.</p>
            

              
            </div>
           
          </div>
  
        </div>
      </section>
      <!-- End register Section -->

     
  @endsection
@section('js')
<script>
    // Define city-to-region mapping
    const cityToRegions = {
    "riyadh": ["الرياض", "الدرعية", "الخرج", "الدوادمي", "المجمعة", "القويعية", "الأفلاج", "وادي الدواسر", "الزلفي", "شقراء", "حوطة بني تميم", "عفيف", "الغاط", "السليل", "ضرما", "المزاحمية", "رماح", "ثادق", "حريملاء", "الحريق", "مرات", "الرين", "الدلم", "Another City in Riyadh"],
    "makkah": ["مكة المكرمة","جدة","الطائف","القنفذة","الليث","رابغ","خليص","الخرمة","رنية","تربة","الجموم","الكامل","المويه","ميسان","أضم","العرضيات","بحرة","Another City in Makkah Al-Mukarramah"] ,
    "madinah": ["المدينة المنورة","ينبع","العلا","المهد","الحناكية","بدر","خيبر","العيص","وادي الفرع"]  , 
    "qassim": ["بريدة","عنيزة","الرس","المذنب","البكيرية","البدائع","الأسياح","النبهانية","الشماسية","عيون الجواء","رياض الخبراء","عقلة الصقور","ضرية","Another City in Qassim"],
    "eastern": ["الدمام","الأحساء","حفر الباطن","الجبيل","القطيف","الخبر","الخفجي","رأس تنورة","بقيق","النعيرية","قرية العليا","العديد","البيضاء","Another City in Eastern Province"],    
    "asir": ["أبها","خميس مشيط","بيشة","النماص","محايل عسير","ظهران الجنوب","تثليث","سراة عبيدة","رجال ألمع","بلقرن","أحد رفيدة","المجاردة","البرك","بارق","تنومة","طريب","Another City in Asir"],    
    "tabuk": ["تبوك", "الوجه","ضباء","تيماء","أملج","حقل","البدع"],
    "northern": ["عرعر", "رفحاء","طريف","العويقيلة"],
    "hail": ["حائل","بقعاء","الغزالة","الشنان","الحائط","السليمي","الشملي","موقق","سميراء"],
    "jazan": ["جازان","صبيا","أبو عريش","صامطة","بيش","الدرب","الحرث","ضمد","الريث","فرسان","الدائر","العارضة","أحد المسارحة","العيدابي","فيفاء","الطوال","هروب"],  
    "najran": ["نجران","شرورة","حبونا","بدر الجنوب","يدمة","ثار","خباش"],
    "bahah": ["الباحة","بلجرشي","المندق","المخواة","قلوة","العقيق","القرى","غامد الزناد","الحجرة","بني حسن"],    
    "aljouf": ["سكاكا", "القريات","دومة الجندل","طبرجل"],

    // Continue with the remaining regions and cities
                };

    // Get references to the city and region select elements
    const citySelect = document.getElementById("city");
    const regionSelect = document.getElementById("region");

    // Function to update region options based on the selected city
    function updateRegions() {
        const selectedCity = citySelect.value;
        const regions = cityToRegions[selectedCity] || [];

        // Clear existing region options
        regionSelect.innerHTML = "";

        // Add new region options
        regions.forEach((region) => {
            const option = document.createElement("option");
            option.value = region;
            option.textContent = region;
            regionSelect.appendChild(option);
        });
    }

    // Add an event listener to update regions when the city selection changes
    citySelect.addEventListener("change", updateRegions);

    // Initial update based on the default city selection
    updateRegions();
</script>

<script>
const formatPhoneNum = (inputField) => {
    const nums = inputField.value.split('-').join("");
    const countryCode = '+966';
    const digits = nums[0] === countryCode ? 1 : 0;

    // get character position of the cursor:
    let cursorPosition = inputField.selectionStart;

    // add dashes (format 1-xxx-xxx-xxxx or xxx-xxx-xxxx):
    if (nums.length > digits+10) {
        inputField.value = `${digits === 1 ? nums.slice(0, digits) + '-' : ""}` + nums.slice(digits,digits+3) + '-' + nums.slice(digits+3,digits+6) + '-' + nums.slice(digits+6,digits+10);
    }
    else if (nums.length > digits+6) {
        inputField.value = `${digits === 1 ? nums.slice(0, digits) + '-' : ""}` + nums.slice(digits,digits+3) + '-' + nums.slice(digits+3,digits+6) + '-' + nums.slice(digits+6,nums.length);
    }
    else if (nums.length > digits+5) {
        inputField.value = `${digits === 1 ? nums.slice(0, digits) + '-' : ""}` + nums.slice(digits,digits+3) + '-' + nums.slice(digits+3,nums.length);
    }
    else if (nums.length > digits+3) {
        inputField.value = `${digits === 1 ? nums.slice(0, digits) + '-' : ""}` + nums.slice(digits, digits+3) + '-' + nums.slice(digits+3, nums.length);
    }
    else if (nums.length > 1 && digits === 1) {
        inputField.value = nums.slice(0,digits) + '-' + nums.slice(digits, nums.length);
    }

    // reseting the input value automatically puts the cursor at the end, which is annoying,
    // so reset the cursor back to where it was before, taking into account any dashes that we added...
    // if the character 1 space behind the cursor is a dash, then move the cursor up one character:
    if (inputField.value.slice(cursorPosition-1, cursorPosition) === '-') {
        cursorPosition++;
    }
    
    inputField.selectionStart = cursorPosition;
    inputField.selectionEnd = cursorPosition;
}
</script>


@endsection