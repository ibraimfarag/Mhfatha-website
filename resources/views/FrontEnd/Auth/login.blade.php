@extends('FrontEnd.layouts.master')

  @section('content')


    <!-- ======= About Section ======= -->
    <section id="login" class="about about-background ">
        <div class="container" data-aos="fade-up">
  
       
            {{-- <h1>{{ app()->getLocale() === 'ar' ? 'مرحبًا بك في موقعنا' : 'Welcome to our website' }}</h1> --}}
  
  
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
            <div class="col-lg-5">
              <div class="about-img">
                <img src="{{ asset('/FrontEnd/assets/images/about/about-us.png') }}" class="img-fluid grad" alt="">
              </div>
            </div>
  
            <div class="col-lg-7 section-titles">
              <h5 class="pt-0 pt-lg-5">نحن نسعى إلى توفير منصة تقنية موثوقة تربط التجار بجمهورهم بأسلوب سهل وفعال. نحن ملتزمون بالاستدامة والامتثال للقوانين واللوائح السارية في المملكة العربية السعودية.
  
              </h5>
              <p class="gray">فريق "محفظة" يعمل بجدية لتقديم خدمة استثنائية لجميع مستخدمينا وشركائنا التجار. نحن نؤمن بأن التكنولوجيا يمكن أن تسهم في تطوير وتيسير العمليات التجارية وتحسين تجربة التسوق عبر الإنترنت.
  
              </p>
  
             
  
            </div>
  
          </div>
  
        </div>
      </section>
      <!-- End About Section -->


  @endsection