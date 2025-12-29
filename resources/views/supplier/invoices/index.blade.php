{{-- Supplier Invoices - Index --}}
<x-dashboard.layout title="الفواتير" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الفواتير</h1>
                <p class="mt-2 text-medical-gray-600">جميع الفواتير المرتبطة بطلباتك</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.invoices.export', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:border-medical-green-500 hover:text-medical-green-600 transition-all duration-200 font-semibold shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>تصدير Excel</span>
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

    @if (session('error'))
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="bg-medical-blue-50 border border-medical-blue-200 text-medical-blue-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الفواتير</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المبالغ</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ number_format($stats['total_amount'], 2) }} د.ل</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مدفوعة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['paid'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">غير مدفوعة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-1">{{ $stats['unpaid'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats Row --}}
    @if(isset($stats['partial']) && ($stats['partial'] > 0 || $stats['issued'] > 0 || $stats['approved'] > 0 || $stats['cancelled'] > 0))
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @if($stats['partial'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">مدفوعة جزئياً</p>
                            <p class="text-3xl font-bold text-medical-yellow-600 mt-1">{{ $stats['partial'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            @if($stats['issued'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">صادرة</p>
                            <p class="text-3xl font-bold text-medical-blue-600 mt-1">{{ $stats['issued'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            @if($stats['approved'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">معتمدة</p>
                            <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['approved'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            @if($stats['cancelled'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">ملغية</p>
                            <p class="text-3xl font-bold text-medical-red-600 mt-1">{{ $stats['cancelled'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('supplier.invoices.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث برقم الفاتورة أو رقم الطلب..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-40">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="issued" {{ request('status') == 'issued' ? 'selected' : '' }}>صادرة</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>معتمدة</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                </select>
            </div>

            <div class="w-40">
                <label for="payment_status" class="block text-sm font-medium text-medical-gray-700 mb-2">حالة الدفع</label>
                <select name="payment_status" id="payment_status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="partial" {{ request('payment_status') == 'partial' ? 'selected' : '' }}>جزئية</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                </select>
            </div>

            <div class="w-48">
                <label for="from_date" class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="to_date" class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('supplier.invoices.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Invoices List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($invoices->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد فواتير</h3>
                <p class="mt-2 text-medical-gray-600">ستظهر الفواتير هنا عند إصدارها.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                رقم الفاتورة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الطلب
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المشتري
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المبلغ
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الدفع
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                التاريخ
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($invoices as $invoice)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-mono font-semibold text-medical-gray-900">{{ $invoice->invoice_number }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('supplier.orders.show', $invoice->order) }}"
                                        class="text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                        {{ $invoice->order->order_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-medical-gray-900">{{ $invoice->order->buyer->organization_name ?? 'غير محدد' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-medical-green-600">{{ number_format($invoice->total_amount, 2) }} د.ل</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-medical-gray-100 text-medical-gray-700',
                                            'issued' => 'bg-medical-blue-100 text-medical-blue-700',
                                            'approved' => 'bg-medical-green-100 text-medical-green-700',
                                            'cancelled' => 'bg-medical-red-100 text-medical-red-700',
                                        ];
                                        $statusLabels = [
                                            'draft' => 'مسودة',
                                            'issued' => 'صادرة',
                                            'approved' => 'معتمدة',
                                            'cancelled' => 'ملغية',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$invoice->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $paymentClasses = [
                                            'paid' => 'bg-medical-green-100 text-medical-green-700',
                                            'partial' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                            'unpaid' => 'bg-medical-red-100 text-medical-red-700',
                                        ];
                                        $paymentLabels = [
                                            'paid' => 'مدفوعة',
                                            'partial' => 'جزئية',
                                            'unpaid' => 'غير مدفوعة',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $paymentClasses[$invoice->payment_status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $paymentLabels[$invoice->payment_status] ?? $invoice->payment_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-medical-gray-900">{{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('supplier.invoices.show', $invoice) }}"
                                            class="inline-flex items-center px-3 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                        <a href="{{ route('supplier.invoices.download', $invoice) }}"
                                            class="inline-flex items-center px-3 py-2 bg-medical-green-50 text-medical-green-700 rounded-lg hover:bg-medical-green-100 transition-colors duration-150 text-sm font-medium"
                                            title="تحميل PDF">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
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
                {{ $invoices->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

