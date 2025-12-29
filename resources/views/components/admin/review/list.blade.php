@props(['title', 'items'])

<div class="bg-white rounded-xl border border-medical-gray-200 p-6 mb-6">
    <h3 class="text-lg font-semibold text-medical-gray-900 mb-3 pb-3 border-b border-medical-gray-200">
        {{ $title }}
    </h3>
    @if(is_array($items) && count($items) > 0)
        <ul class="space-y-2">
            @foreach($items as $item)
                <li class="flex items-start">
                    <svg class="w-5 h-5 text-medical-blue-600 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-medical-gray-700">{{ $item }}</span>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-medical-gray-500 italic">لا توجد عناصر</p>
    @endif
</div>

