{{-- Admin Products Catalog - Professional View --}}
<x-dashboard.layout title="كتالوج المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">كتالوج المنتجات</h1>
            <p class="mt-2 text-medical-gray-600">عرض ومراجعة جميع المنتجات المضافة من الموردين</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        {{-- Total Products --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات نشطة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $stats['active_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Inactive --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات غير نشطة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-2">{{ $stats['inactive_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الفئات</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">{{ $stats['total_categories'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" id="products-filter-form">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">

                {{-- Enhanced Search --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 ml-1 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            بحث شامل
                        </span>
                    </label>
                    <div class="relative">
                        <input name="search" value="{{ request('search') }}" 
                            placeholder="ابحث في: الاسم، الموديل، SKU، الوصف، الفئة، المورد، المصنّع..."
                            class="w-full px-4 py-2.5 pr-10 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all"
                            onkeypress="if(event.key === 'Enter') { event.preventDefault(); this.form.submit(); }">
                        @if(request('search'))
                            <button type="button" 
                                onclick="document.querySelector('input[name=search]').value=''; document.getElementById('products-filter-form').submit();"
                                class="absolute left-2 top-1/2 transform -translate-y-1/2 text-medical-gray-400 hover:text-medical-red-500 transition"
                                title="مسح البحث">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @else
                            <div class="absolute left-2 top-1/2 transform -translate-y-1/2 text-medical-gray-400 pointer-events-none">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-medical-gray-500">
                        <span class="inline-flex items-center">
                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            يبحث في جميع حقول المنتج والعلاقات (اضغط Enter للبحث)
                        </span>
                    </p>
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">المورد</label>
                    <div class="relative" x-data="{ open: false, search: '' }">
                        <select name="supplier" 
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent appearance-none bg-white cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="">جميع الموردين</option>
                            @foreach ($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">الفئة</label>
                    <div class="relative">
                        <select name="category" 
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent appearance-none bg-white cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="">جميع الفئات</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Manufacturer --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">المُصنّع</label>
                    <div class="relative">
                        <select name="manufacturer" 
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent appearance-none bg-white cursor-pointer"
                            onchange="this.form.submit()">
                            <option value="">جميع المصنّعين</option>
                            @foreach ($manufacturers as $id => $name)
                                <option value="{{ $id }}" {{ request('manufacturer') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">حالة النشاط</label>
                    <select name="status" 
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent appearance-none bg-white cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                {{-- Review Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">حالة المراجعة</label>
                    <select name="review_status" 
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent appearance-none bg-white cursor-pointer"
                        onchange="this.form.submit()">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('review_status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="approved" {{ request('review_status') == 'approved' ? 'selected' : '' }}>معتمد</option>
                        <option value="needs_update" {{ request('review_status') == 'needs_update' ? 'selected' : '' }}>يحتاج تعديل</option>
                        <option value="rejected" {{ request('review_status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>

            </div>

            {{-- Active Filters Display --}}
            @php
                $hasActiveFilters = request()->filled('search') || request()->filled('supplier') || 
                                    request()->filled('category') || request()->filled('manufacturer') || 
                                    request()->filled('status') || request()->filled('review_status');
            @endphp
            @if($hasActiveFilters)
                <div class="mt-4 pt-4 border-t border-medical-gray-200">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-medical-gray-700">الفلاتر النشطة:</span>
                        <a href="{{ route('admin.products.index') }}" 
                            class="text-sm text-medical-red-600 hover:text-medical-red-700 font-medium flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            إزالة جميع الفلاتر
                        </a>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if(request('search'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-blue-100 text-medical-blue-800">
                                بحث: "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                        @if(request('supplier'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-green-100 text-medical-green-800">
                                مورد: {{ $suppliers[request('supplier')] ?? 'N/A' }}
                                <a href="{{ request()->fullUrlWithQuery(['supplier' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                        @if(request('category'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-purple-100 text-medical-purple-800">
                                فئة: {{ $categories[request('category')] ?? 'N/A' }}
                                <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                        @if(request('manufacturer'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-yellow-100 text-medical-yellow-800">
                                مصنّع: {{ $manufacturers[request('manufacturer')] ?? 'N/A' }}
                                <a href="{{ request()->fullUrlWithQuery(['manufacturer' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                        @if(request('status'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-gray-100 text-medical-gray-800">
                                حالة: {{ request('status') == 'active' ? 'نشط' : 'غير نشط' }}
                                <a href="{{ request()->fullUrlWithQuery(['status' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                        @if(request('review_status'))
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-orange-100 text-medical-orange-800">
                                مراجعة: {{ ['pending' => 'قيد المراجعة', 'approved' => 'معتمد', 'needs_update' => 'يحتاج تعديل', 'rejected' => 'مرفوض'][request('review_status')] }}
                                <a href="{{ request()->fullUrlWithQuery(['review_status' => null]) }}" class="mr-2 hover:text-medical-red-600">×</a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif

            <div class="mt-4 flex items-center gap-3 justify-between">
                <div class="text-sm text-medical-gray-600">
                    @if($hasActiveFilters)
                        <span class="font-medium">{{ $products->total() }}</span> منتج مطابق للفلاتر
                    @else
                        إجمالي: <span class="font-medium">{{ $products->total() }}</span> منتج
                    @endif
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('admin.products.index') }}" 
                        class="px-6 py-2.5 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition flex items-center">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        إعادة تعيين
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition flex items-center shadow-sm">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        تطبيق الفلاتر
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المنتج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المصنّع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الفئة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">عدد الموردين</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">حالة المراجعة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y bg-white">

                    @forelse($products as $product)
                        <tr class="hover:bg-medical-gray-50">

                            {{-- Product Name + Image --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div
                                        class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                                        @if ($product->hasMedia('product_images'))
                                            <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                                class="w-full h-full object-cover" alt="{{ $product->name }}">
                                        @else
                                            <svg class="w-6 h-6 text-medical-gray-500" fill="none"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                            </svg>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                        @if ($product->model)
                                            <p class="text-sm text-medical-gray-500">موديل: {{ $product->model }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Manufacturer --}}
                            <td class="px-6 py-4 text-medical-gray-900">
                                @if($product->manufacturer)
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-medical-purple-100 text-medical-purple-800">
                                            {{ $product->manufacturer->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-medical-gray-400 text-sm">غير محدد</span>
                                @endif
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4">
                                @if ($product->category)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-medical-gray-400 text-sm">غير محدد</span>
                                @endif
                            </td>

                            {{-- Supplier Count --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-medical-gray-900 font-medium">{{ $product->suppliers->count() }}</span>
                                    <span class="text-medical-gray-500 text-sm mr-1">مورد</span>
                                </div>
                            </td>

                            {{-- Review Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $badgeColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'needs_update' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span
                                    class="px-3 py-1 text-xs rounded-full {{ $badgeColors[$product->review_status] }}">
                                    {{ [
                                        'pending' => 'قيد المراجعة',
                                        'approved' => 'معتمد',
                                        'needs_update' => 'يحتاج تعديل',
                                        'rejected' => 'مرفوض',
                                    ][$product->review_status] }}
                                </span>
                            </td>

                            {{-- Active Status --}}
                            <td class="px-6 py-4">
                                @if ($product->is_active)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">
                                        نشط
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-red-100 text-medical-red-700 rounded-full">
                                        غير نشط
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">

                                    {{-- Review --}}
                                    @if ($product->review_status === 'pending')
                                        <a href="{{ route('admin.products.review', $product->id) }}"
                                            class="p-2 text-medical-purple-600 hover:bg-medical-purple-50 rounded-lg transition-colors duration-150"
                                            title="مراجعة المنتج">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Show --}}
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-150"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    @can('products.update')
                                        {{-- Edit --}}
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="p-2 text-medical-green-600 hover:bg-medical-green-50 rounded-lg transition-colors duration-150"
                                            title="تعديل المنتج">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                    @endcan

                                    @can('products.delete')
                                        {{-- Delete --}}
                                        @if ($product->review_status !== 'pending')
                                            <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                method="POST" 
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟\nسيتم حذفه من جميع الموردين.');"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-150"
                                                    title="حذف المنتج">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        @endif
                                    @endcan

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-medical-gray-600 text-lg font-semibold">لا توجد منتجات</p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>
