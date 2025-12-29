{{-- Supplier Payment - Show Details --}}
<x-dashboard.layout title="تفاصيل الدفعة" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الدفعة</h1>
                <p class="mt-2 text-medical-gray-600 font-mono">{{ $payment->payment_reference ?? 'N/A' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.payments.index') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
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
            {{-- Payment Header --}}
            <div class="bg-white rounded-2xl shadow-medical p-8">
                <div class="flex items-start justify-between mb-8">
                    <div>
                        <h2 class="text-2xl font-bold text-medical-gray-900">دفعة مالية</h2>
                        <p class="font-mono text-xl text-medical-blue-600 mt-2">{{ $payment->payment_reference ?? 'N/A' }}</p>
                    </div>
                    <div class="text-left">
                        <p class="text-sm text-medical-gray-600">تاريخ الدفع</p>
                        <p class="font-semibold text-medical-gray-900">{{ $payment->paid_at ? $payment->paid_at->format('Y-m-d H:i') : 'غير محدد' }}</p>
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
                                    <p class="font-mono font-semibold text-medical-gray-900">{{ $payment->transaction_id }}</p>
                                </div>
                            @endif
                            @if($payment->processor)
                                <div>
                                    <p class="text-xs text-medical-gray-500">معالج بواسطة</p>
                                    <p class="font-semibold text-medical-gray-900">{{ $payment->processor->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Related Information --}}
                    <div>
                        <h3 class="text-sm font-semibold text-medical-gray-600 mb-3">معلومات مرتبطة</h3>
                        <div class="space-y-2">
                            @if($payment->order)
                                <div>
                                    <p class="text-xs text-medical-gray-500">الطلب</p>
                                    <a href="{{ route('supplier.orders.show', $payment->order) }}" class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ $payment->order->order_number }}
                                    </a>
                                </div>
                            @endif
                            @if($payment->invoice)
                                <div>
                                    <p class="text-xs text-medical-gray-500">الفاتورة</p>
                                    <a href="{{ route('supplier.invoices.show', $payment->invoice) }}" class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                </div>
                            @endif
                            @if($payment->buyer)
                                <div>
                                    <p class="text-xs text-medical-gray-500">المشتري</p>
                                    <p class="font-semibold text-medical-gray-900">{{ $payment->buyer->organization_name }}</p>
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
                    <p class="text-medical-gray-600 whitespace-pre-line">{{ $payment->notes }}</p>
                </div>
            @endif

            {{-- Payment Receipts --}}
            @if($receipts->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        إيصالات الدفع
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($receipts as $receipt)
                            <div class="border border-medical-gray-200 rounded-xl p-4 hover:shadow-md transition-shadow">
                                <div class="flex items-center justify-between mb-2">
                                    <p class="text-sm font-medium text-medical-gray-900">{{ $receipt->name }}</p>
                                    <a href="{{ $receipt->getUrl() }}" target="_blank"
                                        class="text-medical-blue-600 hover:text-medical-blue-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                </div>
                                @if($receipt->mime_type && str_starts_with($receipt->mime_type, 'image/'))
                                    <img src="{{ $receipt->getUrl('preview') }}" alt="{{ $receipt->name }}"
                                        class="w-full h-48 object-cover rounded-lg">
                                @else
                                    <div class="w-full h-48 bg-medical-gray-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-12 h-12 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <p class="text-xs text-medical-gray-500 mt-2">{{ $receipt->human_readable_size }}</p>
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
                    حالة الدفعة
                </h3>

                <div class="space-y-4">
                    {{-- Payment Status --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">الحالة</p>
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
                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClasses[$payment->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                            {{ $statusLabels[$payment->status] ?? $payment->status }}
                        </span>
                    </div>

                    {{-- Payment Method --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">طريقة الدفع</p>
                        <p class="font-semibold text-medical-gray-900">
                            {{ $methodLabels[$payment->method] ?? $payment->method }}
                        </p>
                    </div>

                    {{-- Currency --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">العملة</p>
                        <p class="font-semibold text-medical-gray-900">{{ $payment->currency }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Info --}}
            @if($payment->order)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        معلومات الطلب
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                            <a href="{{ route('supplier.orders.show', $payment->order) }}"
                                class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                                {{ $payment->order->order_number }}
                            </a>
                        </div>

                        <div>
                            <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->order->order_date?->format('Y-m-d') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-medical-gray-600">إجمالي الطلب</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ number_format($payment->order->total_amount, 2) }} د.ل</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Invoice Info --}}
            @if($payment->invoice)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        معلومات الفاتورة
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-medical-gray-600">رقم الفاتورة</p>
                            <a href="{{ route('supplier.invoices.show', $payment->invoice) }}"
                                class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                                {{ $payment->invoice->invoice_number }}
                            </a>
                        </div>

                        <div>
                            <p class="text-sm text-medical-gray-600">تاريخ الفاتورة</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->invoice->invoice_date?->format('Y-m-d') }}</p>
                        </div>

                        <div>
                            <p class="text-sm text-medical-gray-600">إجمالي الفاتورة</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ number_format($payment->invoice->total_amount, 2) }} د.ل</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Buyer Info --}}
            @if($payment->buyer)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        معلومات المشتري
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-medical-gray-600">اسم المؤسسة</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->buyer->organization_name }}</p>
                        </div>

                        @if($payment->buyer->contact_email)
                            <div>
                                <p class="text-sm text-medical-gray-600">البريد الإلكتروني</p>
                                <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->buyer->contact_email }}</p>
                            </div>
                        @endif

                        @if($payment->buyer->contact_phone)
                            <div>
                                <p class="text-sm text-medical-gray-600">رقم الهاتف</p>
                                <p class="font-semibold text-medical-gray-900 mt-1">{{ $payment->buyer->contact_phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dashboard.layout>

