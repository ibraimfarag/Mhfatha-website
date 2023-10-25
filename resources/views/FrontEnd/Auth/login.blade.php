@extends('FrontEnd.layouts.master')
@section('title', app()->getLocale() === 'ar' ? '- تسجيل الدخول ' : '- login')
@section('content')


    <!-- ======= Login Section ======= -->
    <section id="login" class="login login-background ">
        <div class="container" data-aos="fade-up">
  
       
            {{-- <h1>{{ app()->getLocale() === 'ar' ? 'مرحبًا بك في موقعنا' : 'Welcome to our website' }}</h1> --}}
  
  
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
            <div class="col-lg-5">
              {{-- <div class="login-img">
                <img src="{{ asset('/FrontEnd/assets/images/auth/login.png') }}" class="img-fluid grad" alt="">
              </div> --}}
                  
              <div class="form">
                <div class="title">{{ app()->getLocale() === 'ar' ? 'تسجيل الدخول' : 'Welcome to our website' }}</div>
                {{-- <div class="subtitle">Let's create your account!</div> --}}
                <div class="input-container ic1">
                  <input id="firstname" class="input" type="text" placeholder=" " />
                  <div class="cut"></div>
                  <label for="firstname" class="placeholder">الاسم</label>
                </div>
                <div class="input-container ic2">
                  <input id="lastname" class="input" type="text" placeholder=" " />
                  <div class="cut"></div>
                  <label for="lastname" class="placeholder">كلمة السر</label>
                </div>
              
                <button type="text" class="submit">submit</button>
              </div>

            </div>
            <div class="col-lg-7 section-titles">
                <h5 class="pt-0 pt-lg-5">نحن نسعى إلى توفير منصة تقنية موثوقة تربط التجار بجمهورهم بأسلوب سهل وفعال. نحن ملتزمون بالاستدامة والامتثال للقوانين واللوائح السارية في المملكة العربية السعودية.</h5>
                <p class="gray">فريق "محفظة" يعمل بجدية لتقديم خدمة استثنائية لجميع مستخدمينا وشركائنا التجار. نحن نؤمن بأن التكنولوجيا يمكن أن تسهم في تطوير وتيسير العمليات التجارية وتحسين تجربة التسوق عبر الإنترنت.</p>
            

              
            </div>
           
          </div>
  
        </div>
      </section>
      <!-- End Login Section -->

     
  @endsection
@section('js')

@endsection