{{-- Dashboard Activity List Component - Matches Landing Page Design Quality --}}
@props([
    'title' => 'النشاطات الأخيرة',
    'activities' => [],
    'emptyMessage' => 'لا توجد نشاطات حديثة',
    'showViewAll' => true
])

<div class="bg-white rounded-2xl p-6 shadow-medical">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-medical-gray-900 font-display">{{ $title }}</h3>
        @if ($showViewAll && count($activities) > 0)
            <a href="#" class="text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium transition-colors duration-200">
                عرض الكل
            </a>
        @endif
    </div>

    {{-- Activities List --}}
    @if (count($activities) > 0)
        <div class="space-y-4">
            @foreach ($activities as $index => $activity)
                <div class="flex items-start space-x-4 space-x-reverse pb-4 {{ $index < count($activities) - 1 ? 'border-b border-medical-gray-100' : '' }} animate-fade-in-up"
                    style="animation-delay: {{ $index * 0.1 }}s;">
                    
                    {{-- Icon --}}
                    <div class="flex-shrink-0 w-10 h-10 bg-gradient-to-br {{ $activity['iconBg'] ?? 'from-medical-blue-100 to-medical-blue-200' }} rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 {{ $activity['iconColor'] ?? 'text-medical-blue-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $activity['icon'] }}" />
                        </svg>
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm text-medical-gray-900 font-medium mb-1">{{ $activity['title'] }}</p>
                        @if (isset($activity['description']))
                            <p class="text-xs text-medical-gray-600 mb-1">{{ $activity['description'] }}</p>
                        @endif
                        <p class="text-xs text-medical-gray-500">{{ $activity['time'] }}</p>
                    </div>

                    {{-- Badge (Optional) --}}
                    @if (isset($activity['badge']))
                        <span class="flex-shrink-0 px-2.5 py-1 text-xs font-semibold rounded-full {{ $activity['badgeClass'] ?? 'bg-medical-blue-50 text-medical-blue-600' }}">
                            {{ $activity['badge'] }}
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-medical-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-sm text-medical-gray-600">{{ $emptyMessage }}</p>
        </div>
    @endif
</div>

