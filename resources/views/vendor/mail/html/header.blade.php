@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<img src="{{ asset('assets/img/Caduceus Icon.png') }}" class="logo" alt="MedEquip Logo" width="120" height="120">
@else
{!! $slot !!}
@endif
</a>
</td>
</tr>
