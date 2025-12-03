{{-- Admin Activity Log - Professional Enterprise Design --}}
<x-dashboard.layout title="سجل النشاط" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">سجل النشاط</h1>
                <p class="mt-3 text-base text-medical-gray-600">تتبع ومراقبة جميع العمليات والأنشطة في الوقت الفعلي
                </p>
            </div>
            <div class="flex items-center gap-3">
                {{-- Export Button --}}
                <button
                    class="px-5 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:border-medical-blue-500 hover:text-medical-blue-600 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>تصدير</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Activities --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-blue-600 bg-medical-blue-50 px-3 py-1 rounded-full">الكل</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الأنشطة</p>
                    <p class="text-4xl font-black text-medical-gray-900">{{ $stats['total'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Today's Activities --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-green-600 bg-medical-green-50 px-3 py-1 rounded-full">اليوم</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">أنشطة اليوم</p>
                    <p class="text-4xl font-black text-medical-green-600">{{ $stats['today'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- Active Users --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-purple-600 bg-medical-purple-50 px-3 py-1 rounded-full">نشط</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">المستخدمون النشطون</p>
                    <p class="text-4xl font-black text-medical-purple-600">{{ $stats['active_users'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        {{-- This Week --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-yellow-600 bg-medical-yellow-50 px-3 py-1 rounded-full">أسبوع</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">أنشطة الأسبوع</p>
                    <p class="text-4xl font-black text-medical-yellow-600">{{ $stats['this_week'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Filters --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center mb-6">
            <div
                class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md mr-4">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-bold text-medical-gray-900">فلاتر البحث المتقدم</h2>
                <p class="text-sm text-medical-gray-500">قم بتصفية النتائج حسب المعايير المطلوبة</p>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.activity') }}">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        البحث في النشاط
                    </label>
                    <input name="q" type="text" value="{{ request('q') }}"
                        placeholder="ابحث في الوصف أو العمليات..."
                        class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium placeholder:text-medical-gray-400">
                </div>

                {{-- Log Type --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-purple-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        نوع السجل
                    </label>
                    <select name="log_name"
                        class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium">
                        <option value="">🔹 جميع الأنواع</option>
                        <option value="suppliers" {{ request('log_name') == 'suppliers' ? 'selected' : '' }}>🏢
                            الموردين
                        </option>
                        <option value="buyers" {{ request('log_name') == 'buyers' ? 'selected' : '' }}>🏥 المشترين
                        </option>
                        <option value="products" {{ request('log_name') == 'products' ? 'selected' : '' }}>📦 المنتجات
                        </option>
                        <option value="orders" {{ request('log_name') == 'orders' ? 'selected' : '' }}>🛒 الطلبات
                        </option>
                        <option value="system" {{ request('log_name') == 'system' ? 'selected' : '' }}>⚙️ النظام
                        </option>
                        <option value="notifications" {{ request('log_name') == 'notifications' ? 'selected' : '' }}>
                            🔔 الإشعارات</option>
                    </select>
                </div>

                {{-- Date Range --}}
                <div>
                    <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        الفترة الزمنية
                    </label>
                    <select name="date_filter"
                        class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium">
                        <option value="">📅 جميع الأوقات</option>
                        <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>⏰ اليوم
                        </option>
                        <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>📆 هذا الأسبوع
                        </option>
                        <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>🗓️ هذا الشهر
                        </option>
                    </select>
                </div>

            </div>

            <div class="mt-6 flex justify-between items-center">
                <p class="text-sm text-medical-gray-500">
                    <span class="font-semibold">{{ $activities->total() }}</span> نتيجة
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('admin.activity') }}"
                        class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all font-semibold">
                        إعادة تعيين
                    </a>
                    <button type="submit"
                        class="px-8 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg hover:shadow-xl">
                        تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Activity Timeline --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 overflow-hidden">
        {{-- Table Header --}}
        <div
            class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100 px-8 py-5 border-b-2 border-medical-gray-200">
            <h2 class="text-lg font-black text-medical-gray-900 flex items-center gap-3">
                <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                السجل الزمني للأنشطة
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead
                    class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100 border-b-2 border-medical-gray-200">
                    <tr>
                        <th
                            class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">
                            التوقيت</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">
                            المستخدم</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">
                            العملية والوصف</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">
                            التصنيف</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest">
                            الإجراء</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-100 bg-white">
                    @forelse($activities as $activity)
                        <tr
                            class="hover:bg-gradient-to-r hover:from-medical-blue-50/30 hover:to-transparent transition-all duration-200 group">
                            {{-- Time with Timeline Indicator --}}
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-2 h-2 rounded-full bg-medical-blue-500 group-hover:bg-medical-blue-600 transition-colors">
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-medical-gray-900">
                                            {{ $activity->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="text-xs font-semibold text-medical-gray-500">
                                            {{ $activity->created_at->format('H:i:s') }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- User with Premium Avatar --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-all">
                                        <span class="text-base font-black text-white">
                                            {{ $activity->causer ? strtoupper(substr($activity->causer->name, 0, 1)) : 'S' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-medical-gray-900">
                                            {{ $activity->causer->name ?? 'النظام' }}
                                        </div>
                                        <div class="text-xs text-medical-gray-500">
                                            {{ $activity->causer->email ?? 'system@medequip.com' }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Activity Description with Event --}}
                            <td class="px-6 py-5">
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <span class="text-base">{{ $activity->event_icon }}</span>
                                        <span class="text-sm font-bold text-medical-gray-900">
                                            {{ $activity->event_label }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-medical-gray-600 leading-relaxed">
                                        {{ $activity->description }}
                                    </p>
                                    @if ($activity->created_at->diffInHours() < 24)
                                        <div
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-medical-green-600 bg-medical-green-50 px-2.5 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ $activity->created_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Type Badge with Premium Design --}}
                            <td class="px-6 py-5 whitespace-nowrap">
                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-black rounded-xl shadow-md bg-medical-{{ $activity->log_color }}-100 text-medical-{{ $activity->log_color }}-700 border-2 border-medical-{{ $activity->log_color }}-200">
                                    <span
                                        class="w-2.5 h-2.5 bg-medical-{{ $activity->log_color }}-600 rounded-full animate-pulse"></span>
                                    {{ $activity->log_name_label }}
                                </span>
                            </td>

                            {{-- Actions with Premium Button --}}
                            <td class="px-6 py-5 whitespace-nowrap">
                                <a href="{{ route('admin.activity.show', $activity) }}"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all shadow-md hover:shadow-lg font-bold text-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    تفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div
                                        class="w-24 h-24 bg-gradient-to-br from-medical-gray-100 to-medical-gray-200 rounded-full flex items-center justify-center mb-6 shadow-lg">
                                        <svg class="w-12 h-12 text-medical-gray-400" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="text-medical-gray-900 text-xl font-black mb-2">لا توجد أنشطة مسجلة</p>
                                    <p class="text-medical-gray-500 text-base">لم يتم العثور على أي سجلات نشاط مطابقة
                                        للمعايير المحددة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($activities->hasPages())
            <div class="px-8 py-5 border-t-2 border-medical-gray-200 bg-gradient-to-r from-medical-gray-50 to-white">
                {{ $activities->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>
