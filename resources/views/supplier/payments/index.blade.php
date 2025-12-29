{{-- Supplier Payments - Index --}}
<x-dashboard.layout title="المدفوعات" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">المدفوعات</h1>
                <p class="mt-2 text-medical-gray-600">جميع المدفوعات المستلمة</p>
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

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المدفوعات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المبلغ</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ number_format($stats['total_amount'], 2) }} د.ل</p>
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
                    <p class="text-sm text-medical-gray-600">مكتملة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-medical-gray-500 mt-1">{{ number_format($stats['completed_amount'], 2) }} د.ل</p>
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
                    <p class="text-sm text-medical-gray-600">قيد الانتظار</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Stats Row --}}
    @if($stats['failed'] > 0 || $stats['refunded'] > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            @if($stats['failed'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">فاشلة</p>
                            <p class="text-3xl font-bold text-medical-red-600 mt-1">{{ $stats['failed'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif

            @if($stats['refunded'] > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">مستردة</p>
                            <p class="text-3xl font-bold text-medical-blue-600 mt-1">{{ $stats['refunded'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('supplier.payments.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث بالرقم المرجعي أو رقم العملية..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                    <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                </select>
            </div>

            <div class="w-48">
                <label for="method" class="block text-sm font-medium text-medical-gray-700 mb-2">طريقة الدفع</label>
                <select name="method" id="method"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="cash" {{ request('method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                    <option value="bank_transfer" {{ request('method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                    <option value="credit_card" {{ request('method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمانية</option>
                    <option value="paypal" {{ request('method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                    <option value="other" {{ request('method') == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
            </div>

            <div class="w-40">
                <label for="currency" class="block text-sm font-medium text-medical-gray-700 mb-2">العملة</label>
                <select name="currency" id="currency"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="LYD" {{ request('currency') == 'LYD' ? 'selected' : '' }}>دينار ليبي</option>
                    <option value="USD" {{ request('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي</option>
                    <option value="EUR" {{ request('currency') == 'EUR' ? 'selected' : '' }}>يورو</option>
                </select>
            </div>

            <div class="w-40">
                <label for="date_from" class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-40">
                <label for="date_to" class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('supplier.payments.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Payments List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($payments->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد مدفوعات</h3>
                <p class="mt-2 text-medical-gray-600">لم يتم استلام أي مدفوعات بعد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المرجع
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المبلغ
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                طريقة الدفع
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                تاريخ الدفع
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الطلب/الفاتورة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($payments as $payment)
                            <tr class="hover:bg-medical-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-mono font-semibold text-medical-gray-900">
                                        {{ $payment->payment_reference ?? 'N/A' }}
                                    </div>
                                    @if($payment->transaction_id)
                                        <div class="text-xs text-medical-gray-500 mt-1">
                                            رقم العملية: {{ $payment->transaction_id }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-lg font-bold text-medical-green-600">
                                        {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $methodLabels = [
                                            'cash' => 'نقدي',
                                            'bank_transfer' => 'تحويل بنكي',
                                            'credit_card' => 'بطاقة ائتمانية',
                                            'paypal' => 'PayPal',
                                            'other' => 'أخرى',
                                        ];
                                    @endphp
                                    <span class="text-sm text-medical-gray-700">
                                        {{ $methodLabels[$payment->method] ?? $payment->method }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                            'completed' => 'bg-medical-green-100 text-medical-green-700',
                                            'failed' => 'bg-medical-red-100 text-medical-red-700',
                                            'refunded' => 'bg-medical-blue-100 text-medical-blue-700',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'completed' => 'مكتملة',
                                            'failed' => 'فاشلة',
                                            'refunded' => 'مستردة',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClasses[$payment->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $statusLabels[$payment->status] ?? $payment->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-medical-gray-600">
                                    {{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'غير محدد' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm">
                                        @if($payment->order)
                                            <a href="{{ route('supplier.orders.show', $payment->order) }}" class="text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                                {{ $payment->order->order_number }}
                                            </a>
                                        @endif
                                        @if($payment->invoice)
                                            <div class="text-xs text-medical-gray-500 mt-1">
                                                فاتورة: {{ $payment->invoice->invoice_number }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('supplier.payments.show', $payment) }}"
                                        class="text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</x-dashboard.layout>

