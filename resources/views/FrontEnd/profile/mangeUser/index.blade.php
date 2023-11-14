@extends('FrontEnd.profile.layout.master')

@section('dash-content')
<div class="container" id="UsersList">
    <h3>
        {{ app()->getLocale() === 'ar' ? 'المستخدمين' : 'Users' }}
    </h3>
    <div class="form-group">
        <input type="text" id="tagsInput" class="form-control" placeholder="{{ app()->getLocale() === 'ar' ? 'بحث' : 'Search' }}">
    </div>
    <table class="table transparent">
        <thead>
            <tr>
                <th>{{ app()->getLocale() === 'ar' ? 'اسم المستخدم' : 'User Name' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'البريد الإلكتروني' : 'Email' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'الجنس' : 'Gender' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'تاريخ الميلاد' : 'Date of Birth' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'االعمر' : 'Age' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'المنطقة' : 'Region' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'المدينة' : 'City' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'الجوال' : 'mobile' }}</th>
                <th>{{ app()->getLocale() === 'ar' ? 'الحالة' : 'Status' }}</th>
                <th></th>
                <!-- Add more table headers as needed -->
            </tr>
        </thead>
        <tbody class="noBorder" id="usersTableBody">
            @php $rowColor = true; @endphp
            @foreach ($users as $user)
                <tr class="{{ $rowColor ? 'light-row' : 'dark-row' }} noBorder">
                    <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                    <td>{{ $user->email }}</td>
           
                    <td>
                        @if ($user->gender == 'male')
                            <span>{{ app()->getLocale() === 'ar' ? 'ذكر' : 'male' }}</span>
                        @else
                            <span >{{ app()->getLocale() === 'ar' ? 'انثى' : 'female' }}</span>
                        @endif
                    </td>
                    <td>{{ $user->birthday }}</td>
                    <td>{{ $user->age }}</td>
                    <td>{{ $user->region }}</td>
                    <td>{{ $user->city }}</td>
                    <td>{{ $user->mobile }}</td>

                    <td>
                        @if ($user->is_vendor == 1)
                            <span>{{ app()->getLocale() === 'ar' ? 'تاجر' : 'Vendor' }}</span>
                        @else
                            <span >{{ app()->getLocale() === 'ar' ? 'مستخدم' : 'user' }}</span>
                        @endif
                    </td>

                    <td>  <form method="GET"
                        action="{{ route('users.edit', ['lang' => app()->getLocale()]) }}"
                        style="display: inline;">
                        <input type="hidden" name="userid" value="{{ $user->id }}">
                        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                        <!-- Add other form fields and submit button if needed -->
                        <button class="btn btn-primary"
                            type="submit">{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Manage' }}</button>
                    </form>
                    
                    </td>
                    <!-- Add more table cells as needed -->
                </tr>
                @php $rowColor = !$rowColor; @endphp
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@section('sub-js')
<script>
    $(document).ready(function() {
        $('#tagsInput').on('keyup', function() {
            const tags = $(this).val().split(',');
            fetchUsers(tags);
        });
    });

    function fetchUsers(tags) {
        $.ajax({
            url: "{{ route('users.fetch') }}",
            method: 'GET',
            data: { tags: tags },
            success: function(response) {
                updateUsersTable(response);
            }
        });
    }

    function updateUsersTable(users) {
        const usersTableBody = $('#usersTableBody');
        usersTableBody.empty();
        let rowColor = true;

        users.forEach(function(user) {
            const statusText = user.is_vendor == 1 ? '<span>{{ app()->getLocale() === 'ar' ? 'تاجر' : 'Vendor' }}</span>' : '<span>{{ app()->getLocale() === 'ar' ? 'مستخدم' : 'user' }}</span>';
            const statusgender = user.gender == 'male' ? '<span>{{ app()->getLocale() === 'ar' ? 'ذكر' : 'male' }}</span>' : '<span>{{ app()->getLocale() === 'ar' ? 'انثى' : 'female' }}</span>';
            const rowClass = rowColor ? 'light-row' : 'dark-row';
            const userAge = calculateAge(user.birthday);

            usersTableBody.append(`
                <tr class="${rowClass} noBorder">
                    <td>${user.first_name} ${user.last_name}</td>
                    <td>${user.email}</td>
                    <td>${statusgender}</td>
                    <td>${userAge}</td>
                    <td>${user.birthday}</td>
                    <td>${user.region}</td>
                    <td>${user.city}</td>
                    <td>${user.mobile}</td>
                    <td>${statusText}</td>
                    <td>
                    <form method="GET" action="{{ route('users.edit', ['lang' => app()->getLocale()]) }}" style="display: inline;">
                        <input type="hidden" name="userid" value="${user.id}">
                        <input type="hidden" name="lang" value="{{ app()->getLocale() }}">
                        <button class="btn btn-primary" type="submit">{{ app()->getLocale() === 'ar' ? 'تعديل' : 'Manage' }}</button>
                    </form>
                </td>                </tr>
            `);

            rowColor = !rowColor;
        });
    }

    function calculateAge(birthday) {
        const birthDate = new Date(birthday);
        const currentDate = new Date();
        const age = currentDate.getFullYear() - birthDate.getFullYear();
        return age;
    }
</script>


@endsection


