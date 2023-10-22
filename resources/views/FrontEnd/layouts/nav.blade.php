
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top" data-scrollto-offset="0">
    <div class="container-fluid d-flex align-items-center justify-content-between">


      <nav id="navbar" class="navbar">
        
      <a href="index.html" class="logo d-flex align-items-center scrollto me-auto me-lg-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="{{ asset('/FrontEnd/assets/images/logo/logo.png') }}" alt="">

      </a>
        <ul>


          <li><a class="nav-link scrollto " href="index.html#hero">{{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
          <li><a class="nav-link scrollto" href="index.html#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About us' }} </a></li>
          <li><a class="nav-link scrollto" href="index.html#meza">{{ app()->getLocale() === 'ar' ? 'مميزاتنا' : 'Advantages' }}</a></li>
          <li><a class="nav-link scrollto" href="index.html#app">{{ app()->getLocale() === 'ar' ? 'التطبيق' : 'App' }}</a></li>
          <li><a class="nav-link scrollto" href="index.html#portfolio">{{ app()->getLocale() === 'ar' ? 'السياسة والشروط' : 'Policy and terms' }}</a></li>
          <li><a class="nav-link scrollto" href="index.html#contact">{{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'contact us' }} </a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle d-none"></i>
      </nav><!-- .navbar -->


      
      
      <nav id="navbar" class="navbar left-nav">
        

          <ul>
  
  
            <li><a class="nav-link  " href="#">{{ app()->getLocale() === 'ar' ? 'الدخول' : 'login' }}</a></li><span>|</span>
            <li><a class="nav-link " href="#">{{ app()->getLocale() === 'ar' ? 'التسجيل' : 'register' }}</a></li><span>|</span>

            @if (App::getLocale() == 'en')
            <li><a class="nav-link" href="{{ route('home', ['lang' => 'ar']) }}">عربي</a></li>
        @elseif (App::getLocale() == 'ar')
        <li><a class="nav-link"href="{{ route('home', ['lang' => 'en']) }}">EN</a></li>
        @endif
          


          </ul>
        </nav><!-- .navbar -->
  
    </div>
  </header><!-- End Header -->
