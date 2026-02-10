@php
    $reportsUrl = route('admin.reports');
    $presets = [
        'today' => ['من' => now()->format('Y-m-d'), 'إلى' => now()->format('Y-m-d'), 'label' => 'اليوم'],
        '7days' => [
            'من' => now()->subDays(6)->format('Y-m-d'),
            'إلى' => now()->format('Y-m-d'),
            'label' => 'آخر 7 أيام',
        ],
        '30days' => [
            'من' => now()->subDays(29)->format('Y-m-d'),
            'إلى' => now()->format('Y-m-d'),
            'label' => 'آخر 30 يوماً',
        ],
        '90days' => [
            'من' => now()->subDays(89)->format('Y-m-d'),
            'إلى' => now()->format('Y-m-d'),
            'label' => 'آخر 3 أشهر',
        ],
        '6months' => [
            'من' => now()->subMonths(5)->startOfMonth()->format('Y-m-d'),
            'إلى' => now()->format('Y-m-d'),
            'label' => 'آخر 6 أشهر',
        ],
        'this_month' => [
            'من' => now()->startOfMonth()->format('Y-m-d'),
            'إلى' => now()->format('Y-m-d'),
            'label' => 'هذا الشهر',
        ],
    ];
    $reportTypeLabels = [
        'all' => 'جميع التقارير',
        'sales' => 'المبيعات',
        'users' => 'المستخدمين',
        'products' => 'المنتجات',
    ];
