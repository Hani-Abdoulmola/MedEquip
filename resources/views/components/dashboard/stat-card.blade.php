{{-- Dashboard Stat Card Component - Matches Landing Page Design Quality --}}
@props([
    'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
    'label' => 'إجمالي',
    'value' => '0',
    'trend' => null, // 'up', 'down', or null
    'trendValue' => null,
    'color' => 'blue', // 'blue', 'green', 'red', 'gray'
    'loading' => false
])

@php
    $colorClasses = [
        'blue' => [
            'bg' => 'from-medical-blue-100 to-medical-blue-200',
            'icon' => 'text-medical-blue-600',
            'text' => 'text-medical-blue-600',
            'trend' => 'text-medical-blue-600',
        ],
        'green' => [
            'bg' => 'from-medical-green-100 to-medical-green-200',
            'icon' => 'text-medical-green-600',
            'text' => 'text-medical-green-600',
            'trend' => 'text-medical-green-600',
        ],
        'red' => [
            'bg' => 'from-medical-red-100 to-medical-red-200',
            'icon' => 'text-medical-red-600',
            'text' => 'text-medical-red-600',
            'trend' => 'text-medical-red-600',
        ],
        'gray' => [
            'bg' => 'from-medical-gray-100 to-medical-gray-200',
            'icon' => 'text-medical-gray-600',
            'text' => 'text-medical-gray-600',
            'trend' => 'text-medical-gray-600',
        ],
    ];
    
    $colors = $colorClasses[$color] ?? $colorClasses['blue'];
@endphp

<div class="bg-white rounded-2xl p-6 shadow-medical hover:shadow-medical-lg transition-all duration-300 animate-fade-in-up">
    @if ($loading)
        {{-- Loading Skeleton --}}
        <div class="animate-pulse">
            <div class="flex items-center justify-between mb-4">
                <div class="w-16 h-16 bg-medical-gray-200 rounded-2xl"></div>
            </div>
            <div class="h-4 bg-medical-gray-200 rounded w-1/2 mb-3"></div>
            <div class="h-8 bg-medical-gray-200 rounded w-3/4 mb-2"></div>
            <div class="h-3 bg-medical-gray-200 rounded w-1/3"></div>
        </div>
    @else
        {{-- Icon --}}
        <div class="flex items-center justify-between mb-4">
            <div class="w-16 h-16 bg-gradient-to-br {{ $colors['bg'] }} rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 {{ $colors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                </svg>
            </div>
            
            {{-- Trend Indicator --}}
            @if ($trend && $trendValue)
                <div class="flex items-center space-x-1 space-x-reverse px-2.5 py-1 rounded-full
                    {{ $trend === 'up' ? 'bg-medical-green-50 text-medical-green-600' : 'bg-medical-red-50 text-medical-red-600' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if ($trend === 'up')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                        @endif
                    </svg>
                    <span class="text-xs font-semibold">{{ $trendValue }}</span>
                </div>
            @endif
        </div>

        {{-- Label --}}
        <p class="text-sm text-medical-gray-600 mb-2 font-medium">{{ $label }}</p>

        {{-- Value --}}
        <p class="text-3xl font-bold text-medical-gray-900 mb-1 font-display">{{ $value }}</p>

        {{-- Additional Content Slot --}}
        @if ($slot->isNotEmpty())
            <div class="mt-3 pt-3 border-t border-medical-gray-100">
                {{ $slot }}
            </div>
        @endif
    @endif
</div>

