@extends('FrontEnd.layouts.master')

  @section('content')

  <!-- ======= Hero Section ======= -->
  <section id="hero" class="hero carousel  carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">

    <div class="carousel-inner">
      <div class="carousel-item active">
        <div class="container-fluid">
          <div class="row justify-content-center gy-12 ">

      
            <div class="col-xl-6 text-center col-xl-6.text-center">
            
              <img src="{{ asset('/FrontEnd/assets/images/slider/slider-text.png') }}" alt="" class="right-img">


              <div class="images-container">
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/googleplay.png') }}" height="50" alt="Google Play">  </a>
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/appstore.png') }}" height="50" alt="App Store"></a>
              </div>
            </div>

             <div class="col-xl-6 col-md-8">
              <img src="{{ asset('/FrontEnd/assets/images/slider/vector1.png') }}" alt="" class="slider-img grad">
             </div>

          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="container-fluid">
          <div class="row justify-content-center gy-12 ">

      
            <div class="col-xl-6 text-center col-xl-6.text-center">
            
              <img src="{{ asset('/FrontEnd/assets/images/slider/slider-text.png') }}" alt="" class="right-img">


              <div class="images-container">
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/googleplay.png') }}" height="50" alt="Google Play">  </a>
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/appstore.png') }}" height="50" alt="App Store"></a>
              </div>
            </div>

             <div class="col-xl-6 col-md-8">
              <img src="{{ asset('/FrontEnd/assets/images/slider/vector2.png') }}" alt="" class="slider-img grad">
             </div>

          </div>
        </div>
      </div>
      <div class="carousel-item ">
        <div class="container-fluid">
          <div class="row justify-content-center gy-12 ">

      
            <div class="col-xl-6 text-center col-xl-6.text-center">
            
              <img src="{{ asset('/FrontEnd/assets/images/slider/slider-text.png') }}" alt="" class="right-img">


              <div class="images-container">
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/googleplay.png') }}" height="50" alt="Google Play">  </a>
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/appstore.png') }}" height="50" alt="App Store"></a>
              </div>
            </div>

             <div class="col-xl-6 col-md-8">
              <img src="{{ asset('/FrontEnd/assets/images/slider/vector3.png') }}" alt="" class="slider-img grad">
             </div>

          </div>
        </div>
      </div>


      <div class="carousel-item  ">
        <div class="container-fluid">
          <div class="row justify-content-center gy-12 ">

      
            <div class="col-xl-6 text-center col-xl-6.text-center">
            
              <img src="{{ asset('/FrontEnd/assets/images/slider/slider-text.png') }}" alt="" class="right-img">


              <div class="images-container">
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/googleplay.png') }}" height="50" alt="Google Play">  </a>
                <a href="#">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/appstore.png') }}" height="50" alt="App Store"></a>
              </div>
            </div>

             <div class="col-xl-6 col-md-8">
              <img src="{{ asset('/FrontEnd/assets/images/slider/vector4.png') }}" alt="" class="slider-img grad ">
             </div>

          </div>
        </div>
      </div>

      
      <!-- End Carousel Item -->




    </div>

    <a class="carousel-control-prev" href="#hero" role="button" data-bs-slide="prev">
      <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
    </a>

    <a class="carousel-control-next" href="#hero" role="button" data-bs-slide="next">
      <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
    </a>

    <ol class="carousel-indicators"></ol>

<!-- ----------------------------------------------------------------------- -->
<!-- ----------------------------------------------------------------------- -->







  </section>
  <!-- End Hero Section -->

    <!-- ======= About Section ======= -->
    <section id="about" class="about about-background ">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>من نحن </h2>
          <p>مؤسسة "محفظة" هي مؤسسة سعودية متخصصة في مجال توفير وسيلة للتجار لعرض ونشر الخصومات والعروض للمستخدمين بطريقة مبتكرة وملائمة. نحن نعمل بشغف لتقديم تجربة تسوق مميزة وميسرة للمستخدمين في المملكة العربية السعودية.

          </p>
          {{-- <h1>{{ app()->getLocale() === 'ar' ? 'مرحبًا بك في موقعنا' : 'Welcome to our website' }}</h1> --}}


        </div>

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
    <!-- Meza Section -->
    <section id="meza" class="about">
      <div class="container" data-aos="fade-up">

        <div class="section-header">
          <h2>مميزاتنا  </h2>
         
  
        <p>منصة مبتكرة وفعالة نقدم منصة تقنية متقدمة تمكن التجار من عرض الخصومات بشكل سهل وفعال، مما يسهم في زيادة مبيعاتهم.</p>
        </div>

        <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">

          <div class="col-lg-5">
            <div class="about-img">
              <img src="{{ asset('/FrontEnd/assets/images/meza/meza2.png') }}" class="img-fluid grad" alt="">
            </div>
          </div>

          <div class="col-lg-7 section-titles">
            <h5 class="pt-0 pt-lg-5">نحن نسعى إلى توفير مميزات  تقنية موثوقة تربط التجار بجمهورهم بأسلوب سهل وفعال.  </h5>
<!-- Tabs -->
<ul class="nav nav-pills mb-3">
  <li class="nav-item">
    <a class="nav-link active" data-bs-toggle="pill" href="#tab1">العميل</a>
  </li>
  <li class="nav-item">
    <a class="nav-link mr-3" data-bs-toggle="pill" href="#tab2">التاجر</a>
  </li>
