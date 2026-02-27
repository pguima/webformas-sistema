@php
    $companyName = (string) (\App\Models\CompanySetting::current()?->company_name ?: config('app.name'));
@endphp

<tr>
<td class="footer">
<p style="font-size: 12px; color: #999;">
© {{ date('Y') }} {{ $companyName }}. {{ __('All rights reserved.') }}
</p>
</td>
</tr>
