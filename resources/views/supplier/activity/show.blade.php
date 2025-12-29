{{-- Supplier Activity Log - Show Details --}}
<x-dashboard.layout title="تفاصيل النشاط" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Breadcrumb --}}
    <div class="mb-8">
        <nav class="flex items-center gap-2 text-sm mb-4">
            <a href="{{ route('supplier.activity.index') }}" class="text-medical-gray-500 hover:text-medical-blue-600 font-semibold transition-colors">سجل النشاط</a>
            <svg class="w-4 h-4 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            <span class="text-medical-gray-900 font-bold">تفاصيل النشاط #{{ $activity->id }}</span>
        </nav>

        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display mb-2">تفاصيل النشاط</h1>
                <p class="text-base text-medical-gray-600">مراجعة شاملة للعملية والبيانات المرتبطة</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.activity.index') }}" class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة للسجل
                </a>
            </div>
        </div>
    </div>

    {{-- Activity Overview Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Log Type Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-medical-gray-500 uppercase tracking-wider">نوع السجل</p>
                        <p class="text-lg font-black text-medical-gray-900">{{ $activity->log_name_label }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Event Type Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center shadow-md">
                        <span class="text-2xl">{{ $activity->event_icon }}</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-medical-gray-500 uppercase tracking-wider">نوع العملية</p>
                        <p class="text-lg font-black text-medical-gray-900">{{ $activity->event_label }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Timestamp Card --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-medical-gray-500 uppercase tracking-wider">الوقت</p>
                        <p class="text-lg font-black text-medical-gray-900">{{ $activity->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-xs font-semibold text-medical-green-600">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Description Card --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-xl flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-medical-gray-900">وصف النشاط</h2>
                <p class="text-sm text-medical-gray-500">التفاصيل الكاملة للعملية التي تمت</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-medical-gray-50 to-white rounded-xl p-6 border-2 border-medical-gray-200">
            <p class="text-base text-medical-gray-900 leading-relaxed font-medium">{{ $activity->description }}</p>
        </div>
    </div>

    {{-- Properties/Details --}}
    @if($activity->properties && count($activity->properties) > 0)
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-medical-gray-900">تفاصيل إضافية</h2>
                    <p class="text-sm text-medical-gray-500">معلومات إضافية حول العملية</p>
                </div>
            </div>

            <div class="bg-gradient-to-br from-medical-gray-50 to-white rounded-xl p-6 border-2 border-medical-gray-200">
                <pre class="text-sm text-medical-gray-900 whitespace-pre-wrap font-mono">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
        </div>
    @endif

</x-dashboard.layout>

