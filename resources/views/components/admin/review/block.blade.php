@props(['title'])

<div class="bg-white rounded-xl border border-medical-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-medical-gray-900 mb-3 pb-3 border-b border-medical-gray-200">
        {{ $title }}
    </h3>
    <div class="text-medical-gray-700 leading-relaxed whitespace-pre-wrap">
        {{ $slot }}
    </div>
</div>

