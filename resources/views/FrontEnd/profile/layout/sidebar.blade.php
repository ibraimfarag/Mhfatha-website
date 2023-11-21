
<div class="row">
    <!-- Right Sidebar -->
<div class="col-md-2">
<div class="sidebar">

<nav id="navbar" class="navbar user_sideBar">


<ul class="user_sideBar">
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('dashboard_user',['lang' => app()->getLocale()]) }}#an"  >
<i class="fas fa-chart-simple"></i>
{{ app()->getLocale() === 'ar' ? 'احصائيات' : 'statistics' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('profile',['lang' => app()->getLocale()]) }}#prfile" >
<i class="fas fa-user"></i>
{{ app()->getLocale() === 'ar' ? 'الملف الشخصي' : 'Profile' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('password.change',['lang' => app()->getLocale()]) }}#ChangePassword" >
<i class="fas fa-unlock-alt"></i>
{{ app()->getLocale() === 'ar' ? 'تغير كلة السر' : 'change password' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('discount.view',['lang' => app()->getLocale()]) }}#history" >
<i class="fas fa-history"></i>
{{ app()->getLocale() === 'ar' ? 'سجل الخصومات' : 'Previous Discounts' }}
</a>
</li>
<li>
    <a class="nav-link scrollto user_sideBar" href="#" onclick="watchUserLocation()">
        <i class="fas fa-store"></i>
        {{ app()->getLocale() === 'ar' ? 'المتاجر القريبة' : 'Nearby Stores' }}
    </a>
</li>
    
@if (Auth::user()->is_vendor || Auth::user()->is_admin)
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('Stores.view',['lang' => app()->getLocale()]) }}#stores">
<i class="fas fa-cog"></i>
{{ app()->getLocale() === 'ar' ? 'ادارة المتاجر' : 'Manage Stores' }}
</a>
</li>

<li>
    <a class="nav-link scrollto user_sideBar" href="{{ route('Stores.view',['lang' => app()->getLocale()]) }}#orders">
        <i class="fas fa-shopping-cart"></i>
        {{ app()->getLocale() === 'ar' ? 'طلبات المبيعات' : 'Sales Orders' }}
        <span id="salesOrderBadge" class="badge badge-Warning"></span>
    </a>
</li>

<li>
    <a class="nav-link scrollto user_sideBar" href="{{ route('Stores.view',['lang' => app()->getLocale()]) }}#messages">
        <i class="fas fa-envelope"></i>
        {{ app()->getLocale() === 'ar' ? 'رسائل' : 'Messages' }}
        <span id="messagesBadge" class="badge badge-danger"></span>
    </a>
</li>
@endif

@if (Auth::user()->is_admin)
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('users.index',['lang' => app()->getLocale()]) }}#UsersList">
    <i class="fa-solid fa-users-gear"></i>{{ app()->getLocale() === 'ar' ? 'ادارة المستخدمين ' : 'Manage users ' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('discounts.admin.view',['lang' => app()->getLocale()]) }}#discountsall">
    <i class="fa-solid fa-percent"></i>{{ app()->getLocale() === 'ar' ? ' الخصومات ' : 'Discounts ' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="{{ route('GeneralSection',['lang' => app()->getLocale()]) }}#settings">
    <i class="fa-solid fa-screwdriver-wrench"></i>{{ app()->getLocale() === 'ar' ? ' الاعدادات ' : 'Settings ' }}
</a>
</li>

@endif


</ul>

</nav><!-- .navbar -->



</div>

</div>