@endphp
{{-- Admin Reports - Analytics and Reports Dashboard --}}
<x-dashboard.layout title="التقارير والإحصائيات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    @php
        $show = function ($types) use ($reportType) {
            return in_array($reportType, $types, true);
        };
    @endphp
    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-4xl font-black text-medical-gray-900 font-display">التقارير والإحصائيات</h1>
            <p class="mt-3 text-base text-medical-gray-600">تحليلات شاملة لأداء المنصة</p>
        </div>
        <a href="{{ route('admin.reports.print', request()->query()) }}" target="_blank" rel="noopener noreferrer"
           class="no-print inline-flex items-center gap-2 px-5 py-3 bg-white border-2 border-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 hover:border-medical-blue-500 transition-all font-bold shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>طباعة التقرير</span>
        </a>
    </div>

    {{-- Print-only header (shown when printing) --}}
    <div class="hidden print:block mb-4 pb-4 border-b-2 border-medical-gray-200">
        <h1 class="text-2xl font-black text-medical-gray-900">التقارير والإحصائيات</h1>
        <p class="text-sm text-medical-gray-600 mt-1">
            الفترة: {{ \Carbon\Carbon::parse($fromDate)->locale('ar')->translatedFormat('d M Y') }} – {{ \Carbon\Carbon::parse($toDate)->locale('ar')->translatedFormat('d M Y') }}
            · نوع التقرير: {{ $reportTypeLabels[$reportType] ?? $reportType }}
        </p>
        <p class="text-xs text-medical-gray-500 mt-1">{{ now()->locale('ar')->translatedFormat('l، d F Y - H:i') }}</p>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        @if ($show(['all', 'sales', 'products']))
            {{-- Total Revenue --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-blue-500/10 to-transparent rounded-full -mr-16 -mt-16">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي المبيعات</p>
                        <p class="text-4xl font-black text-medical-gray-900">
                            {{ number_format($platformOverview['total_revenue'], 0) }} د.ل</p>
                        @if ($platformOverview['revenue_growth'] != 0)
                            <p
                                class="text-sm font-semibold mt-2 {{ $platformOverview['revenue_growth'] > 0 ? 'text-medical-green-600' : 'text-medical-red-600' }}">
                                {{ $platformOverview['revenue_growth'] > 0 ? '+' : '' }}{{ $platformOverview['revenue_growth'] }}%
                                من الفترة السابقة
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($show(['all', 'sales', 'products']))
            {{-- Total Orders --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-green-500/10 to-transparent rounded-full -mr-16 -mt-16">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-1">عدد الطلبات</p>
                        <p class="text-4xl font-black text-medical-gray-900">{{ $platformOverview['total_orders'] }}
                        </p>
                        @if ($platformOverview['orders_growth'] != 0)
                            <p
                                class="text-sm font-semibold mt-2 {{ $platformOverview['orders_growth'] > 0 ? 'text-medical-green-600' : 'text-medical-red-600' }}">
                                {{ $platformOverview['orders_growth'] > 0 ? '+' : '' }}{{ $platformOverview['orders_growth'] }}%
                                من الفترة السابقة
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($show(['all', 'users']))
            {{-- New Users --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-purple-500/10 to-transparent rounded-full -mr-16 -mt-16">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-1">مستخدمين جدد</p>
                        <p class="text-4xl font-black text-medical-gray-900">{{ $userStats['new_users'] }}</p>
                        @if ($userStats['users_growth'] != 0)
                            <p
                                class="text-sm font-semibold mt-2 {{ $userStats['users_growth'] > 0 ? 'text-medical-green-600' : 'text-medical-red-600' }}">
                                {{ $userStats['users_growth'] > 0 ? '+' : '' }}{{ $userStats['users_growth'] }}% من
                                الفترة السابقة
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        @if ($show(['all', 'sales']))
            {{-- Conversion Rate --}}
            <div class="bg-white rounded-2xl p-6 shadow-lg border border-medical-gray-100 overflow-hidden relative">
                <div
                    class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-medical-yellow-500/10 to-transparent rounded-full -mr-16 -mt-16">
                </div>
                <div class="relative">
                    <div class="flex items-center justify-between mb-4">
                        <div
                            class="w-14 h-14 bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-medical-gray-600 mb-1">معدل التحويل</p>
                        <p class="text-4xl font-black text-medical-gray-900">
                            {{ $platformOverview['conversion_rate'] }}%</p>
                        <p class="text-xs font-semibold text-medical-gray-500 mt-1">RFQ إلى طلب</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filters Card --}}
    <div class="no-print bg-white rounded-xl shadow border border-medical-gray-200 overflow-hidden mb-4">
        <div class="px-3 py-2 border-b border-medical-gray-100">
            <h2 class="text-base font-bold text-medical-gray-900 mb-2">تصفية التقرير</h2>
            <form method="GET" action="{{ $reportsUrl }}"
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-2 items-end">
                <div>
                    <label class="block text-xs font-bold text-medical-gray-700 mb-1">من تاريخ</label>
                    <input type="date" name="from_date" value="{{ $fromDate }}"
                        class="w-full px-2 py-2 border border-medical-gray-200 rounded-md focus:ring-2 focus:ring-medical-blue-100 focus:border-medical-blue-500 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-medical-gray-700 mb-1">إلى تاريخ</label>
                    <input type="date" name="to_date" value="{{ $toDate }}"
                        class="w-full px-2 py-2 border border-medical-gray-200 rounded-md focus:ring-2 focus:ring-medical-blue-100 focus:border-medical-blue-500 text-sm transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-medical-gray-700 mb-1">نوع التقرير</label>
                    <select name="report_type"
                        class="w-full px-2 py-2 border border-medical-gray-200 rounded-md focus:ring-2 focus:ring-medical-blue-100 focus:border-medical-blue-500 text-sm transition-all">
                        @foreach ($reportTypeLabels as $value => $label)
                            <option value="{{ $value }}" {{ $reportType === $value ? 'selected' : '' }}>
                                {{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex gap-1 col-span-1">
                    <button type="submit"
                        class="flex-1 px-3 py-2 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-md hover:from-medical-blue-600 hover:to-medical-blue-700 text-xs font-bold shadow">
                        تطبيق
                    </button>
                    <a href="{{ $reportsUrl }}"
                        class="px-3 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-md hover:bg-medical-gray-200 text-xs font-bold flex items-center justify-center"
                        title="إعادة تعيين">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </a>
                </div>
            </form>
        </div>
        {{-- Quick date presets --}}
        <div class="px-3 py-2 bg-medical-gray-50 border-t border-medical-gray-100 flex flex-wrap items-center gap-1">
            <span class="text-xs font-semibold text-medical-gray-600">فترة سريعة:</span>
            @foreach ($presets as $key => $preset)
                <a href="{{ $reportsUrl }}?from_date={{ $preset['من'] }}&to_date={{ $preset['إلى'] }}&report_type={{ $reportType }}"
                    class="inline-flex px-2 py-1 text-xs font-medium rounded border transition-colors {{ $fromDate === $preset['من'] && $toDate === $preset['إلى'] ? 'bg-medical-blue-600 text-white border-medical-blue-600' : 'bg-white border-medical-gray-200 text-medical-gray-700 hover:bg-medical-gray-100' }}">
                    {{ $preset['label'] }}
                </a>
            @endforeach
        </div>
        {{-- Active filter summary --}}
        <div
            class="px-3 py-2 border-t border-medical-gray-100 flex flex-wrap items-center gap-1 text-xs text-medical-gray-600">
            <span class="font-semibold">الفترة:</span>
            <span>{{ \Carbon\Carbon::parse($fromDate)->locale('ar')->translatedFormat('d M Y') }}</span>
            <span>→</span>
            <span>{{ \Carbon\Carbon::parse($toDate)->locale('ar')->translatedFormat('d M Y') }}</span>
            <span class="text-medical-gray-400">|</span>
            <span class="font-semibold">نوع التقرير:</span>
            <span>{{ $reportTypeLabels[$reportType] ?? $reportType }}</span>
        </div>
    </div>


    @if ($show(['all', 'sales', 'products']))
        {{-- Revenue Trends Chart --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-black text-medical-gray-900">اتجاهات الإيرادات (آخر 6 أشهر)</h2>
                    <p class="text-sm text-medical-gray-500 mt-1">تحليل الإيرادات الشهرية للمنصة</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    @endif

    {{-- Order & User Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @if ($show(['all', 'sales', 'products']))
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
        @endif

        @if ($show(['all', 'users']))
            {{-- User Statistics --}}
            <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
                <h2 class="text-xl font-black text-medical-gray-900 mb-6">إحصائيات المستخدمين</h2>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">إجمالي المستخدمين</span>
                        <span class="text-lg font-black text-medical-gray-900">{{ $userStats['total_users'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-blue-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">الموردين</span>
                        <span
                            class="text-lg font-black text-medical-blue-600">{{ $userStats['total_suppliers'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">المشترين</span>
                        <span
                            class="text-lg font-black text-medical-green-600">{{ $userStats['total_buyers'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-purple-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">موردين معتمدين</span>
                        <span
                            class="text-lg font-black text-medical-purple-600">{{ $userStats['verified_suppliers'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-yellow-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">مشترين معتمدين</span>
                        <span
                            class="text-lg font-black text-medical-yellow-600">{{ $userStats['verified_buyers'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if ($show(['all', 'sales']))
        {{-- RFQ/Quotation Statistics --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">إحصائيات طلبات عروض الأسعار</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="p-4 bg-medical-gray-50 rounded-xl">
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">إجمالي RFQs</p>
                    <p class="text-3xl font-black text-medical-gray-900">{{ $rfqStats['total_rfqs'] }}</p>
                </div>
                <div class="p-4 bg-medical-blue-50 rounded-xl">
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">RFQs مفتوحة</p>
                    <p class="text-3xl font-black text-medical-blue-600">{{ $rfqStats['open_rfqs'] }}</p>
                </div>
                <div class="p-4 bg-medical-green-50 rounded-xl">
                    <p class="text-sm font-semibold text-medical-gray-600 mb-1">عروض مقبولة</p>
                    <p class="text-3xl font-black text-medical-green-600">{{ $rfqStats['accepted_quotations'] }}</p>
                </div>
                <div
                    class="p-4 bg-gradient-to-r from-medical-purple-50 to-medical-blue-50 rounded-xl border-2 border-medical-purple-200">
                    <p class="text-sm font-bold text-medical-gray-900 mb-1">معدل القبول</p>
                    <p class="text-3xl font-black text-medical-purple-600">{{ $rfqStats['acceptance_rate'] }}%</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Top Suppliers & Buyers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @if ($show(['all', 'sales', 'users']))
            {{-- Top Suppliers --}}
            <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
                <h2 class="text-xl font-black text-medical-gray-900 mb-6">أفضل الموردين</h2>
                <div class="space-y-3">
                    @forelse($topSuppliers as $index => $supplier)
                        <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center text-white font-black">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $supplier['supplier_name'] }}
                                    </p>
                                    <p class="text-xs text-medical-gray-500">{{ $supplier['product_count'] }} منتج</p>
                                </div>
                            </div>
                            <div class="text-left">
                                <p class="text-lg font-black text-medical-green-600">
                                    {{ number_format($supplier['total_revenue'], 0) }} د.ل</p>
                                <p class="text-xs text-medical-gray-500">{{ $supplier['order_count'] }} طلب</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-medical-gray-500 py-8">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        @endif

        @if ($show(['all', 'sales', 'users']))
            {{-- Top Buyers --}}
            <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
                <h2 class="text-xl font-black text-medical-gray-900 mb-6">أفضل المشترين</h2>
                <div class="space-y-3">
                    @forelse($topBuyers as $index => $buyer)
                        <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center text-white font-black">
                                    {{ $index + 1 }}
                                </div>
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $buyer['buyer_name'] }}</p>
                                    <p class="text-xs text-medical-gray-500">{{ $buyer['order_count'] }} طلب</p>
                                </div>
                            </div>
                            <span
                                class="text-lg font-black text-medical-blue-600">{{ number_format($buyer['total_spending'], 0) }}
                                د.ل</span>
                        </div>
                    @empty
                        <p class="text-center text-medical-gray-500 py-8">لا توجد بيانات</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>

    @if ($show(['all', 'sales', 'products']))
        {{-- Top Products --}}
        <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8 mb-8">
            <h2 class="text-xl font-black text-medical-gray-900 mb-6">أكثر المنتجات مبيعاً</h2>
            <div class="space-y-3">
                @forelse($topProducts as $index => $product)
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center text-white font-black">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold text-medical-gray-900">{{ Str::limit($product['name'], 40) }}
                                </p>
                                <p class="text-xs text-medical-gray-500">{{ $product['total_quantity'] }} وحدة مباعة
                                </p>
                            </div>
                        </div>
                        <span
                            class="text-lg font-black text-medical-purple-600">{{ number_format($product['total_revenue'], 0) }}
                            د.ل</span>
                    </div>
                @empty
                    <p class="text-center text-medical-gray-500 py-8">لا توجد بيانات</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Payment & Delivery Statistics --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        @if ($show(['all', 'sales']))
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
                        <span
                            class="text-lg font-black text-medical-green-600">{{ number_format($paymentStats['total_amount'], 0) }}
                            د.ل</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">مكتملة</span>
                        <span
                            class="text-lg font-black text-medical-green-600">{{ $paymentStats['completed'] }}</span>
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
        @endif

        @if ($show(['all', 'sales', 'products']))
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
                        <span
                            class="text-lg font-black text-medical-yellow-600">{{ $deliveryStats['pending'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-blue-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">قيد النقل</span>
                        <span
                            class="text-lg font-black text-medical-blue-600">{{ $deliveryStats['in_transit'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-green-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">تم التسليم</span>
                        <span
                            class="text-lg font-black text-medical-green-600">{{ $deliveryStats['delivered'] }}</span>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-medical-red-50 rounded-xl">
                        <span class="font-semibold text-medical-gray-900">فاشلة</span>
                        <span class="text-lg font-black text-medical-red-600">{{ $deliveryStats['failed'] }}</span>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Chart Script --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const reportType = '{{ $reportType }}';
                const showChart = ['all', 'sales', 'products'].includes(reportType);
                if (!showChart) return;
                const revenueData = @json($revenueTrends);
                const el = document.getElementById('revenueChart');
                if (!el) return;
                const ctx = el.getContext('2d');
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
            });
        </script>
    @endpush

    @push('styles')
    <style>
        @media print {
            /* Hide dashboard chrome: sidebar and top bar */
            aside,
            .flex-1 > header.sticky,
            .no-print { display: none !important; }
            /* Full width content, no padding */
            main#main-content { padding: 0 !important; max-width: 100% !important; }
            body, .min-h-screen, .flex-1 { background: #fff !important; }
            /* Subtle shadows for print */
            .rounded-2xl, .rounded-xl { box-shadow: 0 1px 3px rgba(0,0,0,.12) !important; }
        }
    </style>
    @endpush

</x-dashboard.layout>
