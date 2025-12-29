{{-- Admin Invoices Management - View Invoice Details --}}
<x-dashboard.layout title="تفاصيل الفاتورة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الفاتورة</h1>
                <p class="mt-2 text-medical-gray-600">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.invoices.edit', $invoice->id) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-yellow-600 text-white rounded-xl hover:bg-medical-yellow-700 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('admin.invoices.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Invoice Header --}}
            <div class="bg-white rounded-2xl shadow-medical p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-medical-gray-900">فاتورة</h2>
                        <p class="font-mono text-xl text-medical-blue-600 mt-2">{{ $invoice->invoice_number }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-medical-gray-600">تاريخ الإصدار</p>
                        <p class="font-semibold text-medical-gray-900">{{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-medical-gray-200">
                    {{-- From (Supplier) --}}
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-3">من:</p>
                        <p class="font-bold text-medical-gray-900">{{ $invoice->order->supplier->company_name ?? 'غير محدد' }}</p>
                        <p class="text-medical-gray-600 mt-1">{{ $invoice->order->supplier->address ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->supplier->contact_email ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->supplier->contact_phone ?? '' }}</p>
                    </div>

                    {{-- To (Buyer) --}}
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-3">إلى:</p>
                        <p class="font-bold text-medical-gray-900">{{ $invoice->order->buyer->organization_name ?? 'غير محدد' }}</p>
                        <p class="text-medical-gray-600 mt-1">{{ $invoice->order->buyer->address ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->buyer->contact_email ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->buyer->contact_phone ?? '' }}</p>
                    </div>
                </div>
            </div>

            {{-- Invoice Items --}}
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                <div class="p-6 border-b border-medical-gray-200">
                    <h3 class="text-lg font-semibold text-medical-gray-900">عناصر الفاتورة</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-medical-gray-200">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">المنتج</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">سعر الوحدة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-medical-gray-200">
                            @forelse($invoice->order->items ?? [] as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-medical-gray-900">{{ $item->product?->name ?? $item->item_name ?? 'منتج' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-medical-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-medical-gray-900">{{ number_format($item->unit_price, 2) }} د.ل</td>
                                    <td class="px-6 py-4 font-semibold text-medical-gray-900">{{ number_format($item->total_price, 2) }} د.ل</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-medical-gray-500">
                                        لا توجد عناصر
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Totals --}}
                <div class="p-6 bg-medical-gray-50">
                    <div class="max-w-xs mr-auto space-y-3">
                        <div class="flex justify-between text-medical-gray-600">
                            <span>المجموع الفرعي:</span>
                            <span>{{ number_format($invoice->subtotal, 2) }} د.ل</span>
                        </div>
                        @if($invoice->tax > 0)
                            <div class="flex justify-between text-medical-gray-600">
                                <span>الضريبة:</span>
                                <span>{{ number_format($invoice->tax, 2) }} د.ل</span>
                            </div>
                        @endif
                        @if($invoice->discount > 0)
                            <div class="flex justify-between text-medical-green-600">
                                <span>الخصم:</span>
                                <span>-{{ number_format($invoice->discount, 2) }} د.ل</span>
                            </div>
                        @endif
                        <div class="flex justify-between pt-3 border-t border-medical-gray-300 text-lg font-bold text-medical-gray-900">
                            <span>الإجمالي:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }} د.ل</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($invoice->notes)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">ملاحظات</h3>
                    <p class="text-medical-gray-600">{{ $invoice->notes }}</p>
                </div>
            @endif

            {{-- Payments --}}
            @if($invoice->payments->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        المدفوعات
                    </h3>

                    <div class="space-y-3">
                        @foreach($invoice->payments as $payment)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $payment->payment_reference ?? 'دفعة' }}</p>
                                    <p class="text-sm text-medical-gray-500">{{ $payment->paid_at?->format('Y-m-d') }}</p>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-medical-green-600">{{ number_format($payment->amount, 2) }} {{ $payment->currency }}</p>
                                    <p class="text-xs text-medical-gray-500">{{ $payment->method ?? 'غير محدد' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    حالة الفاتورة
                </h3>

                <div class="space-y-4">
                    {{-- Invoice Status --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">حالة الفاتورة</p>
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
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClasses[$invoice->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                            {{ $statusLabels[$invoice->status] ?? $invoice->status }}
                        </span>
                    </div>

                    {{-- Payment Status --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">حالة الدفع</p>
                        @php
                            $paymentClasses = [
                                'paid' => 'bg-medical-green-100 text-medical-green-700',
                                'partial' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                'unpaid' => 'bg-medical-red-100 text-medical-red-700',
                            ];
                            $paymentLabels = [
                                'paid' => 'مدفوعة بالكامل',
                                'partial' => 'مدفوعة جزئياً',
                                'unpaid' => 'غير مدفوعة',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $paymentClasses[$invoice->payment_status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                            {{ $paymentLabels[$invoice->payment_status] ?? $invoice->payment_status }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات الطلب
                </h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                        <a href="{{ route('admin.orders.show', $invoice->order_id) }}"
                            class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                            {{ $invoice->order->order_number ?? 'N/A' }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $invoice->order->order_date?->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>

            {{-- Amounts Summary --}}
            <div class="bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl shadow-medical p-6 text-white">
                <h3 class="text-lg font-semibold mb-4 pb-3 border-b border-white/20">
                    ملخص المبالغ
                </h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-white/80">المجموع الفرعي:</span>
                        <span class="font-semibold">{{ number_format($invoice->subtotal, 2) }} د.ل</span>
                    </div>
                    @if($invoice->tax > 0)
                        <div class="flex justify-between">
                            <span class="text-white/80">الضريبة:</span>
                            <span class="font-semibold">{{ number_format($invoice->tax, 2) }} د.ل</span>
                        </div>
                    @endif
                    @if($invoice->discount > 0)
                        <div class="flex justify-between">
                            <span class="text-white/80">الخصم:</span>
                            <span class="font-semibold">-{{ number_format($invoice->discount, 2) }} د.ل</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-white/20 text-xl font-bold">
                        <span>الإجمالي:</span>
                        <span>{{ number_format($invoice->total_amount, 2) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

