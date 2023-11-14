@extends('FrontEnd.layouts.master')
@section('title', app()->getLocale() === 'ar' ? '- لوحة التحكم ' : '- Dashboard')
@section('content')


    <!-- ======= dashbord Section ======= -->
    <section id="dashboard_user" class="userdash login-background ">
        <div class="container-fluid" data-aos="fade-up">
  
       
 
         
  
          <div class="row g-4 g-lg-5" data-aos="fade-up" data-aos-delay="200">
  
      

                <div class="userdash-form">
                      <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                      
                      @include('FrontEnd.profile.layout.sidebar')

                    <!-- Left Panel -->
<div class="col-md-10" id="left-panel">
      <div class="profile-info">


@yield('dash-content')



    
    </div></div>


                    </div>
                       
                </div>    
                      



            </div>
          
           
          </div>
  
        </div>
        
      </section>
      <!-- End Login Section -->

     
  @endsection
@section('js')
<script>
  // Define city-to-region mapping
  const cityToRegions = {
  "riyadh": ["الرياض", "الدرعية", "الخرج", "الدوادمي", "المجمعة", "القويعية", "الأفلاج", "وادي الدواسر", "الزلفي", "شقراء", "حوطة بني تميم", "عفيف", "الغاط", "السليل", "ضرما", "المزاحمية", "رماح", "ثادق", "حريملاء", "الحريق", "مرات", "الرين", "الدلم", "Another City in Riyadh"],
  "makkah": ["مكة المكرمة","جدة","الطائف","القنفذة","الليث","رابغ","خليص","الخرمة","رنية","تربة","الجموم","الكامل","المويه","ميسان","أضم","العرضيات","بحرة","Another City in Makkah Al-Mukarramah"] ,
  "madinah": ["المدينة المنورة","ينبع","العلا","المهد","الحناكية","بدر","خيبر","العيص","وادي الفرع"]  , 
  "qassim": ["بريدة","عنيزة","الرس","المذنب","البكيرية","البدائع","الأسياح","النبهانية","الشماسية","عيون الجواء","رياض الخبراء","عقلة الصقور","ضرية","Another City in Qassim"],
  "eastern": ["الدمام","الأحساء","حفر الباطن","الجبيل","القطيف","الخبر","الخفجي","رأس تنورة","بقيق","النعيرية","قرية العليا","العديد","البيضاء","Another City in Eastern Province"],    
  "asir": ["أبها","خميس مشيط","بيشة","النماص","محايل عسير","ظهران الجنوب","تثليث","سراة عبيدة","رجال ألمع","بلقرن","أحد رفيدة","المجاردة","البرك","بارق","تنومة","طريب","Another City in Asir"],    
  "tabuk": ["تبوك", "الوجه","ضباء","تيماء","أملج","حقل","البدع"],
  "northern": ["عرعر", "رفحاء","طريف","العويقيلة"],
  "hail": ["حائل","بقعاء","الغزالة","الشنان","الحائط","السليمي","الشملي","موقق","سميراء"],
  "jazan": ["جازان","صبيا","أبو عريش","صامطة","بيش","الدرب","الحرث","ضمد","الريث","فرسان","الدائر","العارضة","أحد المسارحة","العيدابي","فيفاء","الطوال","هروب"],  
  "najran": ["نجران","شرورة","حبونا","بدر الجنوب","يدمة","ثار","خباش"],
  "bahah": ["الباحة","بلجرشي","المندق","المخواة","قلوة","العقيق","القرى","غامد الزناد","الحجرة","بني حسن"],    
  "aljouf": ["سكاكا", "القريات","دومة الجندل","طبرجل"],

  // Continue with the remaining regions and cities
              };

  // Get references to the city and region select elements
  const citySelect = document.getElementById("city");
  const regionSelect = document.getElementById("region");

  // Function to update region options based on the selected city
  function updateRegions() {
      const selectedCity = citySelect.value;
      const regions = cityToRegions[selectedCity] || [];

      // Clear existing region options
      regionSelect.innerHTML = "";

      // Add new region options
      regions.forEach((region) => {
          const option = document.createElement("option");
          option.value = region;
          option.textContent = region;
          regionSelect.appendChild(option);
      });
  }

  // Add an event listener to update regions when the city selection changes
  citySelect.addEventListener("change", updateRegions);

  // Initial update based on the default city selection
  updateRegions();
</script>

<script>
    // JavaScript to update the image preview
    document.getElementById('profile_image').addEventListener('change', function() {
        const fileInput = this;
        if (fileInput.files && fileInput.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
            };
            reader.readAsDataURL(fileInput.files[0]);
        }
    });

</script>

<script>

