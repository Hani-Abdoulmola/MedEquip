{{-- Dashboard Quick Actions Component - Matches Landing Page Design Quality --}}
@props([
    'title' => 'إجراءات سريعة',
    'actions' => []
])

<div class="bg-white rounded-2xl p-6 shadow-medical">
    {{-- Header --}}
    <h3 class="text-lg font-bold text-medical-gray-900 mb-6 font-display">{{ $title }}</h3>

    {{-- Actions Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($actions as $index => $action)
            <a href="{{ $action['url'] ?? '#' }}"
                class="flex items-center space-x-4 space-x-reverse p-4 bg-gradient-to-br {{ $action['gradient'] ?? 'from-medical-blue-50 to-medical-blue-100' }} rounded-xl hover:shadow-medical transition-all duration-300 group animate-fade-in-up"
                style="animation-delay: {{ $index * 0.1 }}s;">
                
                {{-- Icon --}}
                <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-medical group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-6 h-6 {{ $action['iconColor'] ?? 'text-medical-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $action['icon'] }}" />
                    </svg>
                </div>

                {{-- Content --}}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold {{ $action['textColor'] ?? 'text-medical-blue-700' }} mb-1">{{ $action['title'] }}</p>
                    @if (isset($action['description']))
                        <p class="text-xs {{ $action['descColor'] ?? 'text-medical-blue-600' }}">{{ $action['description'] }}</p>
                    @endif
                </div>

                {{-- Arrow --}}
                <svg class="w-5 h-5 {{ $action['iconColor'] ?? 'text-medical-blue-600' }} group-hover:translate-x-1 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        @endforeach
    </div>
</div>

