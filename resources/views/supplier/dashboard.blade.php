{{-- Supplier Dashboard - Enterprise + ApexCharts --}}
<x-dashboard.layout title="لوحة تحكم المورد" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    @php
        /** @var \App\Models\Supplier $supplier */
        $companyName = $supplier->company_name ?? 'شركتك';
    @endphp

    {{-- Premium Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">لوحة التحكم</h1>
                <p class="mt-3 text-base text-medical-gray-600">مرحباً بك، {{ $companyName }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if (!$supplier->is_verified)
                    <div
                        class="px-5 py-3 bg-medical-yellow-50 border-2 border-medical-yellow-300 text-medical-yellow-700 rounded-xl font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>قيد المراجعة</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Top Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Total Products --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-green-600 bg-medical-green-50 px-3 py-1 rounded-full">المنتجات</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي المنتجات</p>
                    <p class="text-4xl font-black text-medical-gray-900">{{ $stats['total_products'] }}</p>
                </div>
                <div class="mt-3 text-xs text-medical-gray-500 space-x-2 space-x-reverse flex flex-wrap gap-2">
                    <span>قيد المراجعة: <span
                            class="font-bold text-medical-yellow-600">{{ $stats['pending_products'] }}</span></span>
                    <span>معتمد: <span
                            class="font-bold text-medical-green-600">{{ $stats['approved_products'] }}</span></span>
                </div>
            </div>
        </div>

        {{-- Total Orders --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-blue-600 bg-medical-blue-50 px-3 py-1 rounded-full">الطلبات</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الطلبات</p>
                    <p class="text-4xl font-black text-medical-blue-600">{{ $stats['total_orders'] }}</p>
                </div>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-yellow-600 bg-medical-yellow-50 px-3 py-1 rounded-full">قيد
                        الانتظار</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">طلبات قيد الانتظار</p>
                    <p class="text-4xl font-black text-medical-yellow-600">{{ $stats['pending_orders'] }}</p>
                </div>
            </div>
        </div>

        {{-- Total Revenue --}}
        <div
            class="group relative bg-white rounded-2xl p-6 shadow-lg hover:shadow-2xl transition-all duration-300 border border-medical-gray-100 overflow-hidden">
            <div
                class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16">
            </div>
            <div class="relative">
                <div class="flex items-center justify-between mb-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span
                        class="text-xs font-bold text-medical-purple-600 bg-medical-purple-50 px-3 py-1 rounded-full">الإيرادات</span>
                </div>
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي الإيرادات</p>
                    <p class="text-4xl font-black text-medical-purple-600">
                        {{ number_format($stats['total_revenue'], 2) }} د.ل
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Secondary Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        {{-- Quotations --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">عروض الأسعار</p>
                    <p class="text-3xl font-black text-medical-gray-900">{{ $stats['total_quotations'] }}</p>
                    <p class="text-xs text-medical-yellow-600 mt-1 font-semibold">
                        {{ $stats['pending_quotations'] }} قيد الانتظار
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Completed Orders --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">طلبات مكتملة</p>
                    <p class="text-3xl font-black text-medical-green-600">{{ $stats['completed_orders'] }}</p>
                    <p class="text-xs text-medical-green-600 mt-1 font-semibold">تم التسليم</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending RFQs --}}
        <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">طلبات عروض أسعار جديدة</p>
                    <p class="text-3xl font-black text-medical-red-600">{{ $stats['pending_rfqs'] }}</p>
                    <p class="text-xs text-medical-red-600 mt-1 font-semibold">تتطلب رد</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6">
            <h3 class="text-base font-bold text-medical-gray-900 mb-3">الإيرادات خلال آخر 6 أشهر</h3>
            <div id="revenueChart"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6">
            <h3 class="text-base font-bold text-medical-gray-900 mb-3">حالة الطلبات</h3>
            <div id="ordersStatusChart"></div>
        </div>

        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-6">
            <h3 class="text-base font-bold text-medical-gray-900 mb-3">حالة مراجعة المنتجات</h3>
            <div id="productsReviewChart"></div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
        <div class="flex items-center gap-3 mb-6">
            <div
                class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
            </div>
            <div>
                <h2 class="text-xl font-black text-medical-gray-900">إجراءات سريعة</h2>
                <p class="text-sm text-medical-gray-500">الوصول السريع للصفحات المهمة</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Add Product --}}
            <a href="{{ route('supplier.products.create') }}"
                class="group relative bg-gradient-to-br from-medical-green-50 to-white rounded-xl p-6 border-2 border-medical-green-200 hover:border-medical-green-400 transition-all hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-medical-gray-900">إضافة منتج</p>
                        <p class="text-xs text-medical-gray-600">إنشاء منتج جديد</p>
                    </div>
                </div>
            </a>

            {{-- Products List --}}
            <a href="{{ route('supplier.products.index') }}"
                class="group relative bg-gradient-to-br from-medical-teal-50 to-white rounded-xl p-6 border-2 border-medical-teal-200 hover:border-medical-teal-400 transition-all hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-teal-500 to-medical-teal-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-medical-gray-900">منتجاتي</p>
                        <p class="text-xs text-medical-gray-600">عرض وإدارة المنتجات</p>
                    </div>
                </div>
            </a>

            {{-- Orders --}}
            <a href="{{ route('supplier.orders') }}"
                class="group relative bg-gradient-to-br from-medical-blue-50 to-white rounded-xl p-6 border-2 border-medical-blue-200 hover:border-medical-blue-400 transition-all hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-medical-gray-900">الطلبات</p>
                        <p class="text-xs text-medical-gray-600">عرض وإدارة الطلبات</p>
                    </div>
                </div>
            </a>

            {{-- RFQs --}}
            <a href="{{ route('supplier.rfqs') }}"
                class="group relative bg-gradient-to-br from-medical-red-50 to-white rounded-xl p-6 border-2 border-medical-red-200 hover:border-medical-red-400 transition-all hover:shadow-lg">
                <div class="flex items-center gap-4">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-red-500 to-medical-red-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-bold text-medical-gray-900">طلبات عروض أسعار</p>
                        <p class="text-xs text-medical-gray-600">عرض والرد على RFQs</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Activity: آخر الطلبات --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-black text-medical-gray-900">النشاطات الأخيرة</h2>
                    <p class="text-sm text-medical-gray-500">آخر الطلبات الواردة</p>
                </div>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($recentOrders as $order)
                <div
                    class="flex items-center gap-4 p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-gray-100 transition-colors">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-bold text-medical-gray-900">
                            طلب جديد #{{ $order->order_number ?? $order->id }}
                        </p>
                        <p class="text-sm text-medical-gray-600">
                            {{ $order->buyer->organization_name ?? 'مشتري' }} -
                            {{ number_format($order->total_amount, 2) }} د.ل
                        </p>
                        <p class="text-xs text-medical-gray-500 mt-1">
                            {{ optional($order->order_date ?? $order->created_at)->diffForHumans() }}
                        </p>
                    </div>
                    <span class="px-3 py-1 bg-medical-blue-100 text-medical-blue-700 rounded-full text-xs font-bold">
                        {{ $order->status }}
                    </span>
                </div>
            @empty
                <div class="text-center py-12">
                    <svg class="w-16 h-16 mx-auto text-medical-gray-300 mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-medical-gray-600 font-semibold">لا توجد طلبات حديثة</p>
                    <p class="text-sm text-medical-gray-500 mt-1">سيتم عرض الطلبات الجديدة هنا</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // Revenue Area Chart
            const revenueOptions = {
                series: [{
                    name: 'الإيرادات',
                    data: @json($charts['revenue']['data'] ?? [])
                }],
                chart: {
                    type: 'area',
                    height: 260,
                    toolbar: {
                        show: false
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: @json($charts['revenue']['labels'] ?? []),
                    labels: {
                        style: {
                            fontFamily: 'system-ui'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function(val) {
                            try {
                                return Number(val).toLocaleString('ar-EG') + ' د.ل';
                            } catch (e) {
                                return val + ' د.ل';
                            }
                        }
                    }
                },
                fill: {
                    opacity: 0.25
                },
                grid: {
                    borderColor: '#E5E7EB'
                },
                tooltip: {
                    y: {
                        formatter: function(val) {
                            try {
                                return Number(val).toLocaleString('ar-EG') + ' د.ل';
                            } catch (e) {
                                return val + ' د.ل';
                            }
                        }
                    }
                }
            };
            new ApexCharts(document.querySelector("#revenueChart"), revenueOptions).render();

            // Orders Status Donut
            const ordersStatusOptions = {
                series: @json($charts['orders_status']['data'] ?? []),
                labels: @json($charts['orders_status']['labels'] ?? []),
                chart: {
                    type: 'donut',
                    height: 260
                },
                legend: {
                    position: 'bottom'
                },
                stroke: {
                    width: 1
                }
            };
            new ApexCharts(document.querySelector("#ordersStatusChart"), ordersStatusOptions).render();

            // Products Review Status Donut
            const productsReviewOptions = {
                series: @json($charts['products_review']['data'] ?? []),
                labels: @json($charts['products_review']['labels'] ?? []),
                chart: {
                    type: 'donut',
                    height: 260
                },
                legend: {
                    position: 'bottom'
                },
                stroke: {
                    width: 1
                }
            };
            new ApexCharts(document.querySelector("#productsReviewChart"), productsReviewOptions).render();
        </script>
    @endpush

</x-dashboard.layout>
