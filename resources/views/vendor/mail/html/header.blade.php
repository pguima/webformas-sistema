@php
    $companyName = (string) (\App\Models\CompanySetting::current()?->company_name ?: config('app.name'));
@endphp

<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
{{ $companyName }}
</a>
</td>
</tr>
