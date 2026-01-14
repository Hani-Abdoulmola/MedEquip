{{-- Buyer Invoices - Index --}}
<x-dashboard.layout title="الفواتير" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الفواتير</h1>
        <p class="mt-2 text-medical-gray-600">عرض وإدارة الفواتير الخاصة بطلباتك</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">إجمالي الفواتير</p>
            <p class="text-2xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">مدفوعة</p>
            <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['paid'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">غير مدفوعة</p>
            <p class="text-2xl font-bold text-medical-red-600 mt-1">{{ $stats['unpaid'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">مدفوعة جزئياً</p>
            <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['partial'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">إجمالي المبلغ</p>
            <p class="text-lg font-bold text-medical-gray-900 mt-1">{{ number_format($stats['total_amount'], 2) }} د.ل</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الفاتورة..."
                    class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">حالة الدفع</label>
                <select name="payment_status" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
                    <option value="">الكل</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوعة</option>
                    <option value="unpaid" {{ request('payment_status') === 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                    <option value="partial" {{ request('payment_status') === 'partial' ? 'selected' : '' }}>مدفوعة جزئياً</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الفترة</label>
                <select name="date_filter" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
                    <option value="">الكل</option>
                    <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="last_month" {{ request('date_filter') === 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">بحث</button>
                <a href="{{ route('buyer.invoices.index') }}" class="px-4 py-2 bg-medical-gray-100 rounded-xl">إعادة تعيين</a>
            </div>
        </form>
    </div>

    {{-- Invoices Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if($invoices->isEmpty())
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-medical-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-medical-gray-900 mb-2">لا توجد فواتير</h3>
                <p class="text-medical-gray-600">ستظهر الفواتير هنا بعد إتمام الطلبات</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-medical-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">رقم الفاتورة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">المورد</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">المبلغ</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">حالة الدفع</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-100">
                    @foreach($invoices as $invoice)
                        <tr class="hover:bg-medical-gray-50">
                            <td class="px-6 py-4 font-semibold text-medical-blue-600">{{ $invoice->invoice_number }}</td>
                            <td class="px-6 py-4">{{ $invoice->order?->supplier?->company_name ?? 'غير معروف' }}</td>
                            <td class="px-6 py-4 font-semibold">{{ number_format($invoice->total_amount, 2) }} د.ل</td>
                            <td class="px-6 py-4">
                                @php
                                    $paymentColors = ['paid' => 'bg-green-100 text-green-800', 'unpaid' => 'bg-red-100 text-red-800', 'partial' => 'bg-yellow-100 text-yellow-800'];
                                    $paymentLabels = ['paid' => 'مدفوعة', 'unpaid' => 'غير مدفوعة', 'partial' => 'جزئياً'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $paymentColors[$invoice->payment_status] ?? 'bg-gray-100' }}">
                                    {{ $paymentLabels[$invoice->payment_status] ?? $invoice->payment_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-medical-gray-600">{{ $invoice->invoice_date?->format('Y/m/d') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('buyer.invoices.show', $invoice) }}" class="text-medical-blue-600 hover:text-medical-blue-700">عرض</a>
                                    <a href="{{ route('buyer.invoices.download', $invoice) }}" class="text-medical-green-600 hover:text-medical-green-700">تحميل</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-medical-gray-100">{{ $invoices->links() }}</div>
        @endif
    </div>

</x-dashboard.layout>

