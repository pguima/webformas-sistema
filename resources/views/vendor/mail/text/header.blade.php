@php
    $companyName = (string) (\App\Models\CompanySetting::current()?->company_name ?: config('app.name'));
@endphp

{{ $companyName }}

