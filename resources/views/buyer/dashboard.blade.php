{{-- Buyer Dashboard - Matches Landing Page Design Quality --}}
@php
    $buyer = auth()->user()->buyerProfile;
    $organizationName = $buyer->organization_name ?? 'مؤسستك';

    $stats = [
        'orders' => 0, // Will be implemented when Order model exists
        'pending' => 0,
        'completed' => 0,
        'favorites' => 0,
        'suppliers' => 0,
        'spending' => '0 د.ل',
    ];

    $recentActivities = [
        [
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'iconBg' => 'from-medical-blue-100 to-medical-blue-200',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'طلب جديد تم إنشاؤه',
            'description' => 'طلب معدات طبية بقيمة 18,500 د.ل',
            'time' => 'منذ 5 دقائق',
            'badge' => 'جديد',
            'badgeClass' => 'bg-medical-blue-50 text-medical-blue-600',
        ],
        [
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'تم تأكيد طلب',
            'description' => 'طلب رقم #12346 تم تأكيده من المورد',
            'time' => 'منذ ساعة',
            'badge' => 'مؤكد',
            'badgeClass' => 'bg-medical-green-50 text-medical-green-600',
        ],
        [
            'icon' =>
                'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'iconBg' => 'from-medical-red-100 to-medical-red-200',
            'iconColor' => 'text-medical-red-600',
            'title' => 'منتج جديد في المفضلة',
            'description' => 'جهاز تعقيم طبي متقدم',
            'time' => 'منذ ساعتين',
        ],
        [
            'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4',
            'iconBg' => 'from-medical-blue-100 to-medical-green-100',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'تم شحن طلب',
            'description' => 'طلب رقم #12340 في الطريق',
            'time' => 'منذ 4 ساعات',
            'badge' => 'شحن',
            'badgeClass' => 'bg-medical-blue-50 text-medical-blue-600',
        ],
    ];

    $quickActions = [
        [
            'title' => 'تصفح المنتجات',
            'description' => 'ابحث عن معدات طبية',
            'icon' => 'M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z',
            'url' => '#',
            'gradient' => 'from-medical-blue-50 to-medical-blue-100',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'طلباتي',
            'description' => 'عرض وإدارة الطلبات',
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'url' => route('buyer.orders'),
            'gradient' => 'from-medical-green-50 to-medical-green-100',
            'iconColor' => 'text-medical-green-600',
            'textColor' => 'text-medical-green-700',
            'descColor' => 'text-medical-green-600',
        ],
        [
            'title' => 'المفضلة',
            'description' => 'المنتجات المحفوظة',
            'icon' =>
                'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
            'url' => route('buyer.favorites'),
            'gradient' => 'from-medical-red-50 to-medical-red-100',
            'iconColor' => 'text-medical-red-600',
            'textColor' => 'text-medical-red-700',
            'descColor' => 'text-medical-red-600',
        ],
        [
            'title' => 'الموردين',
            'description' => 'تصفح الموردين',
            'icon' =>
                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'url' => route('buyer.suppliers'),
            'gradient' => 'from-medical-blue-50 to-medical-green-50',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
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

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    <x-dashboard.stat-card
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
        label="طلباتي" :value="$stats['orders']" trend="up" trendValue="+12%" color="blue" />

    <x-dashboard.stat-card icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" label="طلبات قيد الانتظار"
        :value="$stats['pending']" color="blue" />

    <x-dashboard.stat-card icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" label="طلبات مكتملة" :value="$stats['completed']"
        trend="up" trendValue="+20%" color="green" />

    <x-dashboard.stat-card
        icon="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"
        label="المفضلة" :value="$stats['favorites']" color="red" />

    <x-dashboard.stat-card
        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
        label="الموردين" :value="$stats['suppliers']" color="green" />

    <x-dashboard.stat-card
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        label="إجمالي الإنفاق" :value="$stats['spending']" trend="up" trendValue="+15%" color="blue" />
</div>

{{-- Quick Actions & Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <x-dashboard.quick-actions :actions="$quickActions" />
    <x-dashboard.activity-list title="النشاطات الأخيرة" :activities="$recentActivities" />
</div>

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Spending Chart --}}
    <x-dashboard.chart-card title="الإنفاق" subtitle="إحصائيات الإنفاق آخر 7 أيام" chartType="line" :series="[['name' => 'الإنفاق (د.ل)', 'data' => [5000, 8500, 6000, 12000, 9500, 15000, 11000]]]"
        :categories="['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة']" :colors="['#0069af']" />

    {{-- Calendar Widget --}}
    <x-dashboard.calendar-card title="التقويم" :events="[
        [
            'title' => 'استلام طلب #12346',
            'date' => date('Y-m-d', strtotime('+2 days')),
            'color' => 'bg-medical-blue-500',
        ],
        [
            'title' => 'مراجعة عروض الأسعار',
            'date' => date('Y-m-d', strtotime('+4 days')),
            'color' => 'bg-medical-green-500',
        ],
        ['title' => 'اجتماع مع مورد', 'date' => date('Y-m-d', strtotime('+8 days')), 'color' => 'bg-medical-blue-500'],
    ]" />
</div>
