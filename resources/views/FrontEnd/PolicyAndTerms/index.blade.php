@extends('FrontEnd.layouts.master')

@section('content')

<style>
    .padding {
        margin-top: 20vh;
    }
    .white-text {
        color: white;
    }
    .no-dots ul {
        list-style-type: none;
    }
    h4 {
        font-weight: 400 !important;
    }
    .white-text ul li {
        color: white;
    }
</style>

<!-- Meza Section -->
<section id="privacy" class="about padding">
    <div class="container" data-aos="fade-up">

        <div class="section-header">
            <h2>{{ $lang === 'ar' ? 'سياسة الخصوصية' : 'privacy policy' }}</h2>
            {{-- <p>{{ ($lang === 'ar') ? $user_arabic_content['Terms and Conditions for the Beneficiary or Consumer']['introduction'] : $user_english_content['Terms and Conditions for the Beneficiary or Consumer']['introduction'] ?? 'Default introduction text' }}</p> --}}
        </div>

        <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">

            <div class="col-lg-5">
                <div class="about-img">
                    <img src="{{ asset('/FrontEnd/assets/images/meza/Privacy Policy.png') }}" class="img-fluid grad" alt="">
                </div>
            </div>

            <div class="col-lg-7 section-titles">
                <h5 class="pt-0 pt-lg-5">{{ ($lang === 'ar') ? "الشروط والاحكام":"Privacy terms and conditions"  }}</h5>

                <!-- Tabs -->
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="pill" href="#tab1">{{ $lang === 'ar' ? 'العميل' : 'Customer' }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link mr-3" data-bs-toggle="pill" href="#tab2">{{ $lang === 'ar' ? 'التاجر' : 'Vendor' }}</a>
                    </li>
                </ul><!-- End Tabs -->

                <!-- Tab Content -->
                <div class="tab-content">
                    <!-- User Terms -->
                    <div class="tab-pane fade show active" id="tab1">
                           
                        @if(isset($user_arabic_content) || isset($user_english_content))
                            <!-- Display User Terms and Conditions -->
                            <div class="user-terms white-text no-dots">
                                @if($lang == 'ar' && isset($user_arabic_content))
                                    @foreach($user_arabic_content as $title => $sections)
                                        <h4>{{ $title }}</h4>
                                        <br>

                                        @foreach($sections as $sectionTitle => $sectionContent)
                                            <strong>{{ $sectionTitle }}</strong>
                                            <ul>
                                                @foreach($sectionContent as $content)
                                                    <li>{{ $content }}</li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    @endforeach
                                @elseif($lang == 'en' && isset($user_english_content))
                                    @foreach($user_english_content as $title => $sections)
                                        <h4>{{ $title }}</h4>
                                        @foreach($sections as $sectionTitle => $sectionContent)
                                            <strong>{{ $sectionTitle }}</strong>
                                            <ul>
                                                @foreach($sectionContent as $content)
                                                    <li>{{ $content }}</li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <p class="white-text">{{ __('No user terms available.') }}</p>
                        @endif
                    </div><!-- End Tab 1 Content -->

                    <!-- Vendor Terms -->
                    <div class="tab-pane fade show" id="tab2">        
        
                        @if(isset($vendor_arabic_content) || isset($vendor_english_content))
                            <!-- Display Vendor Terms and Conditions -->
                            <div class="vendor-terms white-text no-dots">
                                @if($lang == 'ar' && isset($vendor_arabic_content))
                                    @foreach($vendor_arabic_content as $title => $sections)
                                        <h4>{{ $title }}</h4>
                                        <br>
                                        @foreach($sections as $sectionTitle => $sectionContent)
                                            <strong>{{ $sectionTitle }}</strong>
                                            <ul>
                                                @foreach($sectionContent as $content)
                                                    <li>{{ $content }}</li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    @endforeach
                                @elseif($lang == 'en' && isset($vendor_english_content))
                                    @foreach($vendor_english_content as $title => $sections)
                                        <h4>{{ $title }}</h4>
                                        @foreach($sections as $sectionTitle => $sectionContent)
                                            <strong>{{ $sectionTitle }}</strong>
                                            <ul>
                                                @foreach($sectionContent as $content)
                                                    <li>{{ $content }}</li>
                                                @endforeach
                                            </ul>
                                        @endforeach
                                    @endforeach
                                @endif
                            </div>
                        @else
                            <p class="white-text">{{ __('No vendor terms available.') }}</p>
                        @endif
            
                    </div><!-- End Tab 2 Content -->

                </div><!-- End Tab Content -->

            </div><!-- End col-lg-7 -->

        </div><!-- End row -->
        
    </div><!-- End container -->
</section><!-- End About Section -->

</main><!-- End #main -->
@endsection