// Function to update badge counts and play notification sound if counts change
function updateBadgeCounts() {
    // Make an AJAX request to get real-time badge counts
    $.ajax({
        url: '/get-badge-counts', // Adjust the URL based on your application structure
        method: 'GET',
        dataType: 'json',
        success: function (data) {
            // Get the current badge counts from the DOM
            const currentSalesOrderCount = parseInt($('#salesOrderBadge').text());
            const currentMessagesCount = parseInt($('#messagesBadge').text());

            // Check if the counts have changed
            if (data.salesOrderBadgeCount !== currentSalesOrderCount) {
                playNotificationSound(); // Play the notification sound
            }
            if (data.messagesBadgeCount !== currentMessagesCount) {
                playNotificationSound(); // Play the notification sound
            }

            // Update badge counts in the DOM
            $('#salesOrderBadge').text(data.salesOrderBadgeCount);
            $('#messagesBadge').text(data.messagesBadgeCount);
        },
        error: function (error) {
            console.error('Error fetching badge counts:', error);
        }
    });
}

// Function to play the notification sound
function playNotificationSound() {
    const audio = new Audio('/FrontEnd/assets/sounds/wr.mp3'); // Adjust the path to your notification sound
    audio.play();
}

// Update badge counts on page load
$(document).ready(function () {
    updateBadgeCounts();
});

// Schedule periodic updates (e.g., every 1 minute)
setInterval(updateBadgeCounts, 2000); // Adjust the interval as needed


</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.min.js" integrity="sha512-7U4rRB8aGAHGVad3u2jiC7GA5/1YhQcQjxKeaVms/bT66i3LVBMRcBI9KwABNWnxOSwulkuSXxZLGuyfvo7V1A==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>



<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Document is ready.');

        // Fetch data for the chart from the server
        const fetchDataForChart = async (timePeriod) => {
            try {
                const response = await fetch(`/vendor/discount-chart-data/${timePeriod}`); // Adjust the route accordingly
                const data = await response.json();
    
                // Ensure the response is in the expected format
                if (data.success) {
                    // Extract labels and dataset values from the response
                    const labels = data.labels;
                    const datasetValues = data.dataset;
    
                    // Render the chart
                    renderChart(labels, datasetValues);
                } else {
                    console.error('Failed to fetch data for the chart.');
                }
            } catch (error) {
                console.error('Error fetching data for the chart:', error);
            }
        };
    
        // Function to render the chart
        const renderChart = (labels, datasetValues) => {
            const ctx = document.getElementById('discountChart').getContext('2d');
    
            const chart = new Chart(ctx, {
                type: 'line', // Choose the chart type (line chart in this case)
                data: {
                    labels: labels, // X-axis labels (e.g., days, weeks, months)
                    datasets: [{
                        label: 'Total After Discount', // Dataset label
                        data: datasetValues, // Y-axis values
                        fill: false, // Disable fill beneath the line
                        borderColor: 'rgba(75, 192, 192, 1)', // Line color
                        borderWidth: 2, // Line width
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'time', // Specify time as the x-axis scale
                            time: {
                                unit: 'day', // Adjust the time unit (day, week, month, year, seven years)
                            },
                        },
                        y: {
                            beginAtZero: true,
                        },
                    },
                },
            });
        };
    
        // Initialize the chart with a default time period (e.g., 'week')
        fetchDataForChart('week');
    
        // Add event listeners to update the chart based on the selected time period
        document.getElementById('selectTimePeriod').addEventListener('change', function () {
            const selectedTimePeriod = this.value;
            fetchDataForChart(selectedTimePeriod);
        });
    });
    </script>
    <script>
        let watchId; // Variable to store the watchPosition ID
    
        function watchUserLocation() {
            if (navigator.geolocation) {
                // Start watching the user's location
                watchId = navigator.geolocation.watchPosition(
                    function(position) {
                        const userLatitude = position.coords.latitude;
                        const userLongitude = position.coords.longitude;
    
                        // Redirect to the nearby stores page with parameters
                        window.location.href = `{{ route('stores.nearby') }}?lang={{ app()->getLocale() }}&user_latitude=${userLatitude}&user_longitude=${userLongitude}`;
                    },
                    function(error) {
                        console.error('Error getting user location:', error.message);
                        // Handle the error, show a message, or provide an alternative action
                    }
                );
            } else {
                console.error('Geolocation is not supported by this browser.');
                // Handle the case where geolocation is not supported
            }
        }
    
        // Optional: Stop watching the user's location when they navigate away
        window.addEventListener('beforeunload', function() {
            if (watchId) {
                navigator.geolocation.clearWatch(watchId);
            }
        });
    </script>
    

    @yield('sub-js')
@endsection