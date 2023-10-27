@extends('FrontEnd.profile.layout.master')
@section('dash-content')


<div class="row" id="an">
<h4> مرحبا ,  {{ Auth::user()->first_name }}</h4>
  <div class="container mt-4">
    <div class="row">
<div class="col-lg-6">



  
<div class="row m-3">
  <div class="col-md-6">
    <div class="card gradient-card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8"><h5 class="card-title"> عدد الخصومات</h5>
            <p class="card-text">124</p>
          </div>
          <div class="col-lg-4"><i class="fa-solid fa-percent dashcard_icon"></i></div>

        </div>
        
      </div>
    </div>
  </div>



  <div class="col-md-6">
    <div class="card gradient-card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8"><h5 class="card-title"> اجمالي المشتريات</h5>
            <p class="card-text">8000</p>
          </div>
          <div class="col-lg-4"><i class="fa-solid fa-dollar-sign dashcard_icon"></i></div>

        </div>
        
      </div>
    </div>
  </div>



</div>



<div class="row m-3">
  <div class="col-md-6">
    <div class="card gradient-card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8"><h5 class="card-title"> اجمالي التوفير</h5>
            <p class="card-text">2512.50 </p>
          </div>
          <div class="col-lg-4"><i class="fa-solid fa-wallet dashcard_icon"></i></div>

        </div>
        
      </div>
    </div>
  </div>



  <div class="col-md-6">
    <div class="card gradient-card">
      <div class="card-body">
        <div class="row">
          <div class="col-lg-8"><h5 class="card-title"> عدد الخصومات</h5>
            <p class="card-text">124</p>
          </div>
          <div class="col-lg-4"><i class="fa-solid fa-percent dashcard_icon"></i></div>

        </div>
        
      </div>
    </div>
  </div>



</div>
    


    </div>
<div class="col-lg-6">

</div>
    </div>
  </div>



</div>

@endsection