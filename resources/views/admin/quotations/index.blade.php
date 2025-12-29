{{-- Admin Quotations - Index --}}
<x-dashboard.layout title="عروض الأسعار" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">عروض الأسعار</h1>
                <p class="mt-2 text-medical-gray-600">مراقبة وإدارة جميع عروض الأسعار المقدمة من الموردين</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.quotations.export', request()->all()) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>تصدير Excel</span>
                </a>
                <a href="{{ route('admin.quotations.create') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    <span>إنشاء عرض جديد</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
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
                    <p class="text-xs text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">مقبولة</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['accepted'] }}</p>
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
                    <p class="text-xs text-medical-gray-600">مرفوضة</p>
                    <p class="text-2xl font-bold text-medical-red-600 mt-1">{{ $stats['rejected'] }}</p>
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
                    <p class="text-xs text-medical-gray-600">إجمالي المقبولة</p>
                    <p class="text-2xl font-bold text-medical-purple-600 mt-1">{{ number_format($stats['total_value'], 0) }}</p>
                    <p class="text-xs text-medical-gray-500">د.ل</p>
                </div>
                <div class="w-10 h-10 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('admin.quotations.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث بالكود أو اسم المورد..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-40">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>

            <div class="w-48">
                <label for="supplier_id" class="block text-sm font-medium text-medical-gray-700 mb-2">المورد</label>
                <select name="supplier_id" id="supplier_id"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    @foreach($suppliers as $id => $name)
                        <option value="{{ $id }}" {{ request('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('admin.quotations.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Quotations List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($quotations->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد عروض أسعار</h3>
                <p class="mt-2 text-medical-gray-600">لم يتم تقديم أي عروض أسعار بعد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الكود / المورد
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                طلب عرض السعر
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                السعر
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                التوصيل / الضمان
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($quotations as $quotation)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-mono text-sm text-medical-blue-600">{{ $quotation->reference_code }}</p>
                                    <p class="font-semibold text-medical-gray-900 mt-1">{{ $quotation->supplier->company_name ?? 'غير محدد' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('admin.rfqs.show', $quotation->rfq) }}"
                                        class="font-medium text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ Str::limit($quotation->rfq->title ?? 'غير محدد', 30) }}
                                    </a>
                                    <p class="text-xs text-medical-gray-500 mt-1">{{ $quotation->rfq->reference_code ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-lg font-bold text-medical-green-600">{{ number_format($quotation->total_price, 2) }}</p>
                                    <p class="text-xs text-medical-gray-500">د.ل</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-medical-gray-600">
                                        @if($quotation->lead_time)
                                            <p>⏱️ {{ $quotation->lead_time }} يوم</p>
                                        @endif
                                        @if($quotation->warranty_period)
                                            <p>🛡️ {{ $quotation->warranty_period }} شهر</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-medical-gray-900">{{ $quotation->created_at->format('Y-m-d') }}</p>
                                    <p class="text-xs text-medical-gray-500">{{ $quotation->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                            'accepted' => 'bg-medical-green-100 text-medical-green-700',
                                            'rejected' => 'bg-medical-red-100 text-medical-red-700',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد المراجعة',
                                            'accepted' => 'مقبول',
                                            'rejected' => 'مرفوض',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$quotation->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('admin.quotations.show', $quotation) }}"
                                            class="inline-flex items-center px-3 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                        <a href="{{ route('admin.quotations.edit', $quotation) }}"
                                            class="inline-flex items-center px-3 py-2 bg-medical-green-50 text-medical-green-700 rounded-lg hover:bg-medical-green-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                {{ $quotations->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

