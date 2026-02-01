{{-- Supplier Invoices - Show --}}
<x-dashboard.layout title="تفاصيل الفاتورة" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الفاتورة</h1>
                <p class="mt-2 text-medical-gray-600">{{ $invoice->invoice_number }}</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if ($invoice->status === \App\Models\Invoice::STATUS_ISSUED && auth()->user()->can('approve', $invoice))
                    <form action="{{ route('supplier.invoices.approve', $invoice) }}" method="POST" class="inline">
                        @csrf
                        {{-- <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-medium"
                            onclick="return confirm('هل أنت متأكد من اعتماد هذه الفاتورة؟')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>اعتماد</span>
                        </button> --}}
                    </form>
                @endif
                @if ($invoice->status !== \App\Models\Invoice::STATUS_CANCELLED && auth()->user()->can('cancel', $invoice))
                    {{-- <button type="button" onclick="showCancelModal()"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 transition-all duration-200 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span>إلغاء</span>
                    </button> --}}
                @endif
                @if ($invoice->status !== \App\Models\Invoice::STATUS_CANCELLED && auth()->user()->can('update', $invoice))
                    <a href="{{ route('supplier.invoices.edit', $invoice) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-medical-yellow-600 text-white rounded-xl hover:bg-medical-yellow-700 transition-all duration-200 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>تعديل</span>
                    </a>
                @endif
                @if (auth()->user()->can('delete', $invoice))
                    <form action="{{ route('supplier.invoices.destroy', $invoice) }}" method="POST" class="inline"
                        onsubmit="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-medical-red-700 text-white rounded-xl hover:bg-medical-red-800 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>حذف</span>
                        </button>
                    </form>
                @endif
                <a href="{{ route('supplier.invoices.print', $invoice) }}" target="_blank"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>طباعة</span>
                </a>
                @if ($invoice->hasMedia('invoice_documents'))
                    <a href="{{ $invoice->getFirstMediaUrl('invoice_documents') }}" target="_blank"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <span>عرض المرفق</span>
                    </a>
                @endif
                <a href="{{ route('supplier.invoices.index') }}"
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
    {{-- Note: Success messages are displayed in dashboard layout component --}}
    {{-- Only show error messages here to avoid duplicates --}}
    @if (session('error'))
        <div
            class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div
            class="bg-medical-blue-50 border border-medical-blue-200 text-medical-blue-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('info') }}
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
                        <p class="font-semibold text-medical-gray-900">{{ $invoice->invoice_date?->format('Y-m-d') }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-medical-gray-200">
                    {{-- From (Supplier) --}}
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-3">من:</p>
                        <p class="font-bold text-medical-gray-900">
                            {{ auth()->user()->supplierProfile?->company_name ?? auth()->user()->name }}</p>
                        <p class="text-medical-gray-600 mt-1">{{ auth()->user()->supplierProfile?->address ?? '' }}
                        </p>
                        <p class="text-medical-gray-600">
                            {{ auth()->user()->supplierProfile?->contact_email ?? auth()->user()->email }}</p>
                        <p class="text-medical-gray-600">{{ auth()->user()->supplierProfile?->contact_phone ?? '' }}
                        </p>
                    </div>

                    {{-- To (Buyer) --}}
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-3">إلى:</p>
                        <p class="font-bold text-medical-gray-900">
                            {{ $invoice->order->buyer?->organization_name ?? 'غير محدد' }}</p>
                        <p class="text-medical-gray-600 mt-1">{{ $invoice->order->buyer?->address ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->buyer?->contact_email ?? '' }}</p>
                        <p class="text-medical-gray-600">{{ $invoice->order->buyer?->contact_phone ?? '' }}</p>
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
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    المنتج</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    الكمية</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    سعر الوحدة</th>
                                <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-medical-gray-200">
                            @forelse($invoice->order->items ?? [] as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-medium text-medical-gray-900">
                                            {{ $item->product?->name ?? ($item->item_name ?? 'منتج') }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-medical-gray-900">{{ $item->quantity }}</td>
                                    <td class="px-6 py-4 text-medical-gray-900">
                                        {{ number_format($item->unit_price, 2) }} د.ل</td>
                                    <td class="px-6 py-4 font-semibold text-medical-gray-900">
                                        {{ number_format($item->total_price, 2) }} د.ل</td>
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
                        @if ($invoice->tax > 0)
                            <div class="flex justify-between text-medical-gray-600">
                                <span>الضريبة:</span>
                                <span>{{ number_format($invoice->tax, 2) }} د.ل</span>
                            </div>
                        @endif
                        @if ($invoice->discount > 0)
                            <div class="flex justify-between text-medical-green-600">
                                <span>الخصم:</span>
                                <span>-{{ number_format($invoice->discount, 2) }} د.ل</span>
                            </div>
                        @endif
                        <div
                            class="flex justify-between pt-3 border-t border-medical-gray-300 text-lg font-bold text-medical-gray-900">
                            <span>الإجمالي:</span>
                            <span>{{ number_format($invoice->total_amount, 2) }} د.ل</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            @if ($invoice->notes)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">ملاحظات</h3>
                    <p class="text-medical-gray-600">{{ $invoice->notes }}</p>
                </div>
            @endif

            {{-- Payments --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    المدفوعات
                </h3>

                @if ($invoice->payments->isNotEmpty())
                    <div class="space-y-3 mb-4">
                        @foreach ($invoice->payments as $payment)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">
                                        {{ $payment->payment_reference ?? 'دفعة' }}</p>
                                    <p class="text-sm text-medical-gray-500">
                                        {{ $payment->paid_at?->format('Y-m-d H:i') }}</p>
                                </div>
                                <div class="text-left">
                                    <p class="font-bold text-medical-green-600">
                                        {{ number_format($payment->amount, 2) }} د.ل</p>
                                    <p class="text-xs text-medical-gray-500">
                                        {{ \App\Models\Payment::getMethodLabel($payment->method) }}</p>
                                    @if ($payment->status !== 'completed')
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs {{ \App\Models\Payment::getStatusClasses($payment->status) }}">
                                            {{ \App\Models\Payment::getStatusLabel($payment->status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-medical-gray-500 mb-4">لم تُسجّل أي مدفوعات بعد.</p>
                @endif

                @php
                    $canRecordPayment =
                        in_array($invoice->status, [
                            \App\Models\Invoice::STATUS_ISSUED,
                            \App\Models\Invoice::STATUS_APPROVED,
                        ]) &&
                        $invoice->payment_status !== \App\Models\Invoice::PAYMENT_PAID &&
                        isset($remainingBalance) &&
                        $remainingBalance > 0;
                @endphp
                @if ($canRecordPayment)
                    <button type="button"
                        onclick="document.getElementById('recordPaymentModal').classList.remove('hidden'); document.getElementById('recordPaymentModal').classList.add('flex');"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-colors font-medium text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        تسجيل دفعة (المتبقي: {{ number_format($remainingBalance ?? 0, 2) }} د.ل)
                    </button>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Workflow Steps --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    مراحل الفاتورة
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center gap-2">
                        @if ($invoice->status !== 'draft')
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-green-100 text-medical-green-700 flex items-center justify-center">✓</span>
                        @else
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-gray-200 text-medical-gray-600 flex items-center justify-center">1</span>
                        @endif
                        <span
                            class="{{ $invoice->status === 'draft' ? 'text-medical-gray-600' : 'text-medical-gray-900' }}">إنشاء
                            الفاتورة / مسودة</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (in_array($invoice->status, ['issued', 'approved']))
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-green-100 text-medical-green-700 flex items-center justify-center">✓</span>
                        @else
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-gray-200 text-medical-gray-600 flex items-center justify-center">2</span>
                        @endif
                        <span
                            class="{{ in_array($invoice->status, ['issued', 'approved']) ? 'text-medical-gray-900' : 'text-medical-gray-600' }}">إرسال
                            للمشتري</span>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($invoice->payment_status === 'paid')
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-green-100 text-medical-green-700 flex items-center justify-center">✓</span>
                        @else
                            <span
                                class="flex-shrink-0 w-6 h-6 rounded-full bg-medical-gray-200 text-medical-gray-600 flex items-center justify-center">3</span>
                        @endif
                        <span
                            class="{{ $invoice->payment_status === 'paid' ? 'text-medical-gray-900' : 'text-medical-gray-600' }}">تسجيل
                            الدفع / مدفوعة</span>
                    </div>
                </div>
            </div>

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
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClasses[$invoice->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
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
                        <span
                            class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $paymentClasses[$invoice->payment_status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
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
                        <a href="{{ route('supplier.orders.show', $invoice->order) }}"
                            class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                            {{ $invoice->order->order_number ?? 'N/A' }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">
                            {{ $invoice->order->order_date?->format('Y-m-d') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">حالة الطلب</p>
                        @php
                            $orderStatusClasses = [
                                'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                'processing' => 'bg-medical-blue-100 text-medical-blue-700',
                                'shipped' => 'bg-medical-purple-100 text-medical-purple-700',
                                'delivered' => 'bg-medical-green-100 text-medical-green-700',
                                'cancelled' => 'bg-medical-red-100 text-medical-red-700',
                            ];
                            $orderStatusLabels = [
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المعالجة',
                                'shipped' => 'تم الشحن',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغى',
                            ];
                        @endphp
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1 {{ $orderStatusClasses[$invoice->order->status ?? ''] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                            {{ $orderStatusLabels[$invoice->order->status ?? ''] ?? ($invoice->order->status ?? 'غير محدد') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Amounts Summary --}}
            <div
                class="bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl shadow-medical p-6 text-white">
                <h3 class="text-lg font-semibold mb-4 pb-3 border-b border-white/20">
                    ملخص المبالغ
                </h3>

                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-white/80">المجموع الفرعي:</span>
                        <span class="font-semibold">{{ number_format($invoice->subtotal, 2) }} د.ل</span>
                    </div>
                    @if ($invoice->tax > 0)
                        <div class="flex justify-between">
                            <span class="text-white/80">الضريبة:</span>
                            <span class="font-semibold">{{ number_format($invoice->tax, 2) }} د.ل</span>
                        </div>
                    @endif
                    @if ($invoice->discount > 0)
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

            {{-- Invoice Actions --}}
            <div class="bg-white rounded-2xl shadow-medical p-6 space-y-3">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">إجراءات الفاتورة</h3>

                @if ($invoice->status !== \App\Models\Invoice::STATUS_CANCELLED && auth()->user()->can('cancel', $invoice))
                    <form action="{{ route('supplier.invoices.cancel', $invoice) }}" method="POST" class="w-full"
                        x-data="{ showCancelModal: false }">
                        @csrf
                        <button type="button" @click="showCancelModal = true"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            إلغاء الفاتورة
                        </button>

                        {{-- Cancel Modal --}}
                        <div x-show="showCancelModal" x-cloak @click.away="showCancelModal = false"
                            class="fixed inset-0 z-50 overflow-y-auto mt-8">
                            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
                                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showCancelModal = false">
                                </div>
                                <div class="relative bg-white rounded-lg px-4 pt-5 pb-4 shadow-xl max-w-md w-full">
                                    <h3 class="text-lg font-bold text-gray-900 mb-4">إلغاء الفاتورة</h3>
                                    <form action="{{ route('supplier.invoices.cancel', $invoice) }}" method="POST">
                                        @csrf
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">سبب الإلغاء
                                                (اختياري)</label>
                                            <textarea name="cancellation_reason" rows="3"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500"
                                                placeholder="أدخل سبب الإلغاء..."></textarea>
                                        </div>
                                        <div class="flex gap-3">
                                            <button type="button" @click="showCancelModal = false"
                                                class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                                إلغاء
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                                تأكيد الإلغاء
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </form>
                @endif

                @if ($invoice->status === 'draft')
                    <form action="{{ route('supplier.invoices.send', $invoice) }}" method="POST" class="w-full">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            إرسال الفاتورة للمشتري
                        </button>
                    </form>
                @endif

                @if (isset($remainingBalance) &&
                        $remainingBalance > 0 &&
                        in_array($invoice->status, [\App\Models\Invoice::STATUS_ISSUED, \App\Models\Invoice::STATUS_APPROVED]))
                    <button type="button"
                        onclick="document.getElementById('recordPaymentModal').classList.remove('hidden'); document.getElementById('recordPaymentModal').classList.add('flex');"
                        class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        تسجيل دفعة مستلمة
                    </button>
                @endif

                @if ($invoice->status === 'cancelled')
                    <div class="p-3 bg-red-50 rounded-xl border border-red-200">
                        <p class="text-sm text-red-700">
                            <strong>تم الإلغاء:</strong> {{ $invoice->updated_at->format('Y/m/d H:i') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Record Payment Modal --}}
    @if (isset($remainingBalance) &&
            $remainingBalance > 0 &&
            in_array($invoice->status, [\App\Models\Invoice::STATUS_ISSUED, \App\Models\Invoice::STATUS_APPROVED]))
        <div id="recordPaymentModal"
            class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
            <div class="bg-white rounded-2xl shadow-medical p-8 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-medical-gray-900 mb-4">تسجيل دفعة مستلمة</h3>
                <p class="text-medical-gray-600 mb-6">المبلغ المتبقي:
                    <strong>{{ number_format($remainingBalance, 2) }} د.ل</strong>
                </p>

                <form action="{{ route('supplier.invoices.payments.store', $invoice) }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-1">المبلغ (د.ل)
                                *</label>
                            <input type="number" name="amount" step="0.01" min="0.01"
                                max="{{ $remainingBalance }}"
                                value="{{ old('amount', number_format($remainingBalance, 2, '.', '')) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                                required>
                            @error('amount')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-1">طريقة الدفع *</label>
                            <select name="method" required
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                @foreach (\App\Models\Payment::getMethodOptions() as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('method') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('method')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-1">تاريخ الدفع</label>
                            <input type="datetime-local" name="paid_at"
                                value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                            @error('paid_at')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-1">رقم المرجع / المعاملة
                                (اختياري)</label>
                            <input type="text" name="transaction_id" value="{{ old('transaction_id') }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                                placeholder="مثال: تحويل بنكي #123">
                            @error('transaction_id')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-1">ملاحظات
                                (اختياري)</label>
                            <textarea name="notes" rows="2"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                                placeholder="أي ملاحظات عن الدفعة...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-medium">
                            تسجيل الدفعة
                        </button>
                        <button type="button"
                            onclick="document.getElementById('recordPaymentModal').classList.add('hidden'); document.getElementById('recordPaymentModal').classList.remove('flex');"
                            class="flex-1 px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-medium">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            document.getElementById('recordPaymentModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    this.classList.remove('flex');
                }
            });
        </script>
    @endif

    {{-- Cancel Invoice Modal (for header button) --}}
    @if ($invoice->status !== \App\Models\Invoice::STATUS_CANCELLED && auth()->user()->can('cancel', $invoice))
        <div id="cancelModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50"
            style="display: none;">
            <div class="bg-white rounded-2xl shadow-medical p-8 max-w-md w-full mx-4">
                <h3 class="text-xl font-bold text-medical-gray-900 mb-4">إلغاء الفاتورة</h3>
                <p class="text-medical-gray-600 mb-6">هل أنت متأكد من إلغاء هذه الفاتورة؟ يمكنك إضافة سبب الإلغاء
                    (اختياري).</p>

                <form action="{{ route('supplier.invoices.cancel', $invoice) }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            سبب الإلغاء (اختياري)
                        </label>
                        <textarea name="cancellation_reason" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                            placeholder="أدخل سبب الإلغاء..."></textarea>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit"
                            class="flex-1 px-6 py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 transition-all duration-200 font-medium">
                            تأكيد الإلغاء
                        </button>
                        <button type="button" onclick="hideCancelModal()"
                            class="flex-1 px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-medium">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
            function showCancelModal() {
                document.getElementById('cancelModal').style.display = 'flex';
            }

            function hideCancelModal() {
                document.getElementById('cancelModal').style.display = 'none';
            }

            document.getElementById('cancelModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    hideCancelModal();
                }
            });
        </script>
    @endif

</x-dashboard.layout>
