@extends('FrontEnd.profile.layout.master')

@section('dash-content')

<div class="row" id="ChangePassword">
    <div class="container">
        <div class="row">
            <div class="col-md-4 offset-md-2">
                <div class="card update-user">
                    <div class="card-header">{{ __('Change Password') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('password.change') }}">
                            @csrf
                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                            <div class="form-group">
                                <div class="name-group">
                                    <label for="current_password">
                                        {{ app()->getLocale() === 'ar' ? 'كلمة المرور الحالية' : 'Current Password' }}
                                    </label>
                                    <input id="current_password" type="password" class="form-control @error('current_password') is-invalid @enderror" name="current_password" required>
                                    @error('current_password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="name-group">
                                    <label for="password">
                                        {{ app()->getLocale() === 'ar' ? 'كلمة المرور الجديدة' : 'New Password' }}
                                    </label>
                                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="name-group">
                                    <label for="password_confirmation">
                                        {{ app()->getLocale() === 'ar' ? 'تأكيد كلمة المرور' : 'Confirm Password' }}
                                    </label>
                                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <button type="submit" class="btn btn-primary">
                                    {{ app()->getLocale() === 'ar' ? 'تغيير كلمة المرور' : 'Change Password' }}
                                </button>
                            </div>
                        </form>
                    </div>
                    @if(session()->has('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session()->has('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
