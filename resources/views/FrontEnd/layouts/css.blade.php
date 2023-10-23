
  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&family=Source+Sans+Pro:ital,wght@0,300;0,400;0,600;0,700;1,300;1,400;1,600;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->


  <link href="{{ asset('/FrontEnd/assets/css/bootstrap/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/FrontEnd/assets/css/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">


  <link href="{{ asset('/FrontEnd/assets/css/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ asset('/FrontEnd/assets/css/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/FrontEnd/assets/css/swiper/swiper-bundle.min.css') }}" rel="stylesheet">




  <!-- Variables CSS Files. Uncomment your preferred color scheme -->
  <link href="{{ asset('/FrontEnd/assets/css/main/variables.css') }}" rel="stylesheet">
  <!-- Template Main CSS File -->

  @if (App::getLocale() == 'en')
  <link href="{{ asset('/FrontEnd/assets/css/main/ltr.css') }}" rel="stylesheet">
@elseif (App::getLocale() == 'ar')
<link href="{{ asset('/FrontEnd/assets/css/main/main.css') }}" rel="stylesheet">

@endif

  <link rel="stylesheet"  href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
