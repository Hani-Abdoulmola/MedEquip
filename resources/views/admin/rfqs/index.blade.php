{{-- Admin RFQs - Index --}}
<x-dashboard.layout title="طلبات عروض الأسعار" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">طلبات عروض الأسعار (RFQs)</h1>
                <p class="mt-2 text-medical-gray-600">مراقبة وإدارة جميع طلبات عروض الأسعار في النظام</p>
            </div>
            <a href="{{ route('admin.rfqs.create') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span>إنشاء طلب جديد</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-7 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">الإجمالي</p>
                    <p class="text-2xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">مفتوحة</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['open'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">مغلقة</p>
                    <p class="text-2xl font-bold text-medical-gray-600 mt-1">{{ $stats['closed'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-gray-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">تم الترسية</p>
                    <p class="text-2xl font-bold text-medical-purple-600 mt-1">{{ $stats['awarded'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">ملغية</p>
                    <p class="text-2xl font-bold text-medical-red-600 mt-1">{{ $stats['cancelled'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">عروض الأسعار</p>
                    <p class="text-2xl font-bold text-medical-blue-600 mt-1">{{ $stats['total_quotations'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending_quotations'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('admin.rfqs.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث بالعنوان أو الكود..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-40">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                    <option value="awarded" {{ request('status') == 'awarded' ? 'selected' : '' }}>تم الترسية</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>

            <div class="w-48">
                <label for="buyer_id" class="block text-sm font-medium text-medical-gray-700 mb-2">المشتري</label>
                <select name="buyer_id" id="buyer_id"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    @foreach($buyers as $id => $name)
                        <option value="{{ $id }}" {{ request('buyer_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="w-40">
                <label for="visibility" class="block text-sm font-medium text-medical-gray-700 mb-2">الرؤية</label>
                <select name="visibility" id="visibility"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="public" {{ request('visibility') == 'public' ? 'selected' : '' }}>عام</option>
                    <option value="private" {{ request('visibility') == 'private' ? 'selected' : '' }}>خاص</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('admin.rfqs.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- RFQs List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($rfqs->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد طلبات</h3>
                <p class="mt-2 text-medical-gray-600">لم يتم إنشاء أي طلبات عروض أسعار بعد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الكود / العنوان
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المشتري
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                العناصر
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                العروض
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الموعد النهائي
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الرؤية
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($rfqs as $rfq)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-mono text-sm text-medical-blue-600">{{ $rfq->reference_code }}</p>
                                    <p class="font-semibold text-medical-gray-900 mt-1">{{ Str::limit($rfq->title, 40) }}</p>
                                    <p class="text-xs text-medical-gray-500">{{ $rfq->created_at->format('Y-m-d') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-medical-gray-900">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 bg-medical-blue-50 text-medical-blue-700 rounded-full text-sm font-medium">
                                        {{ $rfq->items->count() }} عنصر
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="px-3 py-1 bg-medical-green-50 text-medical-green-700 rounded-full text-sm font-medium">
                                            {{ $rfq->quotations->count() }} عرض
                                        </span>
                                        @if($rfq->assignedSuppliers->count() > 0)
                                            <span class="px-3 py-1 bg-medical-purple-50 text-medical-purple-700 rounded-full text-sm font-medium">
                                                {{ $rfq->assignedSuppliers->count() }} مورد
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($rfq->deadline)
                                        <p class="text-medical-gray-900">{{ $rfq->deadline->format('Y-m-d') }}</p>
                                        <p class="text-xs {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-medical-gray-500' }}">
                                            {{ $rfq->deadline->diffForHumans() }}
                                        </p>
                                    @else
                                        <span class="text-medical-gray-400">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'open' => 'bg-medical-green-100 text-medical-green-700',
                                            'closed' => 'bg-medical-gray-100 text-medical-gray-700',
                                            'awarded' => 'bg-medical-purple-100 text-medical-purple-700',
                                            'cancelled' => 'bg-medical-red-100 text-medical-red-700',
                                        ];
                                        $statusLabels = [
                                            'open' => 'مفتوح',
                                            'closed' => 'مغلق',
                                            'awarded' => 'تم الترسية',
                                            'cancelled' => 'ملغي',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$rfq->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($rfq->is_public)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-medical-blue-100 text-medical-blue-700">
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                            </svg>
                                            عام
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                            خاص
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.rfqs.show', $rfq) }}"
                                            class="inline-flex items-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                        <a href="{{ route('admin.rfqs.edit', $rfq) }}"
                                            class="inline-flex items-center px-4 py-2 bg-medical-green-50 text-medical-green-700 rounded-lg hover:bg-medical-green-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            تعديل
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $rfqs->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

