@extends('FrontEnd.profile.layout.master')
@section('dash-content')

<div class="row" id="an">
    <h4>مرحبا, {{ Auth::user()->first_name }}</h4>
    <div class="container mt-4">

        @if( Auth::user()->is_admin)
            {{-- Content for users who are both vendors and admins --}}
            @include('FrontEnd.profile.dashboards.admin')

        @elseif(Auth::user()->is_vendor )
            {{-- Content for users who are vendors but not admins --}}
            @include('FrontEnd.profile.dashboards.vendor')

        @else
            {{-- Content for users who are neither vendors nor admins --}}
            @include('FrontEnd.profile.dashboards.user')

        @endif

    </div>
</div>

@endsection
