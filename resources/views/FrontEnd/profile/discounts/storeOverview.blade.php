@extends('FrontEnd.profile.layout.master')

@section('dash-content')
<div class="container" id="stores">
    <!-- Tabs -->
    <ul class="nav nav-tabs" id="storeTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="storeInfo-tab" data-toggle="tab" href="#storeInfo" role="tab" aria-controls="storeInfo" aria-selected="true">Store Info</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="storeDiscounts-tab" data-toggle="tab" href="#storeDiscounts" role="tab" aria-controls="storeDiscounts" aria-selected="false">Store Discounts</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content" id="storeTabsContent">
        <!-- Store Info Tab -->
        <div class="tab-pane fade show active" id="storeInfo" role="tabpanel" aria-labelledby="storeInfo-tab">
            <!-- Content for the "Store Info" tab goes here -->
            <div id="storeInfoContent">
                <!-- Real-time data for Store Info tab -->
            </div>
        </div>

        <!-- Store Discounts Tab -->
        <div class="tab-pane fade" id="storeDiscounts" role="tabpanel" aria-labelledby="storeDiscounts-tab">
            <!-- Content for the "Store Discounts" tab goes here -->
            <div id="storeDiscountsContent">
                <!-- Real-time data for Store Discounts tab -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('sub-js')
<script>
$(document).ready(function () {
    // Function to fetch and update store info and discounts
    function fetchStoreData() {
        // Fetch and update store info data
        $.ajax({
            url: "",
            method: 'GET',
            success: function (storeInfoData) {
                // Update the content in the "#storeInfoContent" element
                $('#storeInfoContent').html(storeInfoData);
            }
        });

        // Fetch and update store discounts data
        $.ajax({
            url: "",
            method: 'GET',
            success: function (storeDiscountsData) {
                // Update the content in the "#storeDiscountsContent" element
                $('#storeDiscountsContent').html(storeDiscountsData);
            }
        });
    }

    // Initial data fetch on page load
    fetchStoreData();

    // Set interval to fetch data every 5 seconds (5000 milliseconds)
    setInterval(fetchStoreData, 5000);
});
</script>
@endsection
