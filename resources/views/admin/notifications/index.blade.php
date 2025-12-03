{{-- Admin Notifications - Professional Design --}}
<x-dashboard.layout title="الإشعارات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Success Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 bg-gradient-to-r from-medical-green-500 to-medical-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="hover:bg-white/20 rounded-lg p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Premium Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">الإشعارات</h1>
                <p class="mt-3 text-base text-medical-gray-600">تتبع جميع التحديثات والأحداث المهمة</p>
            </div>
            <div class="flex items-center gap-3">
                <form action="{{ route('admin.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="px-5 py-3 bg-white border-2 border-medical-blue-300 text-medical-blue-700 rounded-xl hover:bg-medical-blue-50 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>تحديد الكل كمقروء</span>
                    </button>
                </form>

                <form action="{{ route('admin.notifications.destroy-all') }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف جميع الإشعارات؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="px-5 py-3 bg-white border-2 border-medical-red-300 text-medical-red-700 rounded-xl hover:bg-medical-red-50 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        <span>حذف الكل</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Stats Dashboard --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        {{-- Total Notifications --}}
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
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-blue-600 bg-medical-blue-50 px-3 py-1 rounded-full">الكل</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الإشعارات</p>
                    <p class="text-4xl font-black text-medical-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        {{-- Unread Notifications --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-red-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-red-500 to-medical-red-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-red-600 bg-medical-red-50 px-3 py-1 rounded-full">جديد</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">غير مقروءة</p>
                    <p class="text-4xl font-black text-medical-red-600">{{ $stats['unread'] }}</p>
                </div>
            </div>
        </div>

        {{-- Read Notifications --}}
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
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-green-600 bg-medical-green-50 px-3 py-1 rounded-full">مقروءة</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">مقروءة</p>
                    <p class="text-4xl font-black text-medical-green-600">{{ $stats['read'] }}</p>
                </div>
            </div>
        </div>

        {{-- Today's Notifications --}}
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
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-purple-600 bg-medical-purple-50 px-3 py-1 rounded-full">اليوم</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إشعارات اليوم</p>
                    <p class="text-4xl font-black text-medical-purple-600">{{ $stats['today'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6 mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-bold text-medical-gray-900">تصفية الإشعارات</h2>
                    <p class="text-sm text-medical-gray-500">اختر نوع الإشعارات المراد عرضها</p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('admin.notifications.index', ['filter' => 'all']) }}"
                    class="px-5 py-2.5 rounded-xl font-bold transition-all {{ $filter === 'all' ? 'bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white shadow-lg' : 'bg-medical-gray-100 text-medical-gray-700 hover:bg-medical-gray-200' }}">
                    الكل
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'unread']) }}"
                    class="px-5 py-2.5 rounded-xl font-bold transition-all {{ $filter === 'unread' ? 'bg-gradient-to-r from-medical-red-500 to-medical-red-600 text-white shadow-lg' : 'bg-medical-gray-100 text-medical-gray-700 hover:bg-medical-gray-200' }}">
                    غير مقروءة
                </a>
                <a href="{{ route('admin.notifications.index', ['filter' => 'read']) }}"
                    class="px-5 py-2.5 rounded-xl font-bold transition-all {{ $filter === 'read' ? 'bg-gradient-to-r from-medical-green-500 to-medical-green-600 text-white shadow-lg' : 'bg-medical-gray-100 text-medical-gray-700 hover:bg-medical-gray-200' }}">
                    مقروءة
                </a>
            </div>
        </div>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 overflow-hidden">
        {{-- Table Header --}}
        <div
            class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100 px-8 py-5 border-b-2 border-medical-gray-200">
            <h2 class="text-lg font-black text-medical-gray-900 flex items-center gap-3">
                <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                قائمة الإشعارات
            </h2>
        </div>

        <div class="divide-y divide-medical-gray-100">
            @forelse($notifications as $notification)
                <div
                    class="p-6 hover:bg-gradient-to-r hover:from-medical-blue-50/30 hover:to-transparent transition-all duration-200 {{ $notification->read_at ? 'bg-white' : 'bg-medical-blue-50/20' }}">
                    <div class="flex items-start gap-4">
                        {{-- Icon --}}
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <h3 class="text-base font-bold text-medical-gray-900 mb-1">
                                        {{ $notification->data['title'] ?? 'إشعار جديد' }}
                                    </h3>
                                    <p class="text-sm text-medical-gray-600 leading-relaxed">
                                        {{ $notification->data['message'] ?? $notification->data['body'] ?? 'لا يوجد محتوى' }}
                                    </p>
                                    <div class="flex items-center gap-4 mt-3">
                                        <span class="text-xs text-medical-gray-500 flex items-center gap-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $notification->created_at->diffForHumans() }}
                                        </span>
                                        @if (!$notification->read_at)
                                            <span
                                                class="inline-flex items-center gap-1.5 text-xs font-bold text-medical-red-600 bg-medical-red-50 px-2.5 py-1 rounded-full">
                                                <span class="w-2 h-2 bg-medical-red-600 rounded-full animate-pulse"></span>
                                                جديد
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex items-center gap-2">
                                    @if (!$notification->read_at)
                                        <form action="{{ route('admin.notifications.read', $notification->id) }}"
                                            method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="p-2 text-medical-green-600 hover:bg-medical-green-50 rounded-lg transition-colors"
                                                title="تحديد كمقروء">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.notifications.destroy', $notification->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-20 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div
                            class="w-24 h-24 bg-gradient-to-br from-medical-gray-100 to-medical-gray-200 rounded-full flex items-center justify-center mb-6 shadow-lg">
                            <svg class="w-12 h-12 text-medical-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>
                        <p class="text-medical-gray-900 text-xl font-black mb-2">لا توجد إشعارات</p>
                        <p class="text-medical-gray-500 text-base">لم يتم العثور على أي إشعارات
                            {{ $filter === 'unread' ? 'غير مقروءة' : ($filter === 'read' ? 'مقروءة' : '') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($notifications->hasPages())
            <div class="px-8 py-5 border-t-2 border-medical-gray-200 bg-gradient-to-r from-medical-gray-50 to-white">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>
