@extends('FrontEnd.profile.layout.master')
@section('dash-content')

<div class="container" id="stores">
    <a href="{{ url()->previous() }}" class="btn btn-secondary button_dash float-left mt-2">
        {{ app()->getLocale() === 'ar' ? 'رجوع' : 'Back' }} <i class="fa-solid fa-arrow-left"></i> 
    </a>

    <form method="GET" action="{{ route('discounts.create', [ 'lang' => app()->getLocale()]) }}" style="display: inline;">
        <input type="hidden" name="storeid" value="{{ $store->id }}">
        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

        <!-- Add other form fields and submit button if needed -->
        <button class="btn btn-primary button_dash float-left mt-2" type="submit"> {{ app()->getLocale() === 'ar' ? 'اضافة خصم' : 'add discount' }} <i class="fa-solid fa-arrow-left"></i> </button>
    </form>
    <h1> {{ app()->getLocale() === 'ar' ? 'خصومات متجر ' : ' discounts for store' }} {{ old('name', $store->name) }}</h1>




    <table class="table transparent">
        <thead>
            <tr>
                <th>{{ app()->getLocale() === 'ar' ? 'نسبة الخصم' : 'Percent' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'فئة الخصم ' : 'Category' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'بداية الخصم ' : 'Start Date' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'نهاية الخصم ' : 'End Date' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'حالة الخصم ' : 'status ' }}</th>
                <!-- Add more table headers as needed -->
            </tr>
        </thead>
        <tbody class="noBorder">
            @php $rowColor = true; @endphp
            @foreach($discounts as $discount)
            <tr class="{{ $rowColor ? 'light-row' : 'dark-row' }} noBorder">
                <td>{{ $discount->percent }}%</td>
                <td>{{ $discount->category }}</td>
                <td>{{ $discount->start_date }}</td>
                <td>{{ $discount->end_date }}</td>
                <td>
                    @if ($discount->discounts_status === 'working')
                       <span class="text-green">  {{ app()->getLocale() === 'ar' ? 'ساري' : 'Working' }} </span>
                    @elseif ($discount->discounts_status === 'end')
                       
                        <span class="text-red">  {{ app()->getLocale() === 'ar' ? 'منتهي' : 'End' }} </span>
                    @else
                        {{ $discount->discounts_status }}
                    @endif
                </td>
                {{-- <td>
                    @if ($store->status == 1)
                        <span class="text-green">{{ app()->getLocale() === 'ar' ? 'مفتوح' : 'Open' }}</span>
                    @else
                        <span class="text-red">{{ app()->getLocale() === 'ar' ? 'مغلق' : 'Close' }}</span>
                    @endif
                </td> --}}
    
    
                <td>
                    <form method="GET" action="{{ route('stores.edit', [ 'lang' => app()->getLocale()]) }}" style="display: inline;">
                        <input type="hidden" name="storeid" value="{{ $store->id }}">
                        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                   
                        <!-- Add other form fields and submit button if needed -->
                        <button  class="btn btn-primary" type="submit">{{ app()->getLocale() === 'ar' ? 'تحديث' : 'update' }}</button>
                    </form>
                  
                                   <form action="{{ route('discounts.destroy', $discount->id) }}" method="POST" style="display: inline;">
                        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
    
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</button>
                    </form>
                  
                </td>
            </tr>
            @php $rowColor = !$rowColor; @endphp
            @endforeach
        </tbody>
    </table>

</div>


@endsection

@section('sub-js')
@endsection
