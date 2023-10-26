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
              <form method="POST" action="{{ route('login_post') }}">
                @csrf
                <div class="register-form">
                      <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                      
                      <div class="form-group">
                                            
                      <div class="name-group">
                          <label for="email_or_mobile">{{ app()->getLocale() === 'ar' ? '   البريد الإلكتروني او رقم الجوال' : 'Email or Mobile number' }}</label>
                          <input id="email_or_mobile" name="email_or_mobile" type="text" placeholder="{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني او رقم الجوال' : 'Email or Mobile number' }}" />
                      </div>
                      </div>
                      <div class="form-group">
                      <div class="name-group">
                          <label for="password">{{ app()->getLocale() === 'ar' ? 'كلمة السر' : 'Password' }}</label>
                          <input id="password"  name="password" type="password" placeholder="{{ app()->getLocale() === 'ar' ? 'كلمة السر' : 'Password' }}" />
                      </div>
                      </div>
                     
                      <div class="form-group">
                          <button type="submit">{{ app()->getLocale() === 'ar' ? 'تسجيل' : 'Register' }}</button>
                      </div>
                       
                </div>    
                        </form>
              @if(session('loginError'))
<div class="alert alert-danger">
    {{ session('loginError') }}
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
      <!-- End Login Section -->

     
  @endsection
@section('js')

@endsection