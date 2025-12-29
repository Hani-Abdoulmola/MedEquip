{{-- Supplier Activity Log - Index --}}
<x-dashboard.layout title="سجل النشاط" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">سجل النشاط</h1>
                <p class="mt-3 text-base text-medical-gray-600">تتبع جميع عملياتك وأنشطتك في النظام</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.dashboard') }}"
                    class="px-5 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:border-medical-blue-500 hover:text-medical-blue-600 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Activities --}}
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-medical-blue-600 bg-medical-blue-50 px-3 py-1 rounded-full">الكل</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الأنشطة</p>
                    <p class="text-4xl font-black text-medical-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Today's Activities --}}
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-medical-green-600 bg-medical-green-50 px-3 py-1 rounded-full">اليوم</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">أنشطة اليوم</p>
                    <p class="text-4xl font-black text-medical-green-600">{{ $stats['today'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- This Week --}}
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-medical-yellow-600 bg-medical-yellow-50 px-3 py-1 rounded-full">أسبوع</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">أنشطة الأسبوع</p>
                    <p class="text-4xl font-black text-medical-yellow-600">{{ $stats['this_week'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- This Month --}}
        <div class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span class="text-xs font-bold text-medical-purple-600 bg-medical-purple-50 px-3 py-1 rounded-full">شهر</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">أنشطة الشهر</p>
                    <p class="text-4xl font-black text-medical-purple-600">{{ $stats['this_month'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-medical-gray-900">فلاتر البحث المتقدم</h2>
                <p class="text-sm text-medical-gray-500">قم بتصفية النتائج حسب المعايير المطلوبة</p>
            </div>
        </div>

        <form method="GET" action="{{ route('supplier.activity.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        البحث في النشاط
                    </label>
                    <input name="q" type="text" value="{{ request('q') }}" placeholder="ابحث في الوصف أو العمليات..."
                        class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium placeholder:text-medical-gray-400">
                </div>

                {{-- Log Type --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        نوع السجل
                    </label>
                    <select name="log_name" class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium">
                        <option value="">🔹 جميع الأنواع</option>
                        @foreach($logTypes as $logType)
                            <option value="{{ $logType }}" {{ request('log_name') == $logType ? 'selected' : '' }}>
                                {{ $logType }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        الفترة الزمنية
                    </label>
                    <select name="date_filter" class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium">
                        <option value="">📅 جميع الأوقات</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>⏰ اليوم</option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>📆 هذا الأسبوع</option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>🗓️ هذا الشهر</option>
                    </select>
                </div>
            </div>

            <div class="mt-6 flex justify-between items-center">
                <p class="text-sm text-medical-gray-500">
                    <span class="font-semibold">{{ $activities->total() }}</span> نتيجة
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('supplier.activity.index') }}" class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all font-semibold">
                        إعادة تعيين
                    </a>
                    <button type="submit" class="px-8 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg hover:shadow-xl">
                        تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Activity Timeline --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100 px-8 py-5 border-b-2 border-medical-gray-200">
            <h2 class="text-lg font-black text-medical-gray-900 flex items-center gap-3">
                <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                السجل الزمني للأنشطة
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">الوقت</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">النوع</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">الوصف</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">الحدث</th>
                        <th class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-100">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-medical-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-medical-gray-900">{{ $activity->created_at->format('Y-m-d H:i') }}</div>
                                <div class="text-xs text-medical-gray-500">{{ $activity->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-2 px-3 py-1 bg-medical-blue-100 text-medical-blue-700 rounded-lg text-xs font-bold">
                                    {{ $activity->log_name_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-medical-gray-900">{{ Str::limit($activity->description, 60) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1 px-3 py-1 bg-medical-purple-100 text-medical-purple-700 rounded-lg text-xs font-bold">
                                    <span>{{ $activity->event_icon }}</span>
                                    {{ $activity->event_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('supplier.activity.show', $activity) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-semibold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <svg class="w-16 h-16 text-medical-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-lg font-semibold text-medical-gray-500">لا توجد أنشطة مسجلة</p>
                                    <p class="text-sm text-medical-gray-400">سيتم عرض أنشطتك هنا عند قيامك بأي عملية</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="px-8 py-6 border-t border-medical-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

