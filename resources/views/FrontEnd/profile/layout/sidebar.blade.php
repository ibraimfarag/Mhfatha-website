
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
<a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadNearbyStores">
<i class="fas fa-store"></i>
{{ app()->getLocale() === 'ar' ? 'المتاجر القريبة' : 'Nearby Stores' }}
</a>
</li>
<li>
<a class="nav-link scrollto user_sideBar" href="javascript:void(0);" onclick="loadManageStores">
<i class="fas fa-cog"></i>
{{ app()->getLocale() === 'ar' ? 'ادارة المتاجر' : 'Manage Stores' }}
</a>
</li>
</ul>

</nav><!-- .navbar -->



</div>

</div>