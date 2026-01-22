{{-- Admin Factory Data Diagnostics --}}
<x-dashboard.layout title="تشخيص بيانات المصنع" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تشخيص بيانات المصنع</h1>
                <p class="mt-2 text-medical-gray-600">عرض حالة البيانات المُنشأة بواسطة Factories</p>
            </div>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للوحة التحكم
            </a>
        </div>

        {{-- Statistics Overview --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">الفئات</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['categories']['total'] }}</p>
                        <p class="text-xs text-medical-gray-500 mt-1">{{ $stats['categories']['active'] }} نشطة</p>
                    </div>
                    <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 11h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 19h5c1.1046 0 2 .8954 2 2v0"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">الشركات المصنعة</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['manufacturers']['total'] }}</p>
                        <p class="text-xs text-medical-gray-500 mt-1">{{ $stats['manufacturers']['active'] }} نشطة</p>
                    </div>
                    <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">الموردين</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['suppliers']['total'] }}</p>
                        <p class="text-xs text-medical-gray-500 mt-1">{{ $stats['suppliers']['verified'] }} معتمد</p>
                    </div>
                    <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">المنتجات</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['products']['total'] }}</p>
                        <p class="text-xs text-medical-gray-500 mt-1">{{ $stats['products']['active'] }} نشطة</p>
                    </div>
                    <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detailed Statistics --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            {{-- Products Details --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4">تفاصيل المنتجات</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">إجمالي المنتجات</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['products']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">منتجات نشطة</span>
                        <span class="font-bold text-medical-green-600">{{ $stats['products']['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">منتجات معتمدة</span>
                        <span class="font-bold text-medical-green-600">{{ $stats['products']['approved'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">مع فئة</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['products']['with_category'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">مع شركة مصنعة</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['products']['with_manufacturer'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">مع موردين</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['products']['with_suppliers'] }}</span>
                    </div>
                </div>
                @if($stats['products']['total'] > 0)
                    <div class="mt-4 pt-4 border-t border-medical-gray-200">
                        <a href="{{ route('admin.products.index') }}"
                            class="inline-flex items-center text-medical-blue-600 hover:text-medical-blue-700 font-semibold">
                            عرض جميع المنتجات
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Suppliers Details --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4">تفاصيل الموردين</h2>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">إجمالي الموردين</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['suppliers']['total'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">موردين معتمدين</span>
                        <span class="font-bold text-medical-green-600">{{ $stats['suppliers']['verified'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">موردين نشطين</span>
                        <span class="font-bold text-medical-green-600">{{ $stats['suppliers']['active'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">مع حسابات مستخدمين</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['suppliers']['with_users'] }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">مع منتجات</span>
                        <span class="font-bold text-medical-gray-900">{{ $stats['suppliers']['with_products'] }}</span>
                    </div>
                </div>
                @if($stats['suppliers']['total'] > 0)
                    <div class="mt-4 pt-4 border-t border-medical-gray-200">
                        <a href="{{ route('admin.suppliers') }}"
                            class="inline-flex items-center text-medical-blue-600 hover:text-medical-blue-700 font-semibold">
                            عرض جميع الموردين
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Relationships --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
            <h2 class="text-xl font-bold text-medical-gray-900 mb-4">العلاقات</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex justify-between items-center p-4 bg-medical-gray-50 rounded-xl">
                    <span class="text-medical-gray-700 font-semibold">إجمالي عروض المنتجات</span>
                    <span class="text-2xl font-bold text-medical-gray-900">{{ $stats['relationships']['total_offers'] }}</span>
                </div>
                <div class="flex justify-between items-center p-4 bg-medical-green-50 rounded-xl">
                    <span class="text-medical-gray-700 font-semibold">عروض متاحة</span>
                    <span class="text-2xl font-bold text-medical-green-600">{{ $stats['relationships']['available_offers'] }}</span>
                </div>
            </div>
        </div>

        {{-- Sample Data --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Sample Suppliers --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4">عينة من الموردين</h2>
                @if($samples['suppliers']->count() > 0)
                    <div class="space-y-3">
                        @foreach($samples['suppliers'] as $supplier)
                            <div class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $supplier->company_name }}</p>
                                    <p class="text-sm text-medical-gray-600">{{ $supplier->city }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($supplier->is_verified)
                                        <span class="px-2 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">معتمد</span>
                                    @endif
                                    @if($supplier->is_active)
                                        <span class="px-2 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">نشط</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-medical-gray-500 text-center py-8">لا توجد موردين</p>
                @endif
            </div>

            {{-- Sample Products --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4">عينة من المنتجات</h2>
                @if($samples['products']->count() > 0)
                    <div class="space-y-3">
                        @foreach($samples['products'] as $product)
                            <div class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-medical-gray-600">
                                        @if($product->category)
                                            {{ $product->category->name }}
                                        @else
                                            بدون فئة
                                        @endif
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($product->is_active)
                                        <span class="px-2 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">نشط</span>
                                    @endif
                                    @if($product->review_status === 'approved')
                                        <span class="px-2 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">معتمد</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-medical-gray-500 text-center py-8">لا توجد منتجات</p>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-6 bg-gradient-to-r from-medical-blue-50 to-medical-green-50 rounded-2xl p-6 border-2 border-medical-blue-200">
            <h3 class="text-lg font-bold text-medical-gray-900 mb-4">إجراءات سريعة</h3>
            <div class="flex flex-wrap gap-3">
                @if($stats['products']['total'] == 0 || $stats['suppliers']['total'] == 0)
                    <div class="flex-1 min-w-[200px]">
                        <p class="text-sm text-medical-gray-700 mb-2">إنشاء بيانات تجريبية:</p>
                        <code class="block px-4 py-2 bg-white rounded-lg text-sm font-mono text-medical-gray-900">
                            php artisan db:seed --class=ProductCatalogSeeder
                        </code>
                    </div>
                @endif
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition font-semibold">
                    العودة للوحة التحكم
                </a>
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition font-semibold">
                    عرض المنتجات
                </a>
            </div>
        </div>
    </div>

</x-dashboard.layout>
