{{-- Dashboard Sidebar Component - Matches Landing Page Design Quality --}}
@props(['userRole' => 'admin', 'userName' => '', 'userType' => 'مستخدم'])

@php
    // حساب عدد طلبات التسجيل المعلقة للشارة
    $pendingCount = 0;
    if ($userRole === 'admin') {
        $pendingSuppliers = \App\Models\Supplier::where('is_verified', false)->whereNull('rejection_reason')->count();
        $pendingBuyers = \App\Models\Buyer::where('is_verified', false)->whereNull('rejection_reason')->count();
        $pendingCount = $pendingSuppliers + $pendingBuyers;
    }

    // ◀ تم إعادة ترتيب العناصر حسب الصلة، وتجميعهم في قوائم منسدلة وتصنيفهم بتسميات مناسبة

    $menuItems = [
        'admin' => [
            [
                'dropdown' => true,
                'label' => 'إدارة النظام',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'items' => [
                    [
                        'route' => 'dashboard',
                        'icon' =>
                            'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'label' => 'لوحة التحكم',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'المستخدمون & التسجيلات',
                'icon' =>
                    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'items' => [
                    [
                        'route' => 'admin.suppliers',
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'label' => 'الموردين',
                    ],
                    [
                        'route' => 'admin.buyers',
                        'icon' =>
                            'M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z',
                        'label' => 'المشترين',
                    ],
                    [
                        'route' => 'admin.registrations.pending',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                        'label' => 'طلبات التسجيل',
                        'badge' => true,
                    ],
                    [
                        'route' => 'admin.users',
                        'icon' =>
                            'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                        'label' => 'كل المستخدمين',
                        'permission' => 'users.view',
                    ],
                    [
                        'route' => 'admin.roles.index',
                        'icon' =>
                            'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                        'label' => 'الأدوار',
                        'role' => 'Admin',
                    ],
                    [
                        'route' => 'admin.permissions.index',
                        'icon' =>
                            'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z',
                        'label' => 'الصلاحيات',
                        'role' => 'Admin',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'المنتجات',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'items' => [
                    [
                        'route' => 'admin.products.index',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        'label' => 'المنتجات',
                    ],
                    [
                        'route' => 'admin.categories.index',
                        'icon' =>
                            'M7 7h.01M7 3h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 11h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 19h5c1.1046 0 2 .8954 2 2v0',
                        'label' => 'فئات المنتجات',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'العروض والطلبات',
                'icon' =>
                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'items' => [
                    [
                        'route' => 'admin.orders',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'label' => 'الطلبات',
                    ],
                    [
                        'route' => 'admin.rfqs.index',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'label' => 'طلبات عروض الأسعار',
                    ],
                    [
                        'route' => 'admin.quotations.index',
                        'icon' =>
                            'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                        'label' => 'عروض الأسعار',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'الإعدادات والتقارير',
                'icon' =>
                    'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'items' => [
                    [
                        'route' => 'admin.settings.index',
                        'icon' =>
                            'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                        'label' => 'الإعدادات',
                    ],
                    [
                        'route' => 'admin.reports',
                        'icon' =>
                            'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                        'label' => 'التقارير',
                    ],
                    [
                        'route' => 'admin.activity',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'label' => 'سجل النشاط',
                    ],
                    [
                        'route' => 'admin.notifications.index',
                        'icon' =>
                            'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                        'label' => 'الإشعارات',
                    ],
                ],
            ],
        ],
        // باقي القوائم كما هي ...
        'supplier' => [
            [
                'dropdown' => true,
                'label' => 'العمليات',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'items' => [
                    [
                        'route' => 'dashboard',
                        'icon' =>
                            'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'label' => 'لوحة التحكم',
                    ],
                    [
                        'route' => 'supplier.products.index',
                        'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                        'label' => 'منتجاتي',
                    ],
                    [
                        'route' => 'supplier.orders.index',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'label' => 'الطلبات',
                    ],
                    [
                        'route' => 'supplier.deliveries.index',
                        'icon' =>
                            'M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2',
                        'label' => 'التوصيل',
                    ],
                    [
                        'route' => 'supplier.invoices.index',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'label' => 'الفواتير',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'العروض والمناقصات',
                'icon' =>
                    'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                'items' => [
                    [
                        'route' => 'supplier.rfqs.index',
                        'icon' =>
                            'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
                        'label' => 'طلبات عروض الأسعار',
                    ],
                    [
                        'route' => 'supplier.quotations.index',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                        'label' => 'عروضي المقدمة',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'الإشعارات والحساب',
                'icon' =>
                    'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                'items' => [
                    [
                        'route' => 'supplier.notifications.index',
                        'icon' =>
                            'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9',
                        'label' => 'الإشعارات',
                    ],
                    [
                        'route' => 'supplier.profile.show',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'label' => 'الملف الشخصي',
                    ],
                ],
            ],
        ],
        'buyer' => [
            [
                'dropdown' => true,
                'label' => 'منصة المشتري',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'items' => [
                    [
                        'route' => 'dashboard',
                        'icon' =>
                            'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                        'label' => 'لوحة التحكم',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'طلباتي',
                'icon' =>
                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                'items' => [
                    [
                        'route' => 'buyer.orders',
                        'icon' =>
                            'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                        'label' => 'طلباتي',
                    ],
                    [
                        'route' => 'buyer.favorites',
                        'icon' =>
                            'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z',
                        'label' => 'المفضلة',
                    ],
                ],
            ],
            [
                'dropdown' => true,
                'label' => 'الحساب والموردين',
                'icon' =>
                    'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                'items' => [
                    [
                        'route' => 'buyer.suppliers',
                        'icon' =>
                            'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4',
                        'label' => 'الموردين',
                    ],
                    [
                        'route' => 'buyer.profile',
                        'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                        'label' => 'الملف الشخصي',
                    ],
                ],
            ],
        ],
    ];

    $currentRoute = request()->route()->getName();
    $items = $menuItems[$userRole] ?? $menuItems['admin'];
@endphp

{{-- Desktop Sidebar --}}
<aside class="hidden lg:flex lg:flex-col lg:w-72 bg-white border-l border-medical-gray-200 shadow-medical"
    x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform -translate-x-full"
    x-transition:enter-end="opacity-100 transform translate-x-0" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform -translate-x-full">

    {{-- Logo --}}
    <div class="flex items-center justify-center px-6 py-6">
        <div class="flex items-center space-x-3 space-x-reverse">
            <x-application-logo class="h-12 w-12 shadow-medical" />
            <span
                class="text-2xl font-bold font-display bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">MediEquip</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-6 space-y-1">
        @foreach ($items as $item)
            @php
                $canAccess = true;
                if (isset($item['permission'])) {
                    $canAccess = auth()->user()->can($item['permission']);
                } elseif (isset($item['role'])) {
                    $canAccess = auth()->user()->hasRole($item['role']);
                }
            @endphp

            @if (!empty($item['dropdown']) && $item['dropdown'])
                @php
                    $activeDropdown = false;
                    $hasAccessibleItems = false;
                    foreach ($item['items'] as $sub) {
                        if ($currentRoute === ($sub['route'] ?? '')) {
                            $activeDropdown = true;
                        }
                        // Check if at least one item is accessible
                        $subCanAccess = true;
                        if (isset($sub['permission'])) {
                            $subCanAccess = auth()->user()->can($sub['permission']);
                        } elseif (isset($sub['role'])) {
                            $subCanAccess = auth()->user()->hasRole($sub['role']);
                        }
                        if ($subCanAccess) {
                            $hasAccessibleItems = true;
                        }
                    }
                @endphp
                @if ($hasAccessibleItems)
                <div x-data="{ open: {{ $activeDropdown ? 'true' : 'false' }} }" class="relative">
                    {{-- Dropdown Button --}}
                    <button @click="open = !open"
                        class="flex items-center space-x-3 space-x-reverse px-4 py-3 w-full rounded-xl transition-all duration-300 group relative overflow-hidden
                        {{ $activeDropdown
                            ? 'bg-gradient-to-r from-medical-blue-50 via-medical-green-50 to-medical-blue-50 text-medical-blue-700 shadow-sm border border-medical-blue-200/50'
                            : 'text-medical-gray-700 hover:bg-gradient-to-r hover:from-medical-gray-50 hover:to-medical-gray-50 hover:text-medical-blue-600 hover:shadow-sm' }}">
                        {{-- Active Indicator Bar --}}
                        @if ($activeDropdown)
                            <div
                                class="absolute right-0 top-0 bottom-0 w-1 bg-gradient-to-b from-medical-blue-500 to-medical-green-500 rounded-r-xl">
                            </div>
                        @endif

                        <div class="relative z-10 flex items-center space-x-3 space-x-reverse w-full">
                            <div class="relative">
                                <svg class="w-5 h-5 {{ $activeDropdown ? 'text-medical-blue-600' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }} transition-colors duration-300"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="{{ $item['icon'] }}" />
                                </svg>
                                @if ($activeDropdown)
                                    <div class="absolute inset-0 bg-medical-blue-100 rounded-full blur-sm opacity-50">
                                    </div>
                                @endif
                            </div>
                            <span class="font-semibold text-sm flex-1 text-right">{{ $item['label'] }}</span>
                            <svg :class="{ 'rotate-90': open, 'text-medical-blue-600': open }"
                                class="w-4 h-4 transform transition-all duration-300 text-medical-gray-400 group-hover:text-medical-blue-600"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                        </div>
                    </button>

                    {{-- Dropdown Content --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                        class="overflow-hidden mt-1 space-y-1 pr-2">
                        @foreach ($item['items'] as $sub)
                            @php
                                $subCanAccess = true;
                                if (isset($sub['permission'])) {
                                    $subCanAccess = auth()->user()->can($sub['permission']);
                                } elseif (isset($sub['role'])) {
                                    $subCanAccess = auth()->user()->hasRole($sub['role']);
                                }
                                $isActive = $currentRoute === $sub['route'];
                            @endphp
                            @if ($subCanAccess)
                                <a href="{{ route($sub['route']) }}"
                                    class="flex items-center space-x-3 space-x-reverse px-5 py-2.5 rounded-lg transition-all duration-300 group relative overflow-hidden
                                        {{ $isActive
                                            ? 'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-lg shadow-medical-blue-500/30 transform scale-[1.02]'
                                            : 'text-medical-gray-700 hover:bg-gradient-to-r hover:from-medical-blue-50 hover:to-medical-green-50 hover:text-medical-blue-600 hover:shadow-sm hover:translate-x-[-2px]' }}">
                                    {{-- Active Indicator --}}
                                    @if ($isActive)
                                        <div class="absolute right-0 top-0 bottom-0 w-1 bg-white/50 rounded-r-lg"></div>
                                        <div class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent">
                                        </div>
                                    @endif

                                    <div class="relative z-10 flex items-center space-x-3 space-x-reverse w-full">
                                        <div class="relative">
                                            <svg class="w-4 h-4 {{ $isActive ? 'text-white' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }} transition-colors duration-300"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="{{ $sub['icon'] }}" />
                                            </svg>
                                            @if ($isActive)
                                                <div class="absolute inset-0 bg-white/20 rounded-full blur-sm"></div>
                                            @endif
                                        </div>
                                        <span class="font-medium text-xs flex-1">{{ $sub['label'] }}</span>
                                        @if (isset($sub['badge']) && $sub['badge'] && $pendingCount > 0)
                                            <span
                                                class="inline-flex items-center justify-center min-w-[20px] h-5 px-2 text-xs font-bold rounded-full transition-all duration-300
                                                {{ $isActive
                                                    ? 'bg-white/20 text-white backdrop-blur-sm shadow-sm'
                                                    : 'bg-medical-red-500 text-white shadow-sm group-hover:bg-medical-red-600 group-hover:scale-110' }}">
                                                {{ $pendingCount }}
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
                @endif
            @elseif ($canAccess)
                <a href="{{ route($item['route']) }}"
                    class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-xl transition-all duration-200 group
                        {{ $currentRoute === $item['route']
                            ? 'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-medical'
                            : 'text-medical-gray-700 hover:bg-medical-gray-50 hover:text-medical-blue-600' }}">
                    <svg class="w-5 h-5 {{ $currentRoute === $item['route'] ? 'text-white' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }}"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="font-medium text-sm flex-1">{{ $item['label'] }}</span>
                    @if (isset($item['badge']) && $item['badge'] && $pendingCount > 0)
                        <span
                            class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full
                            {{ $currentRoute === $item['route'] ? 'bg-white text-medical-blue-600' : 'bg-medical-red-500 text-white' }}">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- User Info --}}
    <div class="px-6 py-6">
        <div class="flex items-center space-x-3 space-x-reverse p-3 bg-medical-gray-50 rounded-xl">
            <div
                class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                {{ mb_substr($userName, 0, 1) }}
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-medical-gray-900 truncate">{{ $userName }}</p>
                <p class="text-xs text-medical-gray-600">{{ $userType }}</p>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="mt-3">
            @csrf
            <button type="submit"
                class="w-full flex items-center justify-center space-x-2 space-x-reverse px-4 py-2.5 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 transition-colors duration-200 font-medium text-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>تسجيل الخروج</span>
            </button>
        </form>
    </div>
</aside>

{{-- Mobile Sidebar --}}
<div class="lg:hidden fixed inset-0 z-50" x-show="mobileMenuOpen" x-cloak>
    {{-- Backdrop --}}
    <div class="fixed inset-0 bg-medical-gray-900/50 backdrop-blur-sm" @click="mobileMenuOpen = false"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    {{-- Sidebar Content --}}
    <aside class="fixed top-0 right-0 bottom-0 w-80 max-w-full bg-white shadow-medical-2xl flex flex-col"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-x-full"
        x-transition:enter-end="opacity-100 transform translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-x-0"
        x-transition:leave-end="opacity-0 transform translate-x-full">

        {{-- Mobile Logo & Close Button --}}
        <div class="flex items-center justify-between px-6 py-6">
            <div class="flex items-center space-x-3 space-x-reverse">
                <x-application-logo class="h-12 w-12 shadow-medical" />
                <span
                    class="text-2xl font-bold font-display bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">MediEquip</span>
            </div>
            <button @click="mobileMenuOpen = false"
                class="p-2 text-medical-gray-600 hover:text-medical-gray-900 hover:bg-medical-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Mobile Navigation (تابع نفس التنظيم الجديد) --}}
        <nav class="flex-1 overflow-y-auto px-6 space-y-1">
            @foreach ($items as $item)
                @php
                    $canAccess = true;
                    if (isset($item['permission'])) {
                        $canAccess = auth()->user()->can($item['permission']);
                    } elseif (isset($item['role'])) {
                        $canAccess = auth()->user()->hasRole($item['role']);
                    }
                @endphp

                @if (!empty($item['dropdown']) && $item['dropdown'])
                    @php
                        $activeDropdown = false;
                        $hasAccessibleItems = false;
                        foreach ($item['items'] as $sub) {
                            if ($currentRoute === ($sub['route'] ?? '')) {
                                $activeDropdown = true;
                            }
                            // Check if at least one item is accessible
                            $subCanAccess = true;
                            if (isset($sub['permission'])) {
                                $subCanAccess = auth()->user()->can($sub['permission']);
                            } elseif (isset($sub['role'])) {
                                $subCanAccess = auth()->user()->hasRole($sub['role']);
                            }
                            if ($subCanAccess) {
                                $hasAccessibleItems = true;
                            }
                        }
                    @endphp
                    @if ($hasAccessibleItems)
                    <div x-data="{ open: {{ $activeDropdown ? 'true' : 'false' }} }" class="relative">
                        {{-- Dropdown Button --}}
                        <button @click="open = !open"
                            class="flex items-center space-x-3 space-x-reverse px-4 py-3 w-full rounded-xl transition-all duration-300 group relative overflow-hidden
                            {{ $activeDropdown
                                ? 'bg-gradient-to-r from-medical-blue-50 via-medical-green-50 to-medical-blue-50 text-medical-blue-700 shadow-sm border border-medical-blue-200/50'
                                : 'text-medical-gray-700 hover:bg-gradient-to-r hover:from-medical-gray-50 hover:to-medical-gray-50 hover:text-medical-blue-600 hover:shadow-sm' }}">
                            {{-- Active Indicator Bar --}}
                            @if ($activeDropdown)
                                <div
                                    class="absolute right-0 top-0 bottom-0 w-1 bg-gradient-to-b from-medical-blue-500 to-medical-green-500 rounded-r-xl">
                                </div>
                            @endif

                            <div class="relative z-10 flex items-center space-x-3 space-x-reverse w-full">
                                <div class="relative">
                                    <svg class="w-5 h-5 {{ $activeDropdown ? 'text-medical-blue-600' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }} transition-colors duration-300"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="{{ $item['icon'] }}" />
                                    </svg>
                                    @if ($activeDropdown)
                                        <div
                                            class="absolute inset-0 bg-medical-blue-100 rounded-full blur-sm opacity-50">
                                        </div>
                                    @endif
                                </div>
                                <span class="font-semibold text-sm flex-1 text-right">{{ $item['label'] }}</span>
                                <svg :class="{ 'rotate-90': open, 'text-medical-blue-600': open }"
                                    class="w-4 h-4 transform transition-all duration-300 text-medical-gray-400 group-hover:text-medical-blue-600"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Dropdown Content --}}
                        <div x-show="open" x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 max-h-0" x-transition:enter-end="opacity-100 max-h-96"
                            x-transition:leave="transition ease-in duration-200"
                            x-transition:leave-start="opacity-100 max-h-96" x-transition:leave-end="opacity-0 max-h-0"
                            class="overflow-hidden mt-1 space-y-1 pr-2">
                            @foreach ($item['items'] as $sub)
                                @php
                                    $subCanAccess = true;
                                    if (isset($sub['permission'])) {
                                        $subCanAccess = auth()->user()->can($sub['permission']);
                                    } elseif (isset($sub['role'])) {
                                        $subCanAccess = auth()->user()->hasRole($sub['role']);
                                    }
                                    $isActive = $currentRoute === $sub['route'];
                                @endphp
                                @if ($subCanAccess)
                                    <a href="{{ route($sub['route']) }}" @click="mobileMenuOpen = false"
                                        class="flex items-center space-x-3 space-x-reverse px-5 py-2.5 rounded-lg transition-all duration-300 group relative overflow-hidden
                                            {{ $isActive
                                                ? 'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-lg shadow-medical-blue-500/30 transform scale-[1.02]'
                                                : 'text-medical-gray-700 hover:bg-gradient-to-r hover:from-medical-blue-50 hover:to-medical-green-50 hover:text-medical-blue-600 hover:shadow-sm hover:translate-x-[-2px]' }}">
                                        {{-- Active Indicator --}}
                                        @if ($isActive)
                                            <div class="absolute right-0 top-0 bottom-0 w-1 bg-white/50 rounded-r-lg">
                                            </div>
                                            <div
                                                class="absolute inset-0 bg-gradient-to-r from-white/10 to-transparent">
                                            </div>
                                        @endif

                                        <div class="relative z-10 flex items-center space-x-3 space-x-reverse w-full">
                                            <div class="relative">
                                                <svg class="w-4 h-4 {{ $isActive ? 'text-white' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }} transition-colors duration-300"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2.5" d="{{ $sub['icon'] }}" />
                                                </svg>
                                                @if ($isActive)
                                                    <div class="absolute inset-0 bg-white/20 rounded-full blur-sm">
                                                    </div>
                                                @endif
                                            </div>
                                            <span class="font-medium text-xs flex-1">{{ $sub['label'] }}</span>
                                            @if (isset($sub['badge']) && $sub['badge'] && $pendingCount > 0)
                                                <span
                                                    class="inline-flex items-center justify-center min-w-[20px] h-5 px-2 text-xs font-bold rounded-full transition-all duration-300
                                                    {{ $isActive
                                                        ? 'bg-white/20 text-white backdrop-blur-sm shadow-sm'
                                                        : 'bg-medical-red-500 text-white shadow-sm group-hover:bg-medical-red-600 group-hover:scale-110' }}">
                                                    {{ $pendingCount }}
                                                </span>
                                            @endif
                                        </div>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                @elseif ($canAccess)
                    <a href="{{ route($item['route']) }}" @click="mobileMenuOpen = false"
                        class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-xl transition-all duration-200 group
                            {{ $currentRoute === $item['route']
                                ? 'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-medical'
                                : 'text-medical-gray-700 hover:bg-medical-gray-50 hover:text-medical-blue-600' }}">
                        <svg class="w-5 h-5 {{ $currentRoute === $item['route'] ? 'text-white' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }}"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $item['icon'] }}" />
                        </svg>
                        <span class="font-medium text-sm flex-1">{{ $item['label'] }}</span>
                        @if (isset($item['badge']) && $item['badge'] && $pendingCount > 0)
                            <span
                                class="inline-flex items-center justify-center px-2 py-0.5 text-xs font-bold rounded-full
                                {{ $currentRoute === $item['route'] ? 'bg-white text-medical-blue-600' : 'bg-medical-red-500 text-white' }}">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </a>
                @endif
            @endforeach
        </nav>

        {{-- Mobile User Info --}}
        <div class="px-6 py-6">
            <div class="flex items-center space-x-3 space-x-reverse p-3 bg-medical-gray-50 rounded-xl">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                    {{ mb_substr($userName, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-medical-gray-900 truncate">{{ $userName }}</p>
                    <p class="text-xs text-medical-gray-600">{{ $userType }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center space-x-2 space-x-reverse px-4 py-2.5 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 transition-colors duration-200 font-medium text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>تسجيل الخروج</span>
                </button>
            </form>
        </div>
    </aside>
</div>
