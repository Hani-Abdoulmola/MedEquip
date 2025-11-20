{{-- Supplier Dashboard - Matches Landing Page Design Quality --}}
@php
    $supplier = auth()->user()->supplierProfile;
    $companyName = $supplier->company_name ?? 'شركتك';

    $stats = [
        'products' => 0, // Will be implemented when Product model exists
        'orders' => 0, // Will be implemented when Order model exists
        'pending' => 0,
        'completed' => 0,
        'revenue' => '0 د.ل',
        'avgOrder' => '0 د.ل',
    ];

    $recentActivities = [
        [
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'طلب جديد تم استلامه',
            'description' => 'طلب شراء معدات طبية بقيمة 8,500 د.ل',
            'time' => 'منذ 10 دقائق',
            'badge' => 'جديد',
            'badgeClass' => 'bg-medical-green-50 text-medical-green-600',
        ],
        [
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'iconBg' => 'from-medical-blue-100 to-medical-blue-200',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'منتج جديد تمت إضافته',
            'description' => 'جهاز قياس ضغط الدم الرقمي',
            'time' => 'منذ ساعة',
        ],
        [
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'تم تأكيد طلب',
            'description' => 'طلب رقم #12345 - 12,000 د.ل',
            'time' => 'منذ ساعتين',
            'badge' => 'مؤكد',
            'badgeClass' => 'bg-medical-green-50 text-medical-green-600',
        ],
        [
            'icon' =>
                'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'from-medical-blue-100 to-medical-green-100',
            'iconColor' => 'text-medical-green-600',
            'title' => 'تم استلام دفعة',
            'description' => 'دفعة بقيمة 25,000 د.ل',
            'time' => 'منذ 3 ساعات',
        ],
    ];

    $quickActions = [
        [
            'title' => 'إضافة منتج جديد',
            'description' => 'أضف منتج للكتالوج',
            'icon' => 'M12 6v6m0 0v6m0-6h6m-6 0H6',
            'url' => route('supplier.products'),
            'gradient' => 'from-medical-green-50 to-medical-green-100',
            'iconColor' => 'text-medical-green-600',
            'textColor' => 'text-medical-green-700',
            'descColor' => 'text-medical-green-600',
        ],
        [
            'title' => 'إدارة الطلبات',
            'description' => 'عرض وإدارة الطلبات',
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'url' => route('supplier.orders'),
            'gradient' => 'from-medical-blue-50 to-medical-blue-100',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'تقارير المبيعات',
            'description' => 'عرض الإحصائيات',
            'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6',
            'url' => route('supplier.sales'),
            'gradient' => 'from-medical-blue-50 to-medical-green-50',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'الملف الشخصي',
            'description' => 'تحديث معلومات الشركة',
            'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
            'url' => route('supplier.profile'),
            'gradient' => 'from-medical-gray-50 to-medical-gray-100',
            'iconColor' => 'text-medical-gray-600',
            'textColor' => 'text-medical-gray-700',
            'descColor' => 'text-medical-gray-600',
        ],
    ];
@endphp

{{-- Welcome Card --}}
<x-dashboard.welcome-card :userName="auth()->user()->name" userType="مورد" message="مرحباً بك"
    gradient="from-medical-green-500 to-medical-blue-500">
    <div class="flex items-center space-x-3 space-x-reverse text-white/90">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
        <span class="text-sm font-medium">{{ $companyName }}</span>
    </div>
</x-dashboard.welcome-card>

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    <x-dashboard.stat-card icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" label="منتجاتي"
        :value="$stats['products']" color="green" />

    <x-dashboard.stat-card
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
        label="إجمالي الطلبات" :value="$stats['orders']" trend="up" trendValue="+18%" color="blue" />

    <x-dashboard.stat-card icon="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" label="طلبات قيد الانتظار"
        :value="$stats['pending']" color="blue" />

    <x-dashboard.stat-card icon="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" label="طلبات مكتملة" :value="$stats['completed']"
        trend="up" trendValue="+25%" color="green" />

    <x-dashboard.stat-card
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        label="الإيرادات" :value="$stats['revenue']" trend="up" trendValue="+32%" color="green" />

    <x-dashboard.stat-card icon="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" label="متوسط قيمة الطلب" :value="$stats['avgOrder']"
        color="blue" />
</div>

{{-- Quick Actions & Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <x-dashboard.quick-actions :actions="$quickActions" />
    <x-dashboard.activity-list title="النشاطات الأخيرة" :activities="$recentActivities" />
</div>

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Sales Chart --}}
    <x-dashboard.chart-card title="المبيعات" subtitle="إحصائيات المبيعات آخر 7 أيام" chartType="bar" :series="[['name' => 'المبيعات (د.ل)', 'data' => [8500, 12000, 9500, 15000, 11000, 18000, 14500]]]"
        :categories="['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة']" :colors="['#199b69']" />

    {{-- Calendar Widget --}}
    <x-dashboard.calendar-card title="التقويم" :events="[
        [
            'title' => 'موعد تسليم طلب #12345',
            'date' => date('Y-m-d', strtotime('+1 day')),
            'color' => 'bg-medical-green-500',
        ],
        ['title' => 'اجتماع مع مشتري', 'date' => date('Y-m-d', strtotime('+3 days')), 'color' => 'bg-medical-blue-500'],
        ['title' => 'جرد المخزون', 'date' => date('Y-m-d', strtotime('+6 days')), 'color' => 'bg-medical-red-500'],
    ]" />
</div>
