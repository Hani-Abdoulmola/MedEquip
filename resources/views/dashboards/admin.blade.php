{{-- Admin Dashboard - Matches Landing Page Design Quality --}}
<x-dashboard.layout title="لوحة التحكم" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    @php
        // Stats, trends, activities, and quick actions are now passed from AdminDashboardController
        $usersTrendValue = ($usersTrend ?? 0) > 0 ? '+' . $usersTrend . '%' : ($usersTrend ?? 0) . '%';
        $suppliersTrendValue = ($suppliersTrend ?? 0) > 0 ? '+' . $suppliersTrend . '%' : ($suppliersTrend ?? 0) . '%';
        $buyersTrendValue = ($buyersTrend ?? 0) > 0 ? '+' . $buyersTrend . '%' : ($buyersTrend ?? 0) . '%';
    @endphp

    {{-- Welcome Card --}}
<x-dashboard.welcome-card :userName="auth()->user()->name" userType="مدير النظام" message="مرحباً بك في لوحة التحكم"
    gradient="from-medical-blue-500 to-medical-green-500" />

{{-- Empty State Alert --}}
@if($stats['suppliers'] == 0 || $stats['products'] == 0)
    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-2 border-yellow-200 rounded-2xl p-6 mt-6 shadow-medical">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-yellow-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-lg font-bold text-gray-900 mb-2">لا توجد بيانات في النظام</h3>
                <p class="text-gray-700 mb-4">
                    @if($stats['suppliers'] == 0 && $stats['products'] == 0)
                        لم يتم إنشاء موردين أو منتجات بعد. قم بتشغيل seeder لإنشاء بيانات تجريبية.
                    @elseif($stats['suppliers'] == 0)
                        لم يتم إنشاء موردين بعد.
                    @else
                        لم يتم إنشاء منتجات بعد.
                    @endif
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('admin.suppliers.create') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        إضافة مورد
                    </a>
                    <span class="text-gray-500 self-center">أو</span>
                    <code class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg font-mono text-sm self-center">
                        php artisan db:seed --class=ProductCatalogSeeder
                    </code>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    <x-dashboard.stat-card
        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
        label="إجمالي المستخدمين" :value="$stats['users']" :trend="($usersTrend ?? 0) >= 0 ? 'up' : 'down'" :trendValue="$usersTrendValue" color="blue" 
        :url="route('admin.users')" />

    <x-dashboard.stat-card
        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
        label="الموردين" :value="$stats['suppliers']" :trend="($suppliersTrend ?? 0) >= 0 ? 'up' : 'down'" :trendValue="$suppliersTrendValue" color="green" 
        :url="route('admin.suppliers')" />

    <x-dashboard.stat-card
        icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
        label="المشترين" :value="$stats['buyers']" :trend="($buyersTrend ?? 0) >= 0 ? 'up' : 'down'" :trendValue="$buyersTrendValue" color="blue" />

    <x-dashboard.stat-card icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" label="المنتجات"
        :value="$stats['products']" color="green" 
        :url="route('admin.products.index')" />

    <x-dashboard.stat-card
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
        label="الطلبات" :value="$stats['orders']" color="blue" />

    <x-dashboard.stat-card
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        label="الإيرادات" :value="$stats['revenue']" color="green" />
</div>

{{-- Quick Actions & Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <x-dashboard.quick-actions :actions="$quickActions" />
    <x-dashboard.activity-list :activities="$recentActivities" />
</div>

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Platform Activity Chart --}}
    <x-dashboard.chart-card title="نشاط المنصة" subtitle="إحصائيات آخر 7 أيام" chartType="area" :series="$chartSeries ?? []"
        :categories="$chartCategories ?? []" :colors="['#0069af', '#199b69']" />

    {{-- Calendar Widget --}}
    <x-dashboard.calendar-card title="التقويم" :events="[
        [
            'title' => 'اجتماع مع الموردين',
            'date' => date('Y-m-d', strtotime('+2 days')),
            'color' => 'bg-medical-blue-500',
        ],
        ['title' => 'مراجعة الطلبات', 'date' => date('Y-m-d', strtotime('+5 days')), 'color' => 'bg-medical-green-500'],
        ['title' => 'تقرير شهري', 'date' => date('Y-m-d', strtotime('+7 days')), 'color' => 'bg-medical-red-500'],
    ]" />
</div>
</x-dashboard.layout>
