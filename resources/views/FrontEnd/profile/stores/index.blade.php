@extends('FrontEnd.profile.layout.master')
@section('dash-content')
    @if (Auth::user()->is_admin)
        {{-- /* -------------------------------------------------------------------------- */ --}}
        {{-- /* ------------------------------- ADMIN TABLE ------------------------------ */ --}}
        {{-- /* -------------------------------------------------------------------------- */ --}}
        <div class="container" id="stores">
            <h3>
                {{ app()->getLocale() === 'ar' ? 'المتاجر' : ' stores' }}
                {{-- <a href="{{ route('stores.create',['lang' => app()->getLocale()]) }}" class="btn btn-primary">
            {{ app()->getLocale() === 'ar' ? 'إضافة متجر' : 'Add Store' }}
        </a> --}}
            </h3>
            <!-- Filter Form -->
            <form method="GET" action="{{ route('Stores.view', ['lang' => app()->getLocale()]) }}">
                <input type="hidden" name="lang" value="{{ app()->getLocale() }}">


                <div class="form-group">


                    <label for="search">{{ app()->getLocale() === 'ar' ? 'بحث عن متجر' : 'Search for a Store' }}</label>
                    <input type="text" name="search" class="form-control"
                        placeholder="{{ app()->getLocale() === 'ar' ? 'ادخل اسم المتجر/الاسم الاول للتاجر/اسم العائلة للتاجر / رقم هاتف المتجر ' : 'Enter Store Name/First Name of Merchant/Last Name of Merchant/Store Phone Number' }}"
                        value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary button_dash">
                        {{ app()->getLocale() === 'ar' ? 'تطبيق البحث' : 'Apply Search' }}
                    </button>
                    @if (request()->filled('search'))
                        <a href="{{ route('Stores.view', ['lang' => app()->getLocale()]) }}"
                            class="btn btn-danger button_dash">
                            {{ app()->getLocale() === 'ar' ? 'مسح البحث' : 'Clear Search' }}
                        </a>
                    @endif
                </div>

            </form>
            <table class="table transparent">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'اسم المتجر' : 'store name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'اسم التاجر ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'مدينة المتجر  ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? ' الخصومات  ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? ' الخصومات الحالية ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? ' عمليات الخصم ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'اجمالي المدفوعات ' : 'store status' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'حالة المتجر ' : 'store status' }}</th>
                        <!-- Add more table headers as needed -->
                    </tr>
                </thead>
                <tbody class="noBorder">
                    @php $rowColor = true; @endphp
                    @foreach ($userStores as $store)
                        <tr class="{{ $rowColor ? 'light-row' : 'dark-row' }} noBorder">
                            <td>{{ $store->name }}</td>
                            <td>{{ $store->user->first_name }} {{ $store->user->last_name }}</td>
                            <td> الرياض</td>

                            <td>{{ $store->discounts->count() }}</td>
                            <td>{{ $store->discounts->where('discounts_status', 'working')->count() }}</td>
                            <td>{{ $store->userDiscounts->count() }}</td>
                            <td> {{ $store->userDiscounts->sum('after_discount') }}</td>
                            <td>
                                @if ($store->status == 1)
                                    <span class="text-green">{{ app()->getLocale() === 'ar' ? 'مفتوح' : 'Open' }}</span>
                                @else
                                    <span class="text-red">{{ app()->getLocale() === 'ar' ? 'مغلق' : 'Close' }}</span>
                                @endif
                            </td>


                            <td>
                                @if ($store->verifcation == 0)

                                <form method="POST" action="{{ route('stores.verify', ['store' => $store->id, 'lang' => app()->getLocale()]) }}"
                                    style="display: inline;">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                                    <button class="btn btn-info" type="submit">{{ app()->getLocale() === 'ar' ? 'تفعيل' : 'Verify' }}</button>
                                </form>
                                
                                @else
                                <button class="btn btn-info"
                                type="submit" disabled>{{ app()->getLocale() === 'ar' ? 'مفعل' : 'verified' }}</button>
                                @endif


                                <form method="GET" action="{{ route('stores.edit', ['lang' => app()->getLocale()]) }}"
                                    style="display: inline;">
                                    <input type="hidden" name="storeid" value="{{ $store->id }}">
                                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                                    <!-- Add other form fields and submit button if needed -->
                                    <button class="btn btn-primary"
                                        type="submit">{{ app()->getLocale() === 'ar' ? 'ادارة' : 'Manage' }}</button>
                                </form>

                                <form action="{{ route('stores.destroy', ['store' => $store->id]) }}" method="POST"
                                    style="display: inline;">
                                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger">{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</button>
                                </form>
                                <form method="GET"
                                    action="{{ route('discounts.index', ['lang' => app()->getLocale()]) }}"
                                    style="display: inline;">
                                    <input type="hidden" name="storeid" value="{{ $store->id }}">
                                    <input type="hidden" name="lang" value="{{ app()->getLocale() }}">

                                    <!-- Add other form fields and submit button if needed -->
                                    <button class="btn btn-success"
                                        type="submit">{{ app()->getLocale() === 'ar' ? 'الخصومات' : 'Manage' }}</button>
                                </form>
                            </td>
                        </tr>
                        @php $rowColor = !$rowColor; @endphp
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        {{-- /* -------------------------------------------------------------------------- */ --}}
        {{-- /* ------------------------------ VENDOR TABEL ------------------------------ */ --}}
        {{-- /* -------------------------------------------------------------------------- */ --}}
        <div class="container" id="stores">
            <h3>
                {{ app()->getLocale() === 'ar' ? 'المتاجر الخاصه بك' : 'your stores' }}
                <a href="{{ route('stores.create', ['lang' => app()->getLocale()]) }}" class="btn btn-primary">
                    {{ app()->getLocale() === 'ar' ? 'إضافة متجر' : 'Add Store' }}
                </a>
            </h3>
            <table class="table transparent">
                <thead>
                    <tr>
                        <th>{{ app()->getLocale() === 'ar' ? 'اسم المتجر' : 'store name' }}</th>
                        <th>{{ app()->getLocale() === 'ar' ? 'حالة المتجر ' : 'store status' }}</th>
                        <!-- Add more table headers as needed -->
                    </tr>
                </thead>
                <tbody class="noBorder">
                    @php $rowColor = true; @endphp
                    @foreach ($userStores as $store)
                        <tr class="{{ $rowColor ? 'light-row' : 'dark-row' }} noBorder">
                            <td>{{ $store->name }}</td>
                            <td>
                                @if ($store->status == 1)
                                    <span class="text-green">{{ app()->getLocale() === 'ar' ? 'مفتوح' : 'Open' }}</span>
                                @else
                                    <span class="text-red">{{ app()->getLocale() === 'ar' ? 'مغلق' : 'Close' }}</span>
                                @endif
                            </td>

                            @if ($store->verifcation)
                                @if ($store->is_bann)
                                    <!-- Display a message for a verified store that is banned -->
                                    <td>
                                        <p class="text-red">
                                            {{ app()->getLocale() === 'ar' ? 'هذا المتجر محظور' : 'This store is banned.' }}
                                        </p>
                                    </td>
                                @else
                                    <!-- Display content for a verified store that is not banned -->
                                    <td>
                                        <form method="GET"
                                            action="{{ route('stores.edit', ['lang' => app()->getLocale()]) }}"
                                            style="display: inline;">
                                            <input type="hidden" name="storeid" value="{{ $store->id }}">
                                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                                            <!-- Add other form fields and submit button if needed -->
                                            <button class="btn btn-primary"
                                                type="submit">{{ app()->getLocale() === 'ar' ? 'ادارة' : 'Manage' }}</button>
                                        </form>
                                        <form action="{{ route('stores.destroy', ['store' => $store->id]) }}"
                                            method="POST" style="display: inline;">
                                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-danger">{{ app()->getLocale() === 'ar' ? 'حذف' : 'Delete' }}</button>
                                        </form>
                                        <form method="GET"
                                            action="{{ route('discounts.index', ['lang' => app()->getLocale()]) }}"
                                            style="display: inline;">
                                            <input type="hidden" name="storeid" value="{{ $store->id }}">
                                            <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                                            <!-- Add other form fields and submit button if needed -->
                                            <button class="btn btn-success"
                                                type="submit">{{ app()->getLocale() === 'ar' ? 'الخصومات' : 'Manage' }}</button>
                                        </form>
                                    </td>
                                @endif
                            @else
                                <!-- Display a message for an unverified store -->
                                <td>
                                    <p class="text-red">
                                        {{ app()->getLocale() === 'ar' ? 'لم يتم التحقق من المتجر بعد' : 'This store is not verified yet.' }}
                                    </p>
                                </td>
                            @endif


                        </tr>
                        @php $rowColor = !$rowColor; @endphp
                    @endforeach
                    
                </tbody>
            </table>
            <div id="pagination">
                {{ $userStores->links('custom.pagination') }}
            </div>


        </div>
    @endif
@endsection

@section('sub-js')
@endsection
