{{-- Admin Activity Log - Professional Activity Details --}}
<x-dashboard.layout title="تفاصيل النشاط" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Premium Header with Breadcrumb --}}
    <div class="mb-8">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm mb-4">
            <a href="{{ route('admin.activity') }}"
                class="text-medical-gray-500 hover:text-medical-blue-600 font-semibold transition-colors">سجل النشاط</a>
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
                <button
                    class="px-5 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:border-medical-blue-500 hover:text-medical-blue-600 transition-all font-semibold shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>طباعة</span>
                </button>

                <a href="{{ route('admin.activity') }}"
                    class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7 7-7m-7 7h18" />
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
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
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
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center shadow-md">
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
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-medical-gray-500 uppercase tracking-wider">الوقت</p>
                        <p class="text-lg font-black text-medical-gray-900">
                            {{ $activity->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-xs font-semibold text-medical-green-600">
                            {{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Description Card --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div
                class="w-12 h-12 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-xl flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
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

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- User Information --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-medical-gray-900">المستخدم المسؤول</h2>
                    <p class="text-sm text-medical-gray-500">الحساب الذي قام بتنفيذ العملية</p>
                </div>
            </div>

            @if ($activity->causer)
                <div
                    class="bg-gradient-to-br from-medical-blue-50 to-white rounded-xl p-6 border-2 border-medical-blue-100">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-xl">
                            <span class="text-3xl font-black text-white">
                                {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-lg font-black text-medical-gray-900 mb-1">{{ $activity->causer->name }}</p>
                            <p class="text-sm font-semibold text-medical-gray-600 mb-2">{{ $activity->causer->email }}
                            </p>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-medical-green-100 text-medical-green-700 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 bg-medical-green-600 rounded-full"></span>
                                    مستخدم نشط
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div
                    class="bg-gradient-to-br from-medical-gray-50 to-white rounded-xl p-6 border-2 border-medical-gray-200">
                    <div class="flex items-center gap-4">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-medical-gray-500 to-medical-gray-600 rounded-2xl flex items-center justify-center shadow-xl">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-black text-medical-gray-900 mb-1">النظام الآلي</p>
                            <p class="text-sm font-semibold text-medical-gray-600 mb-2">system@medequip.com</p>
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-medical-purple-100 text-medical-purple-700 rounded-full text-xs font-bold">
                                    <span class="w-2 h-2 bg-medical-purple-600 rounded-full"></span>
                                    عملية تلقائية
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Subject Information --}}
        @if ($activity->subject)
            <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-black text-medical-gray-900">العنصر المتأثر</h2>
                        <p class="text-sm text-medical-gray-500">الكيان الذي تمت العملية عليه</p>
                    </div>
                </div>

                <div
                    class="bg-gradient-to-br from-medical-purple-50 to-white rounded-xl p-6 border-2 border-medical-purple-100">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b-2 border-medical-purple-100">
                            <span class="text-sm font-bold text-medical-gray-600">نوع الكيان</span>
                            <span class="text-base font-black text-medical-purple-700">
                                {{ class_basename($activity->subject_type) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between pb-3 border-b-2 border-medical-purple-100">
                            <span class="text-sm font-bold text-medical-gray-600">المعرف</span>
                            <span
                                class="px-4 py-1.5 bg-medical-purple-100 text-medical-purple-700 rounded-lg text-sm font-black">
                                #{{ $activity->subject->id }}
                            </span>
                        </div>

                        @if ($activity->subject->name ?? false)
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-bold text-medical-gray-600">الاسم</span>
                                <span class="text-base font-bold text-medical-gray-900">
                                    {{ $activity->subject->name }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Changes Details --}}
    @if (!empty($activity->properties['attributes']) || !empty($activity->properties['old']))
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <div class="flex items-center gap-3 mb-6">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-medical-gray-900">سجل التغييرات</h2>
                    <p class="text-sm text-medical-gray-500">مقارنة القيم القديمة والجديدة</p>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border-2 border-medical-gray-200">
                <table class="w-full">
                    <thead class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100">
                        <tr>
                            <th
                                class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest border-b-2 border-medical-gray-200">
                                الحقل
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest border-b-2 border-medical-gray-200">
                                القيمة القديمة
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-black text-medical-gray-700 uppercase tracking-widest border-b-2 border-medical-gray-200">
                                القيمة الجديدة
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-medical-gray-100">
                        @foreach ($activity->properties['attributes'] ?? [] as $field => $newValue)
                            <tr class="hover:bg-medical-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-medical-blue-100 text-medical-blue-700 rounded-lg font-black text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ $field }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-medical-red-600 rounded-full"></div>
                                        <span class="text-sm font-bold text-medical-red-700">
                                            {{ $activity->properties['old'][$field] ?? '—' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 bg-medical-green-600 rounded-full"></div>
                                        <span class="text-sm font-bold text-medical-green-700">
                                            {{ $newValue }}
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</x-dashboard.layout>
