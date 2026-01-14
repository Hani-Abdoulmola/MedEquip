{{-- Buyer Invoices - Show --}}
<x-dashboard.layout title="تفاصيل الفاتورة" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('buyer.invoices.index') }}" class="text-medical-gray-500 hover:text-medical-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-medical-gray-900">فاتورة #{{ $invoice->invoice_number }}</h1>
        </div>
        <a href="{{ route('buyer.invoices.download', $invoice) }}" class="px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            تحميل PDF
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Invoice Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold mb-4">معلومات الفاتورة</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الفاتورة</p>
                        <p class="font-semibold mt-1">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الفاتورة</p>
                        <p class="font-semibold mt-1">{{ $invoice->invoice_date?->format('Y/m/d') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي المبلغ</p>
                        <p class="font-bold text-medical-green-600 text-xl mt-1">{{ number_format($invoice->total_amount, 2) }} د.ل</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">حالة الدفع</p>
                        @php
                            $colors = ['paid' => 'text-green-600', 'unpaid' => 'text-red-600', 'partial' => 'text-yellow-600'];
                            $labels = ['paid' => 'مدفوعة', 'unpaid' => 'غير مدفوعة', 'partial' => 'مدفوعة جزئياً'];
                        @endphp
                        <p class="font-semibold mt-1 {{ $colors[$invoice->payment_status] ?? '' }}">{{ $labels[$invoice->payment_status] ?? $invoice->payment_status }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            @if($invoice->order && $invoice->order->items->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                    <div class="p-6 border-b"><h2 class="text-lg font-semibold">بنود الطلب</h2></div>
                    <table class="w-full">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">السعر</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-medical-gray-100">
                            @foreach($invoice->order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 font-semibold">{{ $item->item_name ?? $item->product?->name }}</td>
                                    <td class="px-6 py-4">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="px-6 py-4">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 font-semibold">{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-medical-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 font-bold">الإجمالي</td>
                                <td class="px-6 py-4 font-bold text-medical-green-600">{{ number_format($invoice->total_amount, 2) }} د.ل</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold mb-4">المورد</h2>
                @if($invoice->order?->supplier)
                    <p class="font-semibold">{{ $invoice->order->supplier->company_name }}</p>
                    <p class="text-sm text-medical-gray-600 mt-1">{{ $invoice->order->supplier->contact_email }}</p>
                    <p class="text-sm text-medical-gray-600">{{ $invoice->order->supplier->contact_phone }}</p>
                @endif
            </div>

            {{-- Order Link --}}
            @if($invoice->order)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-lg font-semibold mb-4">الطلب المرتبط</h2>
                    <a href="{{ route('buyer.orders.show', $invoice->order) }}" class="text-medical-blue-600 hover:text-medical-blue-700 font-semibold">
                        {{ $invoice->order->order_number }}
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

