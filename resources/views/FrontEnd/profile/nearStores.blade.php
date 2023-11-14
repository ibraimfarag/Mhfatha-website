@extends('FrontEnd.profile.layout.master')
@section('dash-content')
<div class="container" id="nearby">
    <h3>
        {{ app()->getLocale() === 'ar' ? 'المتاجر القريبة' : 'Stores' }}
    </h3>

<div class="row">

    @if ($nearbyStores->count() > 0)
   
             

    @foreach ($nearbyStores as $key => $store)
    <div class="col-3">

        <div class="card_nearby">
<div class="row">
        <i class="fa-solid fa-shop card_bg"></i>


    <div class="col-12">
        <h4>{{ $store->name }} </h4>

        <p>
         على بعد  {{ $store->distance }} من موقعك الحالي  
        </p>
        <div class="row py-3">
            <div class="col-6">
                <button class="btn btn-primary button_dash">زيارة</button>
            </div>
            <div class="col-6">
                <button class="btn btn-secondary button_dash">الخصومات</button>
            </div>
            
        </div>
    </div>
    


</div>
           
          
           
        

        </div>


    </div>
        

  
    @endforeach

@else
<p>{{ __('No nearby stores found.') }}</p>
@endif


</div>

  
</div>
@endsection

@section('sub-js')

@endsection
