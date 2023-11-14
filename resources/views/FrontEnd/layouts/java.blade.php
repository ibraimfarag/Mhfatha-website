
  <!-- Vendor JS Files -->
  <script src="{{ asset('/FrontEnd/assets/js/Bootstrap/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('/FrontEnd/assets/js/aos/aos.js') }}"></script>
  <script src="{{ asset('/FrontEnd/assets/js/glightbox/glightbox.min.js') }}"></script>
  <script src="{{ asset('/FrontEnd/assets/js/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ asset('/FrontEnd/assets/js/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ asset('/FrontEnd/assets/js/JQueary/3.6.0/jquery.min.js') }}"></script>

  <!-- Template Main JS File -->
  <script src="{{ asset('/FrontEnd/assets/js/main/main.js') }}"></script>
  <script>
    // Get the language toggle button and the elements you want to change language
    const languageToggle = document.getElementById('language-toggle');
    const elementsToTranslate = document.querySelectorAll('[data-translatable]');

    // Define your content in English
    const englishContent = {
        // Add English translations for elements with data-translatable attribute
        // For example:
        nav: 'EN',
        welcomeMessage: 'Welcome to our website!',
        // Add more translations as needed
    };

    // Initial state: Content is in Arabic
    let currentLanguage = 'ar';

    // Function to toggle the language
    function toggleLanguage() {
        currentLanguage = currentLanguage === 'ar' ? 'en' : 'ar';

        // Update the content of elements with data-translatable attribute
        elementsToTranslate.forEach(element => {
            const translationKey = element.getAttribute('data-translatable');
            element.textContent = currentLanguage === 'ar' ? translationKey : englishContent[translationKey];
        });

        // Update the language toggle button text
        languageToggle.textContent = currentLanguage === 'ar' ? 'EN' : 'AR';
    }

    // Add a click event listener to the language toggle button
    languageToggle.addEventListener('click', toggleLanguage);
</script>


<script>
    function toggleTimeInput(checkbox) {
        var inputFrom = checkbox.nextSibling.nextSibling;
        var inputTo = inputFrom.nextSibling.nextSibling;
    
        if (!checkbox.checked) {
            inputFrom.disabled = true;
            inputTo.disabled = true;
        } else {
            inputFrom.disabled = false;
            inputTo.disabled = false;
        }
    }
    </script>