</ul><!-- End Tabs -->

            <!-- Tab Content -->
            <div class="tab-content">

              <div class="tab-pane fade show active" id="tab1">


                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">العثور على الصفقات بسهولة: يمكن للمستخدمين العثور بسهولة على أفضل الخصومات والعروض من التجار المحليين.
                  </h6>
                </div>
              
                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">توفير الوقت والجهد: تطبيق "محفظة" يجعل عملية البحث عن الخصومات والعروض أسهل وأكثر فاعلية، مما يوفر وقتهم وجهدهم.
                  </h6>                </div>

                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">تجربة تسوق مميزة: المستخدمون يمكنهم الاستمتاع بتجربة تسوق فريدة واقتصادية من خلال استفادتهم من الخصومات والعروض المقدمة عبر التطبيق.
                  </h6>                </div>

              </div><!-- End Tab 1 Content -->

              <div class="tab-pane fade show" id="tab2">


                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">زيادة العملاء: يمكن للتاجر الوصول إلى شريحة واسعة من العملاء المحتملين وزيادة عدد العملاء من خلال عرض الخصومات.

                  </h6>                 </div>


                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">تحسين الجاذبية: يمكن للتاجر تعزيز جاذبية منتجاته وخدماته من خلال عروض مغرية وخصومات مجزية.
                  </h6>                 </div>

                <div class="d-flex align-items-center mt-4">
                  <i class="bi bi-check2"></i>
                  <h6 class="gray">تعزيز السمعة: التعامل مع منصة موثوقة ومحترفة مثل "محفظة" يمكن أن يساعد في تعزيز سمعة العلامة التجارية.


                  </h6>                 </div>

              </div><!-- End Tab 2 Content -->

            

            </div>

          </div>

        </div>

      </div>
    </section><!-- End About Section -->


    <!-- ======= Call To Action Section ======= -->
    <section id="app" class="cta">
      <div class="container" data-aos="zoom-out">

        <div class="row g-5">

          <div class="col-lg-8 col-md-6 content d-flex flex-column justify-content-center order-last order-md-first">
            <h2 class="white">
              تحميل التطبيق
            </h2>
            <p class="gray"> للاستفادة من خصوماتنا والعروض الحصرية، يُرجى تنزيل تطبيق "محفظة" على جهازك الذكي. استخدم الروابط أدناه لتحميل التطبيق على نظام التشغيل الخاص بك:

            </p>
           
            <div class="images-container">
              <a href="https://play.google.com/store/apps/details?id=com.app.mhfatha&pcampaignid=web_share">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/googleplay.png') }}" height="50" alt="Google Play">  </a>
              <a href="https://apps.apple.com/us/app/mhfatha/id6475014076">  <img src="{{ asset('/FrontEnd/assets/images/downloadButton/appstore.png') }}" height="50" alt="App Store"></a>
            </div>
          </div>

          <div class="col-lg-4 col-md-6 order-first order-md-last d-flex align-items-center m0">
            <div class="img">
              <img src="{{ asset('/FrontEnd/assets/images/app/preview.gif') }}" alt="" class="img-fluid">
            </div>
          </div>

        </div>

      </div>
    </section><!-- End Call To Action Section -->



   
    <!-- ======= Contact Section ======= -->
    <section id="contact" class="contact">
      <div class="container">

        <div class="section-header">
          <h2>اتصل بنا</h2>
          
        </div>

      </div>

      <div class="container">

        <div class="row gy-5 gx-lg-5">

          <div class="col-lg-4">

            <div class="info">
              <h3 class="white ">ابقي على اتصال</h3>
              <p>في "محفظة"، نحن نقدر تواصلك معنا ونرحب بأية استفسارات أو ملاحظات قد تكون لديك. يمكنك التواصل مع فريق الدعم لدينا 

              </p>
              <div class="info-item d-flex">
                <i class="bi bi-geo-alt flex-shrink-0"></i>
                <div>
                  <h4>العنوان:</h4>
                  <p>A108 Adam Street, New York, NY 535022</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-envelope flex-shrink-0"></i>
                <div>
                  <h4>البريد الالكتروني:</h4>
                  <p>info@example.com</p>
                </div>
              </div><!-- End Info Item -->

              <div class="info-item d-flex">
                <i class="bi bi-phone flex-shrink-0"></i>
                <div>
                  <h4>واتساب:</h4>
                  <p>+1 5589 55488 55</p>
                </div>
              </div><!-- End Info Item -->

            </div>

          </div>

          <div class="col-lg-8">
            <form action="forms/contact.php" method="post" role="form" class="php-email-form">
              <div class="row">
                <div class="col-md-6 form-group">
                  <input type="text" name="name" class="form-control" id="name" placeholder="Your Name" required>
                </div>
                <div class="col-md-6 form-group mt-3 mt-md-0">
                  <input type="email" class="form-control" name="email" id="email" placeholder="Your Email" required>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control" name="subject" id="subject" placeholder="Subject" required>
              </div>
              <div class="form-group mt-3">
                <textarea class="form-control" name="message" placeholder="Message" required></textarea>
              </div>
              <div class="my-3">
                <div class="loading">Loading</div>
                <div class="error-message"></div>
                <div class="sent-message">Your message has been sent. Thank you!</div>
              </div>
              <div class="text-center"><button type="submit">Send Message</button></div>
            </form>
          </div><!-- End Contact Form -->

        </div>

      </div>
    </section><!-- End Contact Section -->

  </main><!-- End #main -->
@endsection