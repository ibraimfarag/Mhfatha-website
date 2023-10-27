@extends('FrontEnd.layouts.master')
@section('title', app()->getLocale() === 'ar' ? '- لوحة التحكم ' : '- Dashboard')
@section('content')


    <!-- ======= dashbord Section ======= -->
    <section id="dashboard_user" class="userdash login-background ">
        <div class="container-fluid" data-aos="fade-up">
  
       
 
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
      

                <div class="userdash-form">
                      <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                      
                      @include('FrontEnd.profile.layout.sidebar')

                    <!-- Left Panel -->
<div class="col-md-10" id="left-panel">
      <div class="profile-info">


@yield('dash-content')



    
    </div></div>



                    </div>
                       
                </div>    
                      



            </div>
          
           
          </div>
  
        </div>
      </section>
      <!-- End Login Section -->

     
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
    // JavaScript to update the image preview
    document.getElementById('profile_image').addEventListener('change', function() {
        const fileInput = this;
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    });

</script>

@endsection