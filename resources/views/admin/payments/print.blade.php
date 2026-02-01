<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المدفوعات - طباعة</title>
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
    <div class="max-w-6xl mx-auto bg-white rounded-2xl shadow-lg p-8 print:shadow-none print:rounded-none">
        {{-- Print / Back actions (hidden when printing) --}}
        <div class="no-print flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">تقرير المدفوعات</h1>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة
                </button>
                <a href="{{ route('admin.payments.index', request()->query()) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>

        <div class="print-break-inside-avoid">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">تقرير المدفوعات</h2>
            <p class="text-sm text-gray-600 mb-4">تاريخ التقرير: {{ now()->format('Y-m-d H:i') }}</p>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 border border-gray-200 text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">#</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">رقم المرجع</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">تاريخ الدفع</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">المبلغ</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">العملة</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">طريقة الدفع</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">الحالة</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">رقم الفاتورة</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">رقم الطلب</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">المشتري</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">المورد</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold text-gray-600 uppercase border-b">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($payments as $payment)
                            <tr>
                                <td class="px-3 py-2 text-gray-900">{{ $loop->iteration }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->payment_reference ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->paid_at?->format('Y-m-d H:i') ?? $payment->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ number_format($payment->amount ?? 0, 2) }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->currency ?? 'LYD' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ \App\Models\Payment::getMethodLabel($payment->method ?? 'other') }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ \App\Models\Payment::getStatusLabel($payment->status ?? 'pending') }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->invoice?->invoice_number ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->order?->order_number ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->buyer?->organization_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ $payment->supplier?->company_name ?? '—' }}</td>
                                <td class="px-3 py-2 text-gray-900">{{ str()->limit($payment->notes ?? '—', 30) }}</td>
                            </tr>
                        @endforeach
                        <tr class="bg-gray-100 font-semibold">
                            <td colspan="9" class="px-3 py-3 border-t">إجمالي عدد السجلات: <strong>{{ $payments->count() }}</strong></td>
                            <td colspan="3" class="px-3 py-3 border-t text-left">إجمالي المبالغ (مكتملة): <strong>{{ number_format($payments->where('status', \App\Models\Payment::STATUS_COMPLETED)->sum('amount'), 2) }} د.ل</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
