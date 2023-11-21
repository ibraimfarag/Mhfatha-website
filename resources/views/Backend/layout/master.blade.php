@extends('FrontEnd.profile.layout.master')
@section('dash-content')
<div class="container" id="settings">
    <h3>
        <i class="fa-solid fa-screwdriver-wrench"></i>   {{ app()->getLocale() === 'ar' ? 'الاعدادت' : 'Setttings' }}
    </h3>

<div class="row">
<div class="col-2">
  @include('Backend.layout.sidebar')
</div>
   <div class="col-9">

@yield('backend-content')



   </div>
             



</div>

  
</div>
@endsection

@section('sub-js')
@yield('backend-js')



@endsection
