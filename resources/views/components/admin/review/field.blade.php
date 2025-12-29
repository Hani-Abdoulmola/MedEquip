@props(['label', 'value'])

<div class="py-3 border-b border-medical-gray-200">
    <dt class="text-sm font-medium text-medical-gray-600 mb-1">{{ $label }}</dt>
    <dd class="text-base text-medical-gray-900">
        @if($value)
            {{ $value }}
        @else
            <span class="text-medical-gray-400 italic">غير محدد</span>
        @endif
    </dd>
</div>

