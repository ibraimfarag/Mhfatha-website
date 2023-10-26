@extends('FrontEnd.layouts.master')
@section('title', app()->getLocale() === 'ar' ? '- لوحة التحكم ' : '- Dashboard')
@section('content')


    <!-- ======= dashbord Section ======= -->
    <section id="login" class="login login-background ">
        <div class="container" data-aos="fade-up">
  
       
            {{-- <h1>{{ app()->getLocale() === 'ar' ? 'مرحبًا بك في موقعنا' : 'Welcome to our website' }}</h1> --}}
  
  
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
      

                <div class="register-form">
                      <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                      
                      <div class="row">
                        <!-- Right Sidebar -->
<div class="col-md-2">
  <div class="sidebar">

    <nav id="navbar" class="navbar user_sideBar">
        
      
        <ul class="user_sideBar">

          <li><a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadProfile()">{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}</a></li>
          <li><a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadPreviousDiscounts()">{{ app()->getLocale() === 'ar' ? 'الخصومات السابقة' : 'Previous Discounts' }}</a></li>
          <li><a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadNearbyStores()">{{ app()->getLocale() === 'ar' ? 'المتاجر القريبة' : 'Nearby Stores' }}</a></li>
          <li><a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadManageStores()">{{ app()->getLocale() === 'ar' ? 'ادارة المتاجر' : 'Manage Stores' }}</a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle d-none"></i>
      </nav><!-- .navbar -->


      
</div>

</div>
                    <!-- Left Panel -->
<div class="col-md-10" id="left-panel">
  <!-- Initial content for the left panel -->
</div>



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
  function loadProfile() {
    $('#left-panel').load('/profile'); // Load the profile view
}

function loadPreviousDiscounts() {
    $('#left-panel').load('/previous_discounts'); // Load the previous discounts view
}

function loadNearbyStores() {
    $('#left-panel').load('/nearby_stores'); // Load the nearby stores view
}

function loadManageStores() {
    $('#left-panel').load('/manage_stores'); // Load the manage stores view
}

</script>
@endsection