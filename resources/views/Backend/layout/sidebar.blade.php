<nav id="navbar" class="navbar user_sideBar">



    <ul class="collapse_sidebar user_sideBar">
        <li>
            <a class="nav-link user_sideBar scrollto" href="{{ route('GeneralSection',['lang' => app()->getLocale()]) }}#GeneralSection">
                {{ app()->getLocale() === 'ar' ? ' عام ' : 'General ' }}
            </a>
        </li>
        <li>
            <a class="nav-link user_sideBar scrollto" href="{{ route('HeroSection',['lang' => app()->getLocale()]) }}#HeroSection">
                {{ app()->getLocale() === 'ar' ? ' المقدمة ' : 'Hero' }}
            </a>
        </li>
        <li>
            <a class="nav-link user_sideBar scrollto" href="{{ route('AboutSection',['lang' => app()->getLocale()]) }}#AboutSection">
                {{ app()->getLocale() === 'ar' ? ' من نحن ' : 'Who Are We' }}
            </a>
        </li>
        <li>
            <a class="nav-link user_sideBar scrollto" href="{{ route('AdvantagesSection',['lang' => app()->getLocale()]) }}#AdvantagesSection">
                {{ app()->getLocale() === 'ar' ? ' المميزات ' : 'Advantages' }}
            </a>
        </li>
        <li>
            <a class="nav-link user_sideBar scrollto" href="{{ route('AppSection',['lang' => app()->getLocale()]) }}#AppSection">
                {{ app()->getLocale() === 'ar' ? ' التطبيق ' : 'App' }}
            </a>
        </li>
        <!-- Add more collapsible items if needed -->
    </ul>
    </nav>