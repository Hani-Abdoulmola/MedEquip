{{-- Buyer Reports Dashboard --}}
<x-dashboard.layout title="التقارير والتحليلات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">التقارير والتحليلات</h1>
                <p class="mt-1 text-sm text-gray-500">تحليل شامل للمشتريات والإنفاق</p>
            </div>
            
            {{-- Period Filter --}}
            <form method="GET" action="{{ route('buyer.reports.index') }}" class="flex items-center gap-3">
                <select name="period" onchange="this.form.submit()" 
                        class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm">
                    <option value="today" {{ $period == 'today' ? 'selected' : '' }}>اليوم</option>
                    <option value="this_week" {{ $period == 'this_week' ? 'selected' : '' }}>هذا الأسبوع</option>
                    <option value="this_month" {{ $period == 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="last_month" {{ $period == 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                    <option value="this_quarter" {{ $period == 'this_quarter' ? 'selected' : '' }}>هذا الربع</option>
                    <option value="this_year" {{ $period == 'this_year' ? 'selected' : '' }}>هذه السنة</option>
                </select>
            </form>
        </div>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Total Spending --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-medical-blue-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    @if($spendingSummary['spending_change'] != 0)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $spendingSummary['spending_change'] > 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $spendingSummary['spending_change'] > 0 ? '↑' : '↓' }}
                            {{ abs($spendingSummary['spending_change']) }}%
                        </span>
                    @endif
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($spendingSummary['total_spending'], 2) }} <span class="text-sm font-normal text-gray-500">د.ل</span></div>
                <p class="text-sm text-gray-500 mt-1">إجمالي الإنفاق</p>
            </div>

            {{-- Total Orders --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-medical-green-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $spendingSummary['total_orders'] }}</div>
                <p class="text-sm text-gray-500 mt-1">عدد الطلبات</p>
            </div>

            {{-- Average Order Value --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($spendingSummary['avg_order_value'], 2) }} <span class="text-sm font-normal text-gray-500">د.ل</span></div>
                <p class="text-sm text-gray-500 mt-1">متوسط قيمة الطلب</p>
            </div>

            {{-- Unique Suppliers --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $spendingSummary['unique_suppliers'] }}</div>
                <p class="text-sm text-gray-500 mt-1">موردين تم التعامل معهم</p>
            </div>
        </div>

        {{-- Charts Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Monthly Spending Chart --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">الإنفاق الشهري</h3>
                <x-dashboard.chart-card 
                    title="" 
                    subtitle="آخر 12 شهر" 
                    chartType="area" 
                    :series="[['name' => 'الإنفاق (د.ل)', 'data' => $monthlySpending['data']]]"
                    :categories="$monthlySpending['labels']" 
                    :colors="['#0069af']" 
                    :showTitle="false" />
            </div>

            {{-- RFQ Funnel --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">أداء طلبات العروض</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">إجمالي الطلبات</span>
                        <span class="font-bold text-gray-900">{{ $rfqStats['total'] }}</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                        @php
                            $awardedPct = $rfqStats['total'] > 0 ? ($rfqStats['awarded'] / $rfqStats['total']) * 100 : 0;
                            $closedPct = $rfqStats['total'] > 0 ? ($rfqStats['closed'] / $rfqStats['total']) * 100 : 0;
                            $openPct = $rfqStats['total'] > 0 ? ($rfqStats['open'] / $rfqStats['total']) * 100 : 0;
                        @endphp
                        <div class="h-full flex">
                            <div class="bg-green-500 h-full" style="width: {{ $awardedPct }}%"></div>
                            <div class="bg-gray-400 h-full" style="width: {{ $closedPct }}%"></div>
                            <div class="bg-blue-500 h-full" style="width: {{ $openPct }}%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 text-center text-sm">
                        <div>
                            <div class="flex items-center justify-center gap-1">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                                <span class="text-gray-600">تم الترسية</span>
                            </div>
                            <p class="font-bold text-gray-900 mt-1">{{ $rfqStats['awarded'] }}</p>
                        </div>
                        <div>
                            <div class="flex items-center justify-center gap-1">
                                <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                                <span class="text-gray-600">مفتوحة</span>
                            </div>
                            <p class="font-bold text-gray-900 mt-1">{{ $rfqStats['open'] }}</p>
                        </div>
                        <div>
                            <div class="flex items-center justify-center gap-1">
                                <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                <span class="text-gray-600">مغلقة</span>
                            </div>
                            <p class="font-bold text-gray-900 mt-1">{{ $rfqStats['closed'] }}</p>
                        </div>
                    </div>
                    
                    <div class="pt-4 border-t border-gray-100 grid grid-cols-2 gap-4">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-medical-green-600">{{ $rfqStats['conversion_rate'] }}%</p>
                            <p class="text-xs text-gray-500">معدل التحويل</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-medical-blue-600">{{ $rfqStats['response_rate'] }}%</p>
                            <p class="text-xs text-gray-500">معدل الاستجابة</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Second Row --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Top Suppliers --}}
            <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">أفضل الموردين</h3>
                    <p class="text-sm text-gray-500">حسب حجم المشتريات</p>
                </div>
                @if(count($topSuppliers) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المورد</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطلبات</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المتوسط</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($topSuppliers as $index => $supplier)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 bg-medical-blue-100 rounded-lg flex items-center justify-center">
                                                    <span class="text-sm font-bold text-medical-blue-600">{{ $index + 1 }}</span>
                                                </div>
                                                <span class="font-medium text-gray-900">{{ $supplier['supplier_name'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">{{ $supplier['order_count'] }}</td>
                                        <td class="px-6 py-4 font-medium text-gray-900">{{ number_format($supplier['total_amount'], 2) }} د.ل</td>
                                        <td class="px-6 py-4 text-gray-600">{{ number_format($supplier['avg_order_value'], 2) }} د.ل</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-12 text-center text-gray-500">
                        <p>لا توجد بيانات للفترة المحددة</p>
                    </div>
                @endif
            </div>

            {{-- Quotation Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">عروض الأسعار</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                        <span class="text-gray-600">المستلمة</span>
                        <span class="text-xl font-bold text-gray-900">{{ $quotationStats['total_received'] }}</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <p class="text-lg font-bold text-yellow-700">{{ $quotationStats['pending'] }}</p>
                            <p class="text-xs text-yellow-600">قيد المراجعة</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <p class="text-lg font-bold text-green-700">{{ $quotationStats['accepted'] }}</p>
                            <p class="text-xs text-green-600">مقبول</p>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg">
                            <p class="text-lg font-bold text-red-700">{{ $quotationStats['rejected'] }}</p>
                            <p class="text-xs text-red-600">مرفوض</p>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">معدل القبول</span>
                            <span class="font-bold text-green-600">{{ $quotationStats['acceptance_rate'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" style="width: {{ $quotationStats['acceptance_rate'] }}%"></div>
                        </div>
                    </div>
                    <div class="pt-3 text-sm">
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500">متوسط قيمة العرض</span>
                            <span class="font-medium">{{ number_format($quotationStats['avg_quote_amount'], 2) }} د.ل</span>
                        </div>
                        <div class="flex justify-between mb-1">
                            <span class="text-gray-500">أقل عرض</span>
                            <span class="font-medium text-green-600">{{ number_format($quotationStats['min_quote_amount'], 2) }} د.ل</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-500">أعلى عرض</span>
                            <span class="font-medium text-red-600">{{ number_format($quotationStats['max_quote_amount'], 2) }} د.ل</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Fulfillment Metrics --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6">مؤشرات تنفيذ الطلبات</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ $fulfillmentMetrics['total_orders'] }}</p>
                    <p class="text-sm text-gray-500">إجمالي الطلبات</p>
                </div>
                <div class="text-center p-4 bg-green-50 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $fulfillmentMetrics['delivered'] }}</p>
                    <p class="text-sm text-gray-500">تم التسليم</p>
                </div>
                <div class="text-center p-4 bg-yellow-50 rounded-xl">
                    <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-2xl font-bold text-yellow-600">{{ $fulfillmentMetrics['in_progress'] }}</p>
                    <p class="text-sm text-gray-500">قيد التنفيذ</p>
                </div>
                <div class="text-center p-4 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                        <span class="text-xl font-bold text-gray-600">{{ $fulfillmentMetrics['fulfillment_rate'] }}%</span>
                    </div>
                    <p class="text-lg font-bold text-gray-900">معدل الإنجاز</p>
                    <p class="text-sm text-gray-500">نسبة الطلبات المسلمة</p>
                </div>
            </div>
        </div>

        {{-- Spending by Category --}}
        @if(count($spendingByCategory) > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-bold text-gray-900">الإنفاق حسب الفئة</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الفئة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الطلبات</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المبلغ</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">النسبة</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php
                                $totalCategorySpending = collect($spendingByCategory)->sum('total_amount');
                            @endphp
                            @foreach($spendingByCategory as $category)
                                @php
                                    $percentage = $totalCategorySpending > 0 
                                        ? ($category->total_amount / $totalCategorySpending) * 100 
                                        : 0;
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $category->category_name }}</td>
                                    <td class="px-6 py-4 text-gray-600">{{ $category->order_count }}</td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ number_format($category->total_amount, 2) }} د.ل</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-100 rounded-full h-2 max-w-[100px]">
                                                <div class="bg-medical-blue-500 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600">{{ round($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-dashboard.layout>

