@php
    $show = function ($types) use ($reportType) {
        return in_array($reportType, $types, true);
    };
    $printUrl = route('admin.reports.print', request()->query());
    $backUrl = route('admin.reports', request()->query());
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة التقرير - التقارير والإحصائيات</title>
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
    <div class="max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-8 print:shadow-none print:rounded-none">
        {{-- Toolbar (hidden when printing) --}}
        <div class="no-print flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
            <h1 class="text-xl font-bold text-gray-800">التقارير والإحصائيات</h1>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl hover:bg-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    طباعة
                </button>
                <a href="{{ $backUrl }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-200 text-gray-700 rounded-xl hover:bg-gray-300 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    العودة
                </a>
            </div>
        </div>

        {{-- Report header --}}
        <div class="print-break-inside-avoid mb-6 pb-4 border-b-2 border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900">التقارير والإحصائيات</h2>
            <p class="text-sm text-gray-600 mt-1">
                الفترة: {{ \Carbon\Carbon::parse($fromDate)->locale('ar')->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->locale('ar')->translatedFormat('d M Y') }}
                · نوع التقرير: {{ $reportTypeLabels[$reportType] ?? $reportType }}
            </p>
            <p class="text-xs text-gray-500 mt-1">تاريخ الطباعة: {{ now()->locale('ar')->translatedFormat('l، d F Y - H:i') }}</p>
        </div>

        {{-- Key metrics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6 print-break-inside-avoid">
            @if($show(['all', 'sales', 'products']))
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600">إجمالي المبيعات</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($platformOverview['total_revenue'], 0) }} د.ل</p>
            </div>
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600">عدد الطلبات</p>
                <p class="text-2xl font-bold text-gray-900">{{ $platformOverview['total_orders'] }}</p>
            </div>
            @endif
            @if($show(['all', 'users']))
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600">مستخدمين جدد</p>
                <p class="text-2xl font-bold text-gray-900">{{ $userStats['new_users'] }}</p>
            </div>
            @endif
            @if($show(['all', 'sales']))
            <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
                <p class="text-xs font-semibold text-gray-600">معدل التحويل</p>
                <p class="text-2xl font-bold text-gray-900">{{ $platformOverview['conversion_rate'] }}%</p>
            </div>
            @endif
        </div>

        @if($show(['all', 'sales', 'products']))
        {{-- Revenue chart --}}
        <div class="mb-6 print-break-inside-avoid">
            <h3 class="text-lg font-bold text-gray-900 mb-3">اتجاهات الإيرادات (آخر 6 أشهر)</h3>
            <div class="h-48">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
        @endif

        {{-- Order & User stats --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 print-break-inside-avoid">
            @if($show(['all', 'sales', 'products']))
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">توزيع حالة الطلبات</h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="py-2 text-gray-700">قيد الانتظار</td><td class="py-2 font-semibold text-right">{{ $orderStats['pending'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">قيد المعالجة</td><td class="py-2 font-semibold text-right">{{ $orderStats['processing'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">تم الشحن</td><td class="py-2 font-semibold text-right">{{ $orderStats['shipped'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">تم التسليم</td><td class="py-2 font-semibold text-right">{{ $orderStats['delivered'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">ملغي</td><td class="py-2 font-semibold text-right">{{ $orderStats['cancelled'] }}</td></tr>
                    </tbody>
                </table>
            </div>
            @endif
            @if($show(['all', 'users']))
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">إحصائيات المستخدمين</h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="py-2 text-gray-700">إجمالي المستخدمين</td><td class="py-2 font-semibold text-right">{{ $userStats['total_users'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">الموردين</td><td class="py-2 font-semibold text-right">{{ $userStats['total_suppliers'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">المشترين</td><td class="py-2 font-semibold text-right">{{ $userStats['total_buyers'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">موردين معتمدين</td><td class="py-2 font-semibold text-right">{{ $userStats['verified_suppliers'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">مشترين معتمدين</td><td class="py-2 font-semibold text-right">{{ $userStats['verified_buyers'] }}</td></tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @if($show(['all', 'sales']))
        <div class="mb-6 print-break-inside-avoid">
            <h3 class="text-lg font-bold text-gray-900 mb-3">إحصائيات طلبات عروض الأسعار</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                <div class="p-3 bg-gray-50 rounded border"><span class="text-gray-600">إجمالي RFQs</span><br><span class="font-bold text-gray-900">{{ $rfqStats['total_rfqs'] }}</span></div>
                <div class="p-3 bg-gray-50 rounded border"><span class="text-gray-600">RFQs مفتوحة</span><br><span class="font-bold text-blue-600">{{ $rfqStats['open_rfqs'] }}</span></div>
                <div class="p-3 bg-gray-50 rounded border"><span class="text-gray-600">عروض مقبولة</span><br><span class="font-bold text-green-600">{{ $rfqStats['accepted_quotations'] }}</span></div>
                <div class="p-3 bg-gray-50 rounded border"><span class="text-gray-600">معدل القبول</span><br><span class="font-bold text-gray-900">{{ $rfqStats['acceptance_rate'] }}%</span></div>
            </div>
        </div>
        @endif

        {{-- Top Suppliers & Buyers --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 print-break-inside-avoid">
            @if($show(['all', 'sales', 'users']))
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">أفضل الموردين</h3>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-gray-200"><th class="text-right py-2 text-gray-600">المورد</th><th class="text-left py-2 text-gray-600">الإيرادات</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topSuppliers as $s)
                        <tr><td class="py-2 font-medium text-gray-900">{{ $s['supplier_name'] }}</td><td class="py-2 text-left">{{ number_format($s['total_revenue'], 0) }} د.ل</td></tr>
                        @empty
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">أفضل المشترين</h3>
                <table class="min-w-full text-sm">
                    <thead><tr class="border-b border-gray-200"><th class="text-right py-2 text-gray-600">المشتري</th><th class="text-left py-2 text-gray-600">الإنفاق</th></tr></thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($topBuyers as $b)
                        <tr><td class="py-2 font-medium text-gray-900">{{ $b['buyer_name'] }}</td><td class="py-2 text-left">{{ number_format($b['total_spending'], 0) }} د.ل</td></tr>
                        @empty
                        <tr><td colspan="2" class="py-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        @if($show(['all', 'sales', 'products']))
        <div class="mb-6 print-break-inside-avoid">
            <h3 class="text-lg font-bold text-gray-900 mb-3">أكثر المنتجات مبيعاً</h3>
            <table class="min-w-full border border-gray-200 text-sm">
                <thead class="bg-gray-50"><tr><th class="px-3 py-2 text-right text-gray-600">المنتج</th><th class="px-3 py-2 text-gray-600">الكمية</th><th class="px-3 py-2 text-left text-gray-600">الإيرادات</th></tr></thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($topProducts as $p)
                    <tr><td class="px-3 py-2 font-medium text-gray-900">{{ Str::limit($p['name'], 40) }}</td><td class="px-3 py-2">{{ $p['total_quantity'] }}</td><td class="px-3 py-2">{{ number_format($p['total_revenue'], 0) }} د.ل</td></tr>
                    @empty
                    <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endif

        {{-- Payment & Delivery --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 print-break-inside-avoid">
            @if($show(['all', 'sales']))
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">إحصائيات المدفوعات</h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="py-2 text-gray-700">إجمالي المدفوعات</td><td class="py-2 font-semibold text-right">{{ $paymentStats['total'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">إجمالي المبلغ</td><td class="py-2 font-semibold text-right text-green-600">{{ number_format($paymentStats['total_amount'], 0) }} د.ل</td></tr>
                        <tr><td class="py-2 text-gray-700">مكتملة</td><td class="py-2 font-semibold text-right">{{ $paymentStats['completed'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">قيد الانتظار</td><td class="py-2 font-semibold text-right">{{ $paymentStats['pending'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">فاشلة</td><td class="py-2 font-semibold text-right">{{ $paymentStats['failed'] }}</td></tr>
                    </tbody>
                </table>
            </div>
            @endif
            @if($show(['all', 'sales', 'products']))
            <div class="border border-gray-200 rounded-lg p-4">
                <h3 class="text-lg font-bold text-gray-900 mb-3">إحصائيات التسليم</h3>
                <table class="min-w-full text-sm">
                    <tbody class="divide-y divide-gray-200">
                        <tr><td class="py-2 text-gray-700">إجمالي عمليات التسليم</td><td class="py-2 font-semibold text-right">{{ $deliveryStats['total'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">قيد الانتظار</td><td class="py-2 font-semibold text-right">{{ $deliveryStats['pending'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">قيد النقل</td><td class="py-2 font-semibold text-right">{{ $deliveryStats['in_transit'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">تم التسليم</td><td class="py-2 font-semibold text-right">{{ $deliveryStats['delivered'] }}</td></tr>
                        <tr><td class="py-2 text-gray-700">فاشلة</td><td class="py-2 font-semibold text-right">{{ $deliveryStats['failed'] }}</td></tr>
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>

    @if($show(['all', 'sales', 'products']))
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const revenueData = @json($revenueTrends);
            const el = document.getElementById('revenueChart');
            if (!el) return;
            new Chart(el.getContext('2d'), {
                type: 'line',
                data: {
                    labels: revenueData.map(item => item.month),
                    datasets: [{ label: 'الإيرادات (د.ل)', data: revenueData.map(item => item.revenue), borderColor: 'rgb(59, 130, 246)', backgroundColor: 'rgba(59, 130, 246, 0.1)', tension: 0.4, fill: true }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: true, position: 'top' } },
                    scales: { y: { beginAtZero: true, ticks: { callback: v => v.toLocaleString() + ' د.ل' } } }
                }
            });
        });
    </script>
    @endif
</body>
</html>
