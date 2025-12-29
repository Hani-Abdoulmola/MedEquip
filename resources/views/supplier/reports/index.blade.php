{{-- Supplier Reports - Advanced Analytics --}}
<x-dashboard.layout title="التقارير والتحليلات" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">التقارير والتحليلات</h1>
                <p class="mt-3 text-base text-medical-gray-600">تحليل شامل لأداء أعمالك وإحصائيات المبيعات</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('supplier.dashboard') }}"
                    class="px-5 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:border-medical-blue-500 hover:text-medical-blue-600 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6 mb-8">
        <form method="GET" action="{{ route('supplier.reports.index') }}" class="flex items-end gap-4">
            <div class="flex-1">
                <label class="block text-sm font-bold text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="from_date" value="{{ $fromDate }}"
                    class="w-full px-4 py-3 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all">
            </div>
            <div class="flex-1">
                <label class="block text-sm font-bold text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="to_date" value="{{ $toDate }}"
                    class="w-full px-4 py-3 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all">
            </div>
            <div>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg">
                    تطبيق
                </button>
            </div>
        </form>
    </div>

    {{-- Sales Overview Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Orders --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الطلبات</p>
                    <p class="text-4xl font-black text-medical-gray-900">{{ $salesOverview['total_orders'] }}</p>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الإيرادات</p>
                    <p class="text-4xl font-black text-medical-green-600">{{ number_format($salesOverview['total_revenue'], 2) }} د.ل</p>
                </div>
            </div>
        </div>

        {{-- Average Order Value --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">متوسط قيمة الطلب</p>
                    <p class="text-4xl font-black text-medical-purple-600">{{ number_format($salesOverview['average_order_value'], 2) }} د.ل</p>
                </div>
            </div>
        </div>

        {{-- Completed Orders --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16"></div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">الطلبات المكتملة</p>
                    <p class="text-4xl font-black text-medical-yellow-600">{{ $salesOverview['completed_orders'] }}</p>
                    <p class="text-xs font-semibold text-medical-gray-500 mt-1">{{ number_format($salesOverview['completed_revenue'], 2) }} د.ل</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Trends Chart --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-black text-medical-gray-900">اتجاهات الإيرادات (آخر 6 أشهر)</h2>
                <p class="text-sm text-medical-gray-500 mt-1">تحليل الإيرادات الشهرية</p>
            </div>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    {{-- Order & Quotation Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Order Status Breakdown --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">توزيع حالة الطلبات</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-medical-yellow-500 rounded-full"></div>
                        <span class="font-semibold text-medical-gray-900">قيد الانتظار</span>
                    </div>
                    <span class="text-lg font-black text-medical-gray-900">{{ $orderStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-medical-blue-500 rounded-full"></div>
                        <span class="font-semibold text-medical-gray-900">قيد المعالجة</span>
                    </div>
                    <span class="text-lg font-black text-medical-gray-900">{{ $orderStats['processing'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-medical-purple-500 rounded-full"></div>
                        <span class="font-semibold text-medical-gray-900">تم الشحن</span>
                    </div>
                    <span class="text-lg font-black text-medical-gray-900">{{ $orderStats['shipped'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-medical-green-500 rounded-full"></div>
                        <span class="font-semibold text-medical-gray-900">تم التسليم</span>
                    </div>
                    <span class="text-lg font-black text-medical-gray-900">{{ $orderStats['delivered'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-medical-red-500 rounded-full"></div>
                        <span class="font-semibold text-medical-gray-900">ملغي</span>
                    </div>
                    <span class="text-lg font-black text-medical-gray-900">{{ $orderStats['cancelled'] }}</span>
                </div>
            </div>
        </div>

        {{-- Quotation Statistics --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">إحصائيات عروض الأسعار</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">إجمالي العروض</span>
                    <span class="text-lg font-black text-medical-gray-900">{{ $quotationStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-yellow-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">قيد الانتظار</span>
                    <span class="text-lg font-black text-medical-yellow-600">{{ $quotationStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">مقبولة</span>
                    <span class="text-lg font-black text-medical-green-600">{{ $quotationStats['accepted'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-red-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">مرفوضة</span>
                    <span class="text-lg font-black text-medical-red-600">{{ $quotationStats['rejected'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-gradient-to-r from-medical-blue-50 to-medical-purple-50 rounded-xl border-2 border-medical-blue-200">
                    <span class="font-bold text-medical-gray-900">معدل النجاح</span>
                    <span class="text-2xl font-black text-medical-blue-600">{{ $quotationStats['success_rate'] }}%</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">قيمة العروض المقبولة</span>
                    <span class="text-lg font-black text-medical-gray-900">{{ number_format($quotationStats['accepted_value'], 2) }} د.ل</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Buyers & Product Performance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Top Buyers --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">أفضل المشترين</h2>
            <div class="space-y-3">
                @forelse($topBuyers as $index => $buyer)
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center text-white font-black">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-medical-gray-900">{{ $buyer['buyer_name'] }}</p>
                                <p class="text-xs text-medical-gray-500">{{ $buyer['order_count'] }} طلب</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-medical-green-600">{{ number_format($buyer['total_revenue'], 2) }} د.ل</span>
                    </div>
                @empty
                    <p class="text-center text-medical-gray-500 py-8">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>

        {{-- Product Performance --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">أفضل المنتجات أداءً</h2>
            <div class="space-y-3">
                @forelse($productPerformance as $index => $product)
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center text-white font-black">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-medical-gray-900">{{ Str::limit($product['name'], 30) }}</p>
                            </div>
                        </div>
                        <span class="text-lg font-black text-medical-purple-600">{{ $product['total_quantity'] }} وحدة</span>
                    </div>
                @empty
                    <p class="text-center text-medical-gray-500 py-8">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Payment & Delivery Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        {{-- Payment Statistics --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">إحصائيات المدفوعات</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">إجمالي المدفوعات</span>
                    <span class="text-lg font-black text-medical-gray-900">{{ $paymentStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">إجمالي المبلغ</span>
                    <span class="text-lg font-black text-medical-green-600">{{ number_format($paymentStats['total_amount'], 2) }} د.ل</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">مكتملة</span>
                    <span class="text-lg font-black text-medical-green-600">{{ $paymentStats['completed'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-yellow-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">قيد الانتظار</span>
                    <span class="text-lg font-black text-medical-yellow-600">{{ $paymentStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-red-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">فاشلة</span>
                    <span class="text-lg font-black text-medical-red-600">{{ $paymentStats['failed'] }}</span>
                </div>
            </div>
        </div>

        {{-- Delivery Statistics --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">إحصائيات التسليم</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">إجمالي عمليات التسليم</span>
                    <span class="text-lg font-black text-medical-gray-900">{{ $deliveryStats['total'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-yellow-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">قيد الانتظار</span>
                    <span class="text-lg font-black text-medical-yellow-600">{{ $deliveryStats['pending'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-blue-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">قيد النقل</span>
                    <span class="text-lg font-black text-medical-blue-600">{{ $deliveryStats['in_transit'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">تم التسليم</span>
                    <span class="text-lg font-black text-medical-green-600">{{ $deliveryStats['delivered'] }}</span>
                </div>
                <div class="flex items-center justify-between p-4 bg-medical-red-50 rounded-xl">
                    <span class="font-semibold text-medical-gray-900">فاشلة</span>
                    <span class="text-lg font-black text-medical-red-600">{{ $deliveryStats['failed'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart Script --}}
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const revenueData = @json($revenueTrends);
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => item.month),
                datasets: [{
                    label: 'الإيرادات (د.ل)',
                    data: revenueData.map(item => item.revenue),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString() + ' د.ل';
                            }
                        }
                    }
                }
            }
        });
    </script>
    @endpush

</x-dashboard.layout>

