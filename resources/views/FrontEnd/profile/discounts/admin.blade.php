@extends('FrontEnd.profile.layout.master')

@section('dash-content')
<div class="container" id="discountsall">
    <h3>{{ app()->getLocale() === 'ar' ? 'عروض المستخدمين' : 'User Discounts' }}</h3>

    <!-- Filter options -->
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" id="userFilter" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بواسطة اسم المستخدم' : 'Search by User Name' }}">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <input type="text" id="storeFilter" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بواسطة اسم المتجر' : 'Search by Store Name' }}">
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <select id="statusFilter" class="form-control">
                    <option value="">{{ app()->getLocale() === 'ar' ? 'الكل' : 'all' }}</option>
                    <option value="3">{{ app()->getLocale() === 'ar' ? 'معلق' : 'Pending' }}</option>
                    <option value="1">{{ app()->getLocale() === 'ar' ? 'مقبول' : 'Accepted' }}</option>
                    <option value="2">{{ app()->getLocale() === 'ar' ? 'مرفوض' : 'Rejected' }}</option>
                </select>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" id="regionFilter" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بواسطة المنطقة' : 'Search by Region' }}">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <input type="text" id="cityFilter" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'ابحث بواسطة المدينة' : 'Search by City' }}">
            </div>
        </div>
    </div>
    

    <table class="table transparent">
        <thead>
            <tr>
                <th>{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'User Name' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Region' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'الجنس' : 'Gender' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'اسم المتجر' : 'Store Name' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'فئة الخصم' : 'Discount Category' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'نسبة الخصم' : 'Discount Percent' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'بعد الخصم' : 'After Discount' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'حالة الخصم' : 'Discount Status' }}</th>
        
            </tr>
        </thead>
        <tbody id="discountsTableBody">
            <!-- Discounts will be displayed here using JavaScript -->
        </tbody>
    </table>
</div>
@endsection

@section('sub-js')


<script>
$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    // Fetch and display user discounts on page load
    fetchUserDiscounts();

    // Attach event listeners to the filter inputs
    $('#userFilter, #storeFilter, #statusFilter, #regionFilter, #cityFilter').on('input', function () {
        fetchUserDiscounts();
    });
    setInterval(fetchUserDiscounts, 2000); 



});

function fetchUserDiscounts() {
    $('[data-toggle="tooltip"]').tooltip('hide');

    const userFilter = $('#userFilter').val();
    const storeFilter = $('#storeFilter').val();
    const statusFilter = $('#statusFilter').val();
    const regionFilter = $('#regionFilter').val();
    const cityFilter = $('#cityFilter').val();

    $.ajax({
        url: "{{ route('discounts.fetch') }}",
        method: 'GET',
        data: {
            userFilter: userFilter,
            storeFilter: storeFilter,
            statusFilter: statusFilter,
            regionFilter: regionFilter,
            cityFilter: cityFilter
        },
        success: function (response) {
            updateDiscountsTable(response);
        }
    });
}

function updateDiscountsTable(discounts) {
    const discountsTableBody = $('#discountsTableBody');
    discountsTableBody.empty();
    let rowColor = true;

    discounts.forEach(function (discount) {
        const statusText = discount.status === 3 ?
            '<span style="color: orange;">{{ app()->getLocale() === 'ar' ? 'معلق' : 'Pending' }}</span>' :
            discount.status === 1 ?
            '<span style="color: green;">{{ app()->getLocale() === 'ar' ? 'مقبول' : 'Accepted' }}</span>' :
            discount.status === 2 ?
            '<span style="color: red;">{{ app()->getLocale() === 'ar' ? 'مرفوض' : 'Rejected' }}</span>' :
            '<span style="color: blue;">{{ app()->getLocale() === 'ar' ? 'غير معروف' : 'Unknown' }}</span>';

        const reasonCell = `${discount.status === 1 || discount.status === 3 ? '' : 
                        `<i class="fa-regular fa-circle-question" data-toggle="tooltip" data-trigger="hover" data-placement="top" title="${discount.reason}"></i>`} `;

        const rowClass = rowColor ? 'light-row' : 'dark-row';
        rowColor = !rowColor;

        const row = `
            <tr class="${rowClass}">
                <td>${discount.user_name}</td>
                <td>${discount.user_region}</td>
                <td>${discount.user_gender}</td>
                <td>${discount.store_name}</td>
                <td>${discount.store_city}</td>
                <td>${discount.category}</td>
                <td>${discount.percent}%</td>
                <td>${discount.after_discount}</td>
                <td>${statusText} <span class="reason-tooltip">${reasonCell} </span></td>
                
            </tr>
        `;

        discountsTableBody.append(row);
    });

    // // Enable Bootstrap tooltips
       $('[data-toggle="tooltip"]').tooltip();
}



</script>
@endsection
