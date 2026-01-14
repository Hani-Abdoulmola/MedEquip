{{-- Buyer Dashboard - Real Data --}}
<x-dashboard.layout title="لوحة التحكم" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
@php
    $organizationName = $buyer->organization_name ?? 'مؤسستك';

    // Build recent activities from real data
    $recentActivities = [];

    // Add recent RFQs to activities
    foreach ($recentRfqs->take(2) as $rfq) {
        $recentActivities[] = [
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'iconBg' => 'from-medical-blue-100 to-medical-blue-200',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'طلب عرض سعر: ' . Str::limit($rfq->title, 30),
            'description' => 'الحالة: ' . __('rfq.status.' . $rfq->status),
            'time' => $rfq->created_at->diffForHumans(),
            'badge' => $rfq->status === 'open' ? 'مفتوح' : ($rfq->status === 'awarded' ? 'تم الترسية' : null),
            'badgeClass' =>
                $rfq->status === 'open'
                    ? 'bg-medical-blue-50 text-medical-blue-600'
                    : 'bg-medical-green-50 text-medical-green-600',
        ];
    }

    // Add recent quotations to activities
    foreach ($recentQuotations->take(2) as $quotation) {
        $recentActivities[] = [
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'عرض سعر من: ' . ($quotation->supplier?->user?->name ?? 'مورد'),
            'description' => 'للطلب: ' . Str::limit($quotation->rfq?->title ?? '', 25),
            'time' => $quotation->created_at->diffForHumans(),
            'badge' =>
                $quotation->status === 'pending'
                    ? 'قيد المراجعة'
                    : ($quotation->status === 'accepted'
                        ? 'مقبول'
                        : null),
            'badgeClass' =>
                $quotation->status === 'pending'
                    ? 'bg-yellow-50 text-yellow-600'
                    : 'bg-medical-green-50 text-medical-green-600',
        ];
    }

    // Quick Actions
    $quickActions = [
        [
            'title' => 'تصفح المنتجات',
            'description' => 'ابحث عن معدات طبية',
            'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
            'url' => route('buyer.products.index'),
            'gradient' => 'from-medical-blue-50 to-medical-blue-100',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'إنشاء طلب عرض سعر',
            'description' => 'أنشئ RFQ جديد',
            'icon' => 'M12 4v16m8-8H4',
            'url' => route('buyer.rfqs.create'),
            'gradient' => 'from-medical-green-50 to-medical-green-100',
            'iconColor' => 'text-medical-green-600',
            'textColor' => 'text-medical-green-700',
            'descColor' => 'text-medical-green-600',
        ],
        [
            'title' => 'طلباتي',
            'description' => 'عرض طلبات عروض الأسعار',
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'url' => route('buyer.rfqs.index'),
            'gradient' => 'from-purple-50 to-purple-100',
            'iconColor' => 'text-purple-600',
            'textColor' => 'text-purple-700',
            'descColor' => 'text-purple-600',
        ],
        [
            'title' => 'عروض الأسعار',
            'description' => 'مراجعة عروض الموردين',
            'icon' =>
                'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'url' => route('buyer.quotations.index'),
            'gradient' => 'from-orange-50 to-orange-100',
            'iconColor' => 'text-orange-600',
            'textColor' => 'text-orange-700',
            'descColor' => 'text-orange-600',
        ],
        [
            'title' => 'المفضلة',
            'description' => 'المنتجات المحفوظة',
            'icon' =>
                'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'url' => route('buyer.products.favorites'),
            'gradient' => 'from-medical-red-50 to-medical-red-100',
            'iconColor' => 'text-medical-red-600',
            'textColor' => 'text-medical-red-700',
            'descColor' => 'text-medical-red-600',
        ],
        [
            'title' => 'الملف الشخصي',
            'description' => 'إدارة معلومات المنظمة',
            'icon' =>
                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'url' => route('buyer.profile.show'),
            'gradient' => 'from-gray-50 to-gray-100',
            'iconColor' => 'text-gray-600',
            'textColor' => 'text-gray-700',
            'descColor' => 'text-gray-600',
        ],
    ];
@endphp

{{-- Welcome Card --}}
<x-dashboard.welcome-card :userName="auth()->user()->name" userType="مشتري" message="مرحباً بك"
    gradient="from-medical-blue-500 to-medical-blue-600">
    <div class="flex items-center space-x-3 space-x-reverse text-white/90">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span class="text-sm font-medium">{{ $organizationName }}</span>
    </div>
