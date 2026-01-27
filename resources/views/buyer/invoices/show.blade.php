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

            {{-- Invoice Actions --}}
            <div class="bg-white rounded-2xl shadow-medical p-6 space-y-3">
                <h2 class="text-lg font-semibold mb-4">إجراءات الفاتورة</h2>
                
                @if(!$invoice->acknowledged_at && $invoice->status === 'approved')
                    <form action="{{ route('buyer.invoices.acknowledge', $invoice) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            تأكيد استلام الفاتورة
                        </button>
                    </form>
                @endif

                @if($invoice->status === 'approved' && $invoice->payment_status === 'unpaid')
                    <form action="{{ route('buyer.invoices.dispute', $invoice) }}" method="POST" class="w-full" 
                          x-data="{ showDisputeModal: false }">
                        @csrf
                        <button type="button" @click="showDisputeModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            الاعتراض على الفاتورة
                        </button>
                        
                        {{-- Dispute Modal --}}
                        <div x-show="showDisputeModal" 
                             x-cloak
                             @click.away="showDisputeModal = false"
                             class="fixed inset-0 z-50 overflow-y-auto mt-8">
                            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showDisputeModal = false"></div>
                                <div class="relative bg-white rounded-lg px-4 pt-5 pb-4 shadow-xl max-w-md w-full">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">الاعتراض على الفاتورة</h3>
                                    <form action="{{ route('buyer.invoices.dispute', $invoice) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">سبب الاعتراض <span class="text-red-500">*</span></label>
                                            <textarea name="dispute_reason" rows="4" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500"
                                                placeholder="أدخل سبب الاعتراض..."></textarea>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" @click="showDisputeModal = false"
                                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                                إلغاء
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                                                إرسال الاعتراض
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if($invoice->status === 'approved')
                    <form action="{{ route('buyer.invoices.request-copy', $invoice) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-blue-50 text-medical-blue-600 rounded-xl hover:bg-medical-blue-100 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            طلب نسخة من الفاتورة
                        </button>
                    </form>
                @endif

                @if($invoice->acknowledged_at)
                    <div class="p-3 bg-green-50 rounded-xl border border-green-200">
                        <p class="text-sm text-green-700">
                            <strong>تم التأكيد:</strong> {{ $invoice->acknowledged_at->format('Y/m/d H:i') }}
                        </p>
                    </div>
                @endif

                @if($invoice->disputed_at)
                    <div class="p-3 bg-yellow-50 rounded-xl border border-yellow-200">
                        <p class="text-sm text-yellow-700">
                            <strong>تم الاعتراض:</strong> {{ $invoice->disputed_at->format('Y/m/d H:i') }}
                        </p>
                        @if($invoice->dispute_reason)
                            <p class="text-xs text-yellow-600 mt-1">{{ $invoice->dispute_reason }}</p>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-dashboard.layout>

