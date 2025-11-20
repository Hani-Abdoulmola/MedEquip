{{-- Dashboard Sidebar Component - Matches Landing Page Design Quality --}}
@props(['userRole' => 'admin', 'userName' => '', 'userType' => 'مستخدم'])

@php
    // Calculate pending registrations count for badge
    $pendingCount = 0;
    if ($userRole === 'admin') {
        $pendingSuppliers = \App\Models\Supplier::where('is_verified', false)->whereNull('rejection_reason')->count();
        $pendingBuyers = \App\Models\Buyer::where('is_verified', false)->whereNull('rejection_reason')->count();
        $pendingCount = $pendingSuppliers + $pendingBuyers;
    }

    $menuItems = [
        'admin' => [
            [
                'route' => 'dashboard',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'label' => 'لوحة التحكم',
            ],
            [
                'route' => 'admin.users',
                'icon' =>
                    'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z',
                'label' => 'المستخدمين',
            ],
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
                'badge' => true, // Show pending count badge
            ],
            [
                'route' => 'admin.products',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'label' => 'المنتجات',
            ],
            [
                'route' => 'admin.orders',
                'icon' =>
                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                'label' => 'الطلبات',
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
                'route' => 'admin.settings',
                'icon' =>
                    'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
                'label' => 'الإعدادات',
            ],
        ],
        'supplier' => [
            [
                'route' => 'dashboard',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'label' => 'لوحة التحكم',
            ],
            [
                'route' => 'supplier.products',
                'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4',
                'label' => 'منتجاتي',
            ],
            [
                'route' => 'supplier.orders',
                'icon' =>
                    'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01',
                'label' => 'الطلبات',
            ],
            ['route' => 'supplier.sales', 'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6', 'label' => 'المبيعات'],
            [
                'route' => 'supplier.profile',
                'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
                'label' => 'الملف الشخصي',
            ],
        ],
        'buyer' => [
            [
                'route' => 'dashboard',
                'icon' =>
                    'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'label' => 'لوحة التحكم',
            ],
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
            <div class="bg-gradient-to-br from-medical-blue-500 to-medical-green-500 p-2.5 rounded-xl shadow-medical">
                <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
            </div>
            <span
                class="text-2xl font-bold font-display bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">MediEquip</span>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 overflow-y-auto px-6 space-y-1">
        @foreach ($items as $item)
            <a href="{{ route($item['route']) }}"
                class="flex items-center space-x-3 space-x-reverse px-4 py-3 rounded-xl transition-all duration-200 group
                    {{ $currentRoute === $item['route']
                        ? 'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-medical'
                        : 'text-medical-gray-700 hover:bg-medical-gray-50 hover:text-medical-blue-600' }}">
                <svg class="w-5 h-5 {{ $currentRoute === $item['route'] ? 'text-white' : 'text-medical-gray-400 group-hover:text-medical-blue-600' }}"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $item['icon'] }}" />
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
                <div
                    class="bg-gradient-to-br from-medical-blue-500 to-medical-green-500 p-2.5 rounded-xl shadow-medical">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                </div>
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

        {{-- Mobile Navigation (Same as Desktop) --}}
        <nav class="flex-1 overflow-y-auto px-6 space-y-1">
            @foreach ($items as $item)
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
            @endforeach
        </nav>

        {{-- Mobile User Info (Same as Desktop) --}}
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
