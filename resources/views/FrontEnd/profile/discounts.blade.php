@extends('FrontEnd.profile.layout.master')
@section('dash-content')

<div class="container" id="history">
    <h1>User Discounts</h1>

    <table class="table">
        <thead>
            <tr>
                <th>Discount Name</th>
                <th>Discount Amount</th>
                <th> Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($userDiscounts as $discount)
            <tr>
                <td>{{ $discount->store->name }}</td>
                <td>{{ $discount->discount->category }}</td>
                <td>{{ $discount->date }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>


@endsection