</x-dashboard.welcome-card>

{{-- Pending Quotations Alert --}}
@if ($pendingQuotations->count() > 0)
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-xl p-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-semibold text-yellow-800">لديك {{ $pendingQuotations->count() }} عروض أسعار تنتظر
                        مراجعتك</h4>
                    <p class="text-sm text-yellow-600">راجع العروض واختر الأنسب لمتطلباتك</p>
                </div>
            </div>
            <a href="{{ route('buyer.quotations.index') }}"
                class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors text-sm font-medium">
                مراجعة العروض
            </a>
        </div>
    </div>
@endif

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">
    <x-dashboard.stat-card
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
        label="طلبات عروض الأسعار" :value="$stats['rfqs']['total']" :subtitle="$stats['rfqs']['open'] . ' مفتوح'" color="blue" />

    <x-dashboard.stat-card
        icon="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
        label="عروض الأسعار المستلمة" :value="$stats['quotations']['total']" :subtitle="$stats['quotations']['pending'] . ' قيد المراجعة'" color="green" />

    <x-dashboard.stat-card icon="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" label="الطلبات" :value="$stats['orders']['total']"
        :subtitle="$stats['orders']['pending'] . ' قيد التنفيذ'" color="purple" />

    <x-dashboard.stat-card
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        label="إجمالي الإنفاق" :value="$stats['orders']['total_spending']" color="orange" />
</div>

{{-- Additional Stats Row --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mt-6">
    <x-dashboard.stat-card icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" label="طلبات تم ترسيتها"
        :value="$stats['rfqs']['awarded']" color="green" />

    <x-dashboard.stat-card
        icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
        label="المفضلة" :value="$stats['favorites']" color="red" />

    <x-dashboard.stat-card icon="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"
        label="طلبات تم تسليمها" :value="$stats['orders']['delivered']" color="blue" />
</div>

{{-- Quick Actions & Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <x-dashboard.quick-actions :actions="$quickActions" />

    @if (count($recentActivities) > 0)
        <x-dashboard.activity-list title="النشاطات الأخيرة" :activities="$recentActivities" />
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">النشاطات الأخيرة</h3>
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
                <p class="text-gray-500">لا توجد نشاطات حديثة</p>
                <a href="{{ route('buyer.rfqs.create') }}"
                    class="mt-4 inline-block text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                    أنشئ أول طلب عرض سعر ←
                </a>
            </div>
        </div>
    @endif
</div>

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Spending Chart --}}
    <x-dashboard.chart-card title="الإنفاق" subtitle="إحصائيات الإنفاق آخر 7 أيام" chartType="line" :series="[['name' => 'الإنفاق (د.ل)', 'data' => $spendingTrend['data']]]"
        :categories="$spendingTrend['labels']" :colors="['#0069af']" />

    {{-- Calendar Widget --}}
    <x-dashboard.calendar-card title="التقويم" :events="$upcomingEvents" />
</div>

{{-- Recent RFQs Table --}}
@if ($recentRfqs->count() > 0)
    <div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">آخر طلبات عروض الأسعار</h3>
                <a href="{{ route('buyer.rfqs.index') }}"
                    class="text-medical-blue-600 hover:text-medical-blue-700 text-sm font-medium">
                    عرض الكل ←
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الرمز المرجعي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العنوان</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عروض
                            الأسعار</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الموعد النهائي</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($recentRfqs as $rfq)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-900">{{ $rfq->reference_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-900">{{ Str::limit($rfq->title, 40) }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'under_review' => 'bg-yellow-100 text-yellow-800',
                                        'closed' => 'bg-gray-100 text-gray-800',
                                        'awarded' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'open' => 'مفتوح',
                                        'under_review' => 'قيد المراجعة',
                                        'closed' => 'مغلق',
                                        'awarded' => 'تم الترسية',
                                        'cancelled' => 'ملغي',
                                    ];
                                @endphp
                                <span
                                    class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="text-sm text-gray-900">{{ $rfq->quotations_count ?? $rfq->quotations->count() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if ($rfq->deadline)
                                    <span
                                        class="text-sm {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $rfq->deadline->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('buyer.rfqs.show', $rfq) }}"
                                    class="text-medical-blue-600 hover:text-medical-blue-800 text-sm font-medium">
                                    عرض
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
</x-dashboard.layout>
