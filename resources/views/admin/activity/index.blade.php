{{-- Admin Activity Log - List All Activities --}}
<x-dashboard.layout title="سجل النشاط" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- 
    ADMIN ACTIVITY LOG INDEX PAGE
    Controller: ActivityLogController@index
    Data: $activities = Activity::with(['causer'])->latest()->paginate(25);
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">سجل النشاط</h1>
                <p class="mt-2 text-medical-gray-600">عرض جميع الأنشطة والعمليات في النظام</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        {{-- Total Activities Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الأنشطة</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $activities->total() }}</p>
                    <p class="text-xs text-medical-gray-500 mt-1">جميع السجلات</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Today's Activities Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">أنشطة اليوم</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ $activities->where('created_at', '>=', today())->count() }}</p>
                    <p class="text-xs text-medical-gray-500 mt-1">منذ منتصف الليل</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active Users Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">المستخدمون النشطون</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                        {{ $activities->pluck('causer_id')->unique()->count() }}</p>
                    <p class="text-xs text-medical-gray-500 mt-1">مستخدم فريد</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- This Week Card --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">أنشطة هذا الأسبوع</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">
                        {{ $activities->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
                    <p class="text-xs text-medical-gray-500 mt-1">آخر 7 أيام</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-gray-100 to-medical-gray-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-2xl shadow-medical mb-6" x-data="{ showFilters: false }">
        <div class="p-6 border-b border-medical-gray-200">
            <button @click="showFilters = !showFilters" class="flex items-center justify-between w-full text-right">
                <div class="flex items-center space-x-3 space-x-reverse">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                    <h3 class="text-lg font-bold text-medical-gray-900">الفلاتر والبحث</h3>
                </div>
                <svg class="w-5 h-5 text-medical-gray-600 transition-transform duration-200"
                    :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>

        <div x-show="showFilters" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform -translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0" class="p-6">
            <form method="GET" action="{{ route('admin.activity') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Search Input --}}
                <div>
                    <label for="q" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}"
                        placeholder="ابحث في الوصف..."
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                </div>

                {{-- Log Name Filter --}}
                <div>
                    <label for="log_name" class="block text-sm font-medium text-medical-gray-700 mb-2">نوع السجل</label>
                    <select id="log_name" name="log_name"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                        <option value="">الكل</option>
                        <option value="default" {{ request('log_name') == 'default' ? 'selected' : '' }}>افتراضي
                        </option>
                    </select>
                </div>

                {{-- Filter Buttons --}}
                <div class="flex items-end space-x-3 space-x-reverse">
                    <button type="submit"
                        class="flex-1 px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.activity') }}"
                        class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Activity Log Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                    <tr>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-medical-gray-600 uppercase tracking-wider">
                            الوقت</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-medical-gray-600 uppercase tracking-wider">
                            المستخدم</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-medical-gray-600 uppercase tracking-wider">
                            النشاط</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-medical-gray-600 uppercase tracking-wider">
                            النوع</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-medium text-medical-gray-600 uppercase tracking-wider">
                            الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-medical-gray-900">{{ $activity->created_at->format('Y-m-d') }}
                                </div>
                                <div class="text-xs text-medical-gray-500">{{ $activity->created_at->format('H:i:s') }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-medical-blue-100 rounded-full flex items-center justify-center ml-3">
                                        <span class="text-sm font-medium text-medical-blue-600">
                                            {{ $activity->causer ? substr($activity->causer->name, 0, 1) : 'N' }}
                                        </span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-medical-gray-900">
                                            {{ $activity->causer->name ?? 'النظام' }}
                                        </div>
                                        <div class="text-xs text-medical-gray-500">
                                            {{ $activity->causer->email ?? 'system@medequip1.com' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-medical-gray-900">{{ $activity->description }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="px-3 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-medical-blue-100 text-medical-blue-800">
                                    {{ $activity->log_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('admin.activity.show', $activity) }}"
                                    class="text-medical-blue-600 hover:text-medical-blue-900 font-medium">
                                    عرض التفاصيل
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-medical-gray-500 text-lg">لا توجد أنشطة مسجلة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($activities->hasPages())
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-dashboard.layout>
