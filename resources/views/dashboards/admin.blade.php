{{-- Admin Dashboard - Matches Landing Page Design Quality --}}
@php
    $stats = [
        'users' => \App\Models\User::count(),
        'suppliers' => \App\Models\Supplier::count(),
        'buyers' => \App\Models\Buyer::count(),
        'products' => 0, // Will be implemented when Product model exists
        'orders' => 0, // Will be implemented when Order model exists
        'revenue' => '0 د.ل', // Will be implemented with real data
    ];

    $recentActivities = [
        [
            'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'iconBg' => 'from-medical-blue-100 to-medical-blue-200',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'مستخدم جديد انضم للمنصة',
            'description' => 'تم تسجيل مورد جديد بنجاح',
            'time' => 'منذ 5 دقائق',
            'badge' => 'جديد',
            'badgeClass' => 'bg-medical-blue-50 text-medical-blue-600',
        ],
        [
            'icon' =>
                'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'طلب جديد تم إنشاؤه',
            'description' => 'طلب شراء معدات طبية بقيمة 15,000 د.ل',
            'time' => 'منذ 15 دقيقة',
            'badge' => 'طلب',
            'badgeClass' => 'bg-medical-green-50 text-medical-green-600',
        ],
        [
            'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
            'iconBg' => 'from-medical-blue-100 to-medical-green-100',
            'iconColor' => 'text-medical-blue-600',
            'title' => 'منتج جديد تمت إضافته',
            'description' => 'جهاز أشعة رقمي - موديل 2024',
            'time' => 'منذ ساعة',
        ],
        [
            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
            'iconBg' => 'from-medical-green-100 to-medical-green-200',
            'iconColor' => 'text-medical-green-600',
            'title' => 'تم اعتماد مورد جديد',
            'description' => 'شركة المعدات الطبية المتقدمة',
            'time' => 'منذ ساعتين',
        ],
    ];

    $quickActions = [
        [
            'title' => 'إضافة مستخدم',
            'description' => 'إنشاء حساب جديد',
            'icon' => 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
            'url' => route('admin.users'),
            'gradient' => 'from-medical-blue-50 to-medical-blue-100',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'إدارة الموردين',
            'description' => 'عرض وإدارة الموردين',
            'icon' =>
                'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
            'url' => route('admin.suppliers'),
            'gradient' => 'from-medical-green-50 to-medical-green-100',
            'iconColor' => 'text-medical-green-600',
            'textColor' => 'text-medical-green-700',
            'descColor' => 'text-medical-green-600',
        ],
        [
            'title' => 'عرض التقارير',
            'description' => 'تقارير وإحصائيات',
            'icon' =>
                'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
            'url' => route('admin.reports'),
            'gradient' => 'from-medical-blue-50 to-medical-green-50',
            'iconColor' => 'text-medical-blue-600',
            'textColor' => 'text-medical-blue-700',
            'descColor' => 'text-medical-blue-600',
        ],
        [
            'title' => 'الإعدادات',
            'description' => 'إعدادات النظام',
            'icon' =>
                'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
            'url' => route('admin.settings.index'),
            'gradient' => 'from-medical-gray-50 to-medical-gray-100',
            'iconColor' => 'text-medical-gray-600',
            'textColor' => 'text-medical-gray-700',
            'descColor' => 'text-medical-gray-600',
        ],
    ];
@endphp

{{-- Welcome Card --}}
<x-dashboard.welcome-card :userName="auth()->user()->name" userType="مدير النظام" message="مرحباً بك في لوحة التحكم"
    gradient="from-medical-blue-500 to-medical-green-500" />

{{-- Statistics Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-6">
    <x-dashboard.stat-card
        icon="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
        label="إجمالي المستخدمين" :value="$stats['users']" trend="up" trendValue="+12%" color="blue" />

    <x-dashboard.stat-card
        icon="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
        label="الموردين" :value="$stats['suppliers']" trend="up" trendValue="+8%" color="green" />

    <x-dashboard.stat-card
        icon="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
        label="المشترين" :value="$stats['buyers']" trend="up" trendValue="+15%" color="blue" />

    <x-dashboard.stat-card icon="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" label="المنتجات"
        :value="$stats['products']" color="green" />

    <x-dashboard.stat-card
        icon="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"
        label="الطلبات" :value="$stats['orders']" color="blue" />

    {{-- <x-dashboard.stat-card
        icon="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
        label="الإيرادات" :value="$stats['revenue']" trend="up" trendValue="+23%" color="green" /> --}}
</div>

{{-- Quick Actions & Recent Activity --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    <x-dashboard.quick-actions :actions="$quickActions" />
    <x-dashboard.activity-list :activities="$recentActivities" />
</div>

{{-- Charts & Calendar --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
    {{-- Platform Activity Chart --}}
    <x-dashboard.chart-card title="نشاط المنصة" subtitle="إحصائيات آخر 7 أيام" chartType="area" :series="[
        ['name' => 'الطلبات', 'data' => [12, 19, 15, 25, 22, 30, 28]],
        ['name' => 'المستخدمين الجدد', 'data' => [8, 12, 10, 15, 18, 20, 16]],
    ]"
        :categories="['السبت', 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة']" :colors="['#0069af', '#199b69']" />

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
