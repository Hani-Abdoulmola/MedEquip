<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة فاتورة {{ $invoice->invoice_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-break-inside-avoid { break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-full p-6 print:p-0 print:bg-white">
    <div class="max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8 print:shadow-none print:rounded-none">
        {{-- Print / Back actions (hidden when printing) --}}
        <div class="no-print flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">فاتورة {{ $invoice->invoice_number }}</h1>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة
                </button>
                <a href="{{ route('buyer.invoices.show', $invoice) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>

        {{-- Invoice content (From = Supplier, To = Buyer) --}}
        <div class="print-break-inside-avoid">
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">فاتورة</h2>
                    <p class="font-mono text-xl text-blue-600 mt-2">{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-600">تاريخ الإصدار</p>
                    <p class="font-semibold text-gray-900">{{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-gray-200">
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-3">من (المورد):</p>
                    <p class="font-bold text-gray-900">{{ $invoice->order?->supplier?->company_name ?? '—' }}</p>
                    <p class="text-gray-600 mt-1">{{ $invoice->order?->supplier?->address ?? '' }}</p>
                    <p class="text-gray-600">{{ $invoice->order?->supplier?->contact_email ?? '' }}</p>
                    <p class="text-gray-600">{{ $invoice->order?->supplier?->contact_phone ?? '' }}</p>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-600 mb-3">إلى (المشتري):</p>
                    <p class="font-bold text-gray-900">{{ $invoice->order?->buyer?->organization_name ?? 'غير محدد' }}</p>
                    <p class="text-gray-600 mt-1">{{ $invoice->order?->buyer?->address ?? '' }}</p>
                    <p class="text-gray-600">{{ $invoice->order?->buyer?->contact_email ?? '' }}</p>
                    <p class="text-gray-600">{{ $invoice->order?->buyer?->contact_phone ?? '' }}</p>
                </div>
            </div>
        </div>

        <div class="mt-8 print-break-inside-avoid">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">عناصر الفاتورة</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase border-b">المنتج</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase border-b">الكمية</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase border-b">سعر الوحدة</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase border-b">الإجمالي</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invoice->order->items ?? [] as $item)
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $item->item_name ?? $item->product?->name ?? 'منتج' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
                                <td class="px-4 py-3 text-gray-900">{{ number_format($item->unit_price, 2) }} د.ل</td>
                                <td class="px-4 py-3 font-semibold text-gray-900">{{ number_format($item->total_price, 2) }} د.ل</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-500">لا توجد عناصر</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6 p-4 bg-gray-50 rounded-lg max-w-xs mr-auto">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>المجموع الفرعي:</span>
                        <span>{{ number_format($invoice->subtotal, 2) }} د.ل</span>
                    </div>
                    @if($invoice->tax > 0)
                        <div class="flex justify-between text-gray-600">
                            <span>الضريبة:</span>
                            <span>{{ number_format($invoice->tax, 2) }} د.ل</span>
                        </div>
                    @endif
                    @if($invoice->discount > 0)
                        <div class="flex justify-between text-green-600">
                            <span>الخصم:</span>
                            <span>-{{ number_format($invoice->discount, 2) }} د.ل</span>
                        </div>
                    @endif
                    <div class="flex justify-between pt-3 border-t border-gray-300 text-lg font-bold text-gray-900">
                        <span>الإجمالي:</span>
                        <span>{{ number_format($invoice->total_amount, 2) }} د.ل</span>
                    </div>
                </div>
            </div>
        </div>

        @if($invoice->notes)
            <div class="mt-6 p-4 border border-gray-200 rounded-lg print-break-inside-avoid">
                <h3 class="text-sm font-semibold text-gray-700 mb-2">ملاحظات</h3>
                <p class="text-gray-600 text-sm">{{ $invoice->notes }}</p>
            </div>
        @endif
        <p class="mt-4 text-sm text-gray-500">رقم الطلب: {{ $invoice->order->order_number ?? '—' }}</p>
    </div>
</body>
</html>
