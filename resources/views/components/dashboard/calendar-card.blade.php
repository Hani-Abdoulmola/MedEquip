{{-- Dashboard Calendar Card Component - Matches Landing Page Design Quality --}}
@props([
    'title' => 'التقويم',
    'events' => [],
    'calendarId' => 'calendar-' . uniqid(),
])

@php
    // Convert events to JSON for JavaScript
    $eventsJson = json_encode($events);
@endphp

<div class="bg-white rounded-2xl p-6 shadow-medical hover:shadow-medical-lg transition-all duration-300">
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-medical-gray-900 font-display">{{ $title }}</h3>
        <button onclick="document.getElementById('{{ $calendarId }}').dispatchEvent(new CustomEvent('today'))"
            class="text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">
            اليوم
        </button>
    </div>

    {{-- Calendar Container --}}
    <div id="{{ $calendarId }}" class="calendar-container"></div>

    {{-- Upcoming Events --}}
    <div class="mt-6 pt-6 border-t border-medical-gray-200">
        <h4 class="text-sm font-semibold text-medical-gray-900 mb-4">الأحداث القادمة</h4>
        <div class="space-y-3" id="{{ $calendarId }}-events">
            @if (count($events) > 0)
                @foreach (array_slice($events, 0, 3) as $event)
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-2 h-2 rounded-full {{ $event['color'] ?? 'bg-medical-blue-500' }} mt-2"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-medical-gray-900 truncate">{{ $event['title'] }}</p>
                            <p class="text-xs text-medical-gray-600">{{ $event['date'] }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-sm text-medical-gray-500 text-center py-4">لا توجد أحداث قادمة</p>
            @endif
        </div>
    </div>

    {{-- Additional Content Slot --}}
    @if ($slot->isNotEmpty())
        <div class="mt-6 pt-6 border-t border-medical-gray-200">
            {{ $slot }}
        </div>
    @endif
</div>

{{-- Inline Calendar Styles --}}
@once
    @push('styles')
    <style>
        .calendar-container {
            direction: rtl;
        }
        .calendar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }
        .calendar-nav-btn {
            padding: 0.5rem;
            border-radius: 0.5rem;
            color: #6b7280;
            transition: all 0.2s;
        }
        .calendar-nav-btn:hover {
            background-color: #f3f4f6;
            color: #0069af;
        }
        .calendar-month-year {
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
        }
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 0.25rem;
        }
        .calendar-day-header {
            text-align: center;
            font-size: 0.75rem;
            font-weight: 600;
            color: #6b7280;
            padding: 0.5rem 0;
        }
        .calendar-day {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.875rem;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
            color: #374151;
        }
        .calendar-day:hover {
            background-color: #f3f4f6;
        }
        .calendar-day.other-month {
            color: #d1d5db;
        }
        .calendar-day.today {
            background: linear-gradient(135deg, #0069af 0%, #199b69 100%);
            color: white;
            font-weight: 600;
        }
        .calendar-day.has-event {
            position: relative;
        }
        .calendar-day.has-event::after {
            content: '';
            position: absolute;
            bottom: 4px;
            left: 50%;
            transform: translateX(-50%);
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background-color: #0069af;
        }
        .calendar-day.today.has-event::after {
            background-color: white;
        }
    </style>
    @endpush
@endonce

{{-- Calendar JavaScript --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const calendarEl = document.getElementById('{{ $calendarId }}');
    if (!calendarEl) return;

    const events = {!! $eventsJson !!};
    let currentDate = new Date();

    const arabicMonths = [
        'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
        'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'
    ];
    const arabicDays = ['ح', 'ن', 'ث', 'ر', 'خ', 'ج', 'س'];

    function renderCalendar(date) {
        const year = date.getFullYear();
        const month = date.getMonth();
        const firstDay = new Date(year, month, 1);
        const lastDay = new Date(year, month + 1, 0);
        const prevLastDay = new Date(year, month, 0);
        
        const firstDayOfWeek = firstDay.getDay();
        const lastDate = lastDay.getDate();
        const prevLastDate = prevLastDay.getDate();

        let html = `
            <div class="calendar-header">
                <button class="calendar-nav-btn" onclick="document.getElementById('{{ $calendarId }}').dispatchEvent(new CustomEvent('next'))">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <div class="calendar-month-year">${arabicMonths[month]} ${year}</div>
                <button class="calendar-nav-btn" onclick="document.getElementById('{{ $calendarId }}').dispatchEvent(new CustomEvent('prev'))">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
            </div>
            <div class="calendar-grid">
        `;

        // Day headers
        arabicDays.forEach(day => {
            html += `<div class="calendar-day-header">${day}</div>`;
        });

        // Previous month days
        for (let i = firstDayOfWeek; i > 0; i--) {
            html += `<div class="calendar-day other-month">${prevLastDate - i + 1}</div>`;
        }

        // Current month days
        const today = new Date();
        for (let day = 1; day <= lastDate; day++) {
            const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
            const isToday = day === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const hasEvent = events.some(e => e.date === dateStr);
            
            let classes = 'calendar-day';
            if (isToday) classes += ' today';
            if (hasEvent) classes += ' has-event';
            
            html += `<div class="${classes}">${day}</div>`;
        }

        // Next month days
        const remainingDays = 42 - (firstDayOfWeek + lastDate);
        for (let day = 1; day <= remainingDays; day++) {
            html += `<div class="calendar-day other-month">${day}</div>`;
        }

        html += '</div>';
        calendarEl.innerHTML = html;
    }

    // Event listeners
    calendarEl.addEventListener('prev', () => {
        currentDate.setMonth(currentDate.getMonth() - 1);
        renderCalendar(currentDate);
    });

    calendarEl.addEventListener('next', () => {
        currentDate.setMonth(currentDate.getMonth() + 1);
        renderCalendar(currentDate);
    });

    calendarEl.addEventListener('today', () => {
        currentDate = new Date();
        renderCalendar(currentDate);
    });

    // Initial render
    renderCalendar(currentDate);
});
</script>
@endpush

