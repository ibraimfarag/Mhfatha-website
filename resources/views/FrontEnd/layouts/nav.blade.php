
  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top" data-scrollto-offset="0">
    <div class="container-fluid d-flex align-items-center justify-content-between">


      <nav id="navbar" class="navbar">
        
      <a href="/" class="logo d-flex align-items-center scrollto me-auto me-lg-0">
        <!-- Uncomment the line below if you also wish to use an image logo -->
        <img src="{{ asset('/FrontEnd/assets/images/logo/logo.png') }}" alt="">

      </a>
        <ul>


          <li><a class="nav-link scrollto " href="{{ route('home', ['lang' => app()->getLocale()])}}#hero">{{ app()->getLocale() === 'ar' ? 'الرئيسية' : 'Home' }}</a></li>
          <li><a class="nav-link scrollto" href="{{ route('home', ['lang' => app()->getLocale()])}}#about">{{ app()->getLocale() === 'ar' ? 'من نحن' : 'About us' }} </a></li>
          <li><a class="nav-link scrollto" href="{{ route('home', ['lang' => app()->getLocale()])}}#meza">{{ app()->getLocale() === 'ar' ? 'مميزاتنا' : 'Advantages' }}</a></li>
          <li><a class="nav-link scrollto" href="{{ route('home', ['lang' => app()->getLocale()])}}#app">{{ app()->getLocale() === 'ar' ? 'التطبيق' : 'App' }}</a></li>
          <li><a class="nav-link scrollto" href="{{ route('home', ['lang' => app()->getLocale()])}}#portfolio">{{ app()->getLocale() === 'ar' ? 'السياسة والشروط' : 'Policy and terms' }}</a></li>
          <li><a class="nav-link scrollto" href="{{ route('home', ['lang' => app()->getLocale()])}}#contact">{{ app()->getLocale() === 'ar' ? 'اتصل بنا' : 'contact us' }} </a></li>
        </ul>
        <i class="bi bi-list mobile-nav-toggle d-none"></i>
      </nav><!-- .navbar -->


      
      
      <nav id="navbar" class="navbar left-nav">
        

          <ul>
  
  
            <li><a class="nav-link scrollto" href="{{ route('login', ['lang' => app()->getLocale()]) }}#login">
              {{ app()->getLocale() === 'ar' ? 'الدخول' : 'login' }}
          </a></li>
          <span>|</span>            <li><a class="nav-link " href="#">{{ app()->getLocale() === 'ar' ? 'التسجيل' : 'register' }}</a></li><span>|</span>

            @if (App::getLocale() == 'en')
            <li><a class="nav-link" href="{{ route(request()->route()->getName(), ['lang' => 'ar']) }}">عربي</a></li>
        @elseif (App::getLocale() == 'ar')
        <li><a class="nav-link"href="{{ route(request()->route()->getName(), ['lang' => 'en']) }}">EN</a></li>
        @endif
          


          </ul>
        </nav><!-- .navbar -->
  
    </div>
  </header><!-- End Header -->
