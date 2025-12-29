{{-- Admin Payments Management - View Payment Details --}}
<x-dashboard.layout title="تفاصيل الدفعة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الدفعة</h1>
                <p class="mt-2 text-medical-gray-600 font-mono">{{ $payment->payment_reference ?? 'N/A' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.payments.edit', $payment->id) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-yellow-600 text-white rounded-xl hover:bg-medical-yellow-700 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('admin.payments.index') }}"
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Payment Header --}}
            <div class="bg-white rounded-2xl shadow-medical p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-medical-gray-900">دفعة مالية</h2>
                        <p class="font-mono text-xl text-medical-blue-600 mt-2">{{ $payment->payment_reference ?? 'N/A' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-medical-gray-600">تاريخ الدفع</p>
                        <p class="font-semibold text-medical-gray-900">{{ $payment->paid_at?->format('Y-m-d H:i') ?? 'غير محدد' }}</p>
                    </div>
                </div>

                {{-- Payment Amount --}}
                <div class="bg-medical-green-50 rounded-xl p-6 mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">المبلغ</p>
                    <p class="text-4xl font-black text-medical-green-600">
                        {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-medical-gray-200">
                    {{-- Payment Details --}}
                    <div>
                        <h3 class="text-sm font-semibold text-medical-gray-600 mb-3">تفاصيل الدفعة</h3>
                        <div class="space-y-2">
                            <div>
                                <p class="text-xs text-medical-gray-500">طريقة الدفع</p>
                                <p class="font-semibold text-medical-gray-900">
                                    @php
                                        $methodLabels = [
                                            'cash' => 'نقدي',
                                            'bank_transfer' => 'تحويل بنكي',
                                            'credit_card' => 'بطاقة ائتمانية',
                                            'paypal' => 'PayPal',
                                            'other' => 'أخرى',
                                        ];
                                    @endphp
                                    {{ $methodLabels[$payment->method] ?? $payment->method }}
                                </p>
                            </div>
                            @if($payment->transaction_id)
                                <div>
                                    <p class="text-xs text-medical-gray-500">رقم العملية</p>
                                    <p class="font-mono text-medical-gray-900">{{ $payment->transaction_id }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Related Entities --}}
                    <div>
                        <h3 class="text-sm font-semibold text-medical-gray-600 mb-3">الجهات المرتبطة</h3>
                        <div class="space-y-2">
                            @if($payment->invoice)
                                <div>
                                    <p class="text-xs text-medical-gray-500">الفاتورة</p>
                                    <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}"
                                        class="font-semibold text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </div>
                            @endif
                            @if($payment->order)
                                <div>
                                    <p class="text-xs text-medical-gray-500">الطلب</p>
                                    <a href="{{ route('admin.orders.show', $payment->order_id) }}"
                                        class="font-semibold text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ $payment->order->order_number }}
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if($payment->notes)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">ملاحظات</h3>
                    <p class="text-medical-gray-600">{{ $payment->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    حالة الدفعة
                </h3>
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'قيد الانتظار', 'color' => 'yellow'],
                        'completed' => ['label' => 'مكتملة', 'color' => 'green'],
                        'failed' => ['label' => 'فاشلة', 'color' => 'red'],
                        'refunded' => ['label' => 'مستردة', 'color' => 'gray'],
                    ];
                    $config = $statusConfig[$payment->status] ?? ['label' => $payment->status, 'color' => 'gray'];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-medical-{{ $config['color'] }}-100 text-medical-{{ $config['color'] }}-700">
                    {{ $config['label'] }}
                </span>
            </div>

            {{-- Buyer & Supplier Info --}}
            @if($payment->buyer || $payment->supplier)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        معلومات الأطراف
                    </h3>
                    <div class="space-y-3">
                        @if($payment->buyer)
                            <div>
                                <p class="text-sm text-medical-gray-600">المشتري</p>
                                <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->buyer->organization_name }}</p>
                            </div>
                        @endif
                        @if($payment->supplier)
                            <div>
                                <p class="text-sm text-medical-gray-600">المورد</p>
                                <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->supplier->company_name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

