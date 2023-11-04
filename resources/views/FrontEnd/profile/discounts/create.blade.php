@extends('FrontEnd.profile.layout.master')
@section('dash-content')

<div class="container" id="stores">
    <a href="{{ url()->previous() }}" class="btn btn-secondary button_dash float-left mt-2">
        {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }} <i class="fa-solid fa-arrow-left"></i> 
    </a>

    <h1>{{ app()->getLocale() === 'ar' ? 'إنشاء خصم جديد' : 'Create New Discount' }}</h1>
<div class="row">

</div>
   <div class="col-4">
    <form method="POST" action="{{ route('discounts.store') }}">
        @csrf
        <input type="hidden" name="store_id" value="{{ $store->id }}">
        <div class="form-group">
            <div class="name-group">

            <label for="percent">{{ app()->getLocale() === 'ar' ? 'نسبة الخصم' : 'Percent' }}:</label>
            <input type="number" name="percent" class="form-control" id="percent" min="0" max="100" step="1">
        </div>
        @if ($errors->has('percent'))
        <div class="alert alert-danger">{{ $errors->first('percent') }}</div>
    @endif
            <div class="name-group">

            <label for="category">{{ app()->getLocale() === 'ar' ? 'الفئة' : 'Category' }}:</label>
            <input type="text" name="category" class="form-control" id="category">
        </div>
        </div>

        <div class="form-group">
            <div class="name-group">

            <label for="start_date">{{ app()->getLocale() === 'ar' ? 'تاريخ البدء' : 'Start Date' }}:</label>
            <input type="date" name="start_date" class="form-control" id="start_date">
        </div>
     
            <div class="name-group">

            <label for="end_date">{{ app()->getLocale() === 'ar' ? 'تاريخ الانتهاء' : 'End Date' }}:</label>
            <input type="date" name="end_date" class="form-control" id="end_date">
        </div>
        </div>

        <button type="submit" class="btn btn-primary button_dash">{{ app()->getLocale() === 'ar' ? 'إنشاء الخصم' : 'Create Discount' }}</button>
    </form>
   </div>

</div>

@endsection

@section('sub-js')
@endsection
