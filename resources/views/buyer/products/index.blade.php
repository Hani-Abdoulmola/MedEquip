<x-dashboard.layout title="كتالوج المنتجات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6" x-data="productCatalog()">
        {{-- Header --}}
        <div
            class="bg-gradient-to-r from-medical-blue-50 via-white to-medical-green-50 rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div
                        class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-1">كتالوج المنتجات</h1>
                        <p class="text-sm text-gray-600 flex items-center gap-2">
                            <span
                                class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white rounded-full border border-gray-200">
                                <svg class="w-4 h-4 text-medical-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-semibold text-gray-900" x-text="totalProducts"></span>
                                <span class="text-gray-600">منتج متوفر</span>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    {{-- View Toggle --}}
                    <div class="flex items-center bg-white rounded-xl p-1 shadow-sm border border-gray-200">
                        <button @click="view = 'grid'"
                            :class="view === 'grid' ?
                                'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-md' :
                                'text-gray-500 hover:text-gray-700'"
                            class="p-2.5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>
                        </button>
                        <button @click="view = 'list'"
                            :class="view === 'list' ?
                                'bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white shadow-md' :
                                'text-gray-500 hover:text-gray-700'"
                            class="p-2.5 rounded-lg transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>

                    {{-- Compare Button --}}
                    <template x-if="compareProducts.length >= 2">
                        <a :href="'{{ route('buyer.products.compare') }}?products=' + compareProducts.join(',')"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 transition-all font-semibold shadow-md hover:shadow-lg">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <span>مقارنة</span>
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-sm font-bold"
                                x-text="compareProducts.length"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-100 overflow-hidden">
            {{-- Main Search Bar --}}
            <div
                class="p-6 bg-gradient-to-r from-medical-blue-50 via-medical-green-50 to-white border-b-2 border-gray-100">
                <div class="flex items-center gap-3 mb-4">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center shadow-md">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">البحث والفلترة</h3>
                        <p class="text-xs text-gray-600">ابحث عن المنتجات باستخدام الفلاتر المتقدمة</p>
                    </div>
                </div>
                <div class="relative" x-data="{ showAutocomplete: false }">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchProducts"
                            @focus="showAutocomplete = searchQuery.length >= 2"
                            @keydown.escape="showAutocomplete = false"
                            placeholder="ابحث بالاسم، الموديل، SKU، العلامة التجارية..."
                            class="w-full pl-16 pr-12 py-4 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-lg font-medium shadow-sm bg-white">
                        <div class="absolute left-6 top-1/2 -translate-y-1/2">
                            <svg x-show="!isLoading" class="w-6 h-6 text-medical-blue-500" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <svg x-show="isLoading" class="w-6 h-6 text-medical-blue-500 animate-spin" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>
                        @if (request('search'))
                            <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                class="absolute right-4 top-1/2 -translate-y-1/2 p-2 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5 text-gray-400 hover:text-red-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </a>
                        @endif
                    </div>

                    {{-- Autocomplete Dropdown --}}
                    <div x-show="showAutocomplete && autocompleteResults.length > 0"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 transform scale-95"
                        x-transition:enter-end="opacity-100 transform scale-100"
                        @click.away="showAutocomplete = false"
                        class="absolute z-50 w-full mt-3 bg-white rounded-xl shadow-2xl border-2 border-gray-200 max-h-80 overflow-y-auto">
                        <template x-for="item in autocompleteResults" :key="item.id">
                            <a :href="'/buyer/products/' + item.id"
                                class="flex items-center gap-4 p-4 hover:bg-gradient-to-r hover:from-medical-blue-50 hover:to-medical-green-50 border-b border-gray-100 last:border-0 transition-colors">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-green-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-gray-900 truncate text-base" x-text="item.name"></p>
                                    <p class="text-sm text-gray-600 mt-0.5">
                                        <span x-text="item.brand || ''"></span>
                                        <span x-show="item.sku"
                                            class="text-xs bg-gray-100 px-2 py-0.5 rounded-full mr-2 font-mono"
                                            x-text="item.sku"></span>
                                    </p>
                                </div>
                                <span x-show="item.category"
                                    class="text-xs text-medical-blue-700 bg-medical-blue-100 px-3 py-1.5 rounded-full font-semibold border border-medical-blue-200"
                                    x-text="item.category"></span>
                            </a>
                        </template>
                    </div>
                </div>
            </div>

            {{-- Filter Bar --}}
            <div class="p-6 bg-gradient-to-br from-gray-50 to-white" x-data="{ showFilters: true }">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-medical-blue-100 to-medical-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">الفلاتر المتقدمة</h4>
                            <p class="text-xs text-gray-600">قم بتصفية المنتجات حسب معايير محددة</p>
                        </div>
                    </div><br>
                    <button @click="showFilters = !showFilters" type="button"
                        class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg x-show="showFilters" class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                            </path>
                        </svg>
                        <svg x-show="!showFilters" class="w-5 h-5 text-gray-600" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>
                </div>

                <form method="GET" action="{{ route('buyer.products.index') }}" id="filter-form" class="space-y-5"
                    x-show="showFilters" x-transition x-data="categoryFilter()">
                    {{-- Hidden search field --}}
                    <input type="hidden" name="search" :value="searchQuery">

                    {{-- First Row: Categories --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Parent Category Filter --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-medical-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                    </path>
                                </svg>
                                الفئة الرئيسية
                            </label>
                            <select name="parent_category" x-model="selectedParentCategory"
                                @change="onParentCategoryChange()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 bg-white shadow-sm transition-all hover:border-medical-blue-300">
                                <option value="">جميع الفئات</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ request('parent_category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Subcategory Filter --}}
                        <div class="space-y-2" x-show="selectedParentCategory" x-transition>
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-medical-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                الفئة الفرعية
                            </label>
                            <select name="category" @change="$el.form.submit()" x-model="selectedSubcategory"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-medical-green-500 focus:border-medical-green-500 bg-white shadow-sm transition-all hover:border-medical-green-300">
                                <option value="">جميع الفئات الفرعية</option>
                                <template x-for="child in getChildrenForParent(selectedParentCategory)"
                                    :key="child.id">
                                    <option :value="child.id" x-text="child.name"></option>
                                </template>
                            </select>
                        </div>
                    </div><br>

                    {{-- Second Row: Manufacturer, Price, Sort --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Manufacturer Filter --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                                الشركة المصنعة
                            </label>
                            <select name="manufacturer" @change="$el.form.submit()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-purple-500 focus:border-purple-500 bg-white shadow-sm transition-all hover:border-purple-300">
                                <option value="">جميع المصنعين</option>
                                @foreach ($manufacturers as $manufacturer)
                                    <option value="{{ $manufacturer->id }}"
                                        {{ request('manufacturer') == $manufacturer->id ? 'selected' : '' }}>
                                        {{ $manufacturer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Price Range --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                نطاق السعر (د.ل)
                            </label>
                            <div class="flex gap-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    placeholder="من" step="0.01" @change.debounce.500ms="$el.form.submit()"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm transition-all hover:border-green-300">
                                <span class="flex items-center text-gray-400 font-bold">-</span>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    placeholder="إلى" step="0.01" @change.debounce.500ms="$el.form.submit()"
                                    class="flex-1 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white shadow-sm transition-all hover:border-green-300">
                            </div>
                        </div>

                        {{-- Sort --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                </svg>
                                الترتيب
                            </label>
                            <select name="sort" @change="$el.form.submit()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-orange-500 focus:border-orange-500 bg-white shadow-sm transition-all hover:border-orange-300">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الاسم (أ-ي)
                                </option>
                                <option value="created_at"
                                    {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>الأحدث أولاً
                                </option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>
                                    السعر:
                                    منخفض إلى عالي</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>
                                    السعر:
                                    عالي إلى منخفض</option>
                                <option value="suppliers" {{ request('sort') == 'suppliers' ? 'selected' : '' }}>
                                    الأكثر
                                    موردين</option>
                            </select>
                        </div>
                    </div>

                    {{-- Third Row: Stock Status, Lead Time --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Stock Status Filter --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                    </path>
                                </svg>
                                حالة المخزون
                            </label>
                            <select name="stock_status" @change="$el.form.submit()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white shadow-sm transition-all hover:border-blue-300">
                                <option value="">الكل</option>
                                <option value="in_stock"
                                    {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                                    متوفر في المخزون</option>
                                <option value="low_stock"
                                    {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                                    مخزون منخفض (1-10)</option>
                                <option value="out_of_stock"
                                    {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                                    غير متوفر</option>
                            </select>
                        </div>

                        {{-- Lead Time Filter --}}
                        <div class="space-y-2">
                            <label class="flex items-center gap-2 text-sm font-bold text-gray-700">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                وقت التسليم
                            </label>
                            <select name="lead_time" @change="$el.form.submit()"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white shadow-sm transition-all hover:border-indigo-300">
                                <option value="">الكل</option>
                                <option value="fast" {{ request('lead_time') == 'fast' ? 'selected' : '' }}>
                                    سريع (1-7 أيام)</option>
                                <option value="medium" {{ request('lead_time') == 'medium' ? 'selected' : '' }}>
                                    متوسط (8-14 يوم)</option>
                                <option value="standard" {{ request('lead_time') == 'standard' ? 'selected' : '' }}>
                                    قياسي (15-30 يوم)</option>
                                <option value="extended" {{ request('lead_time') == 'extended' ? 'selected' : '' }}>
                                    ممتد (أكثر من 30 يوم)</option>
                            </select>
                        </div>
                    </div>

                    {{-- Actions Row --}}
                    <div class="flex items-center justify-between pt-4 border-t-2 border-gray-200">
                        <div class="flex items-center gap-2">
                            @php
                                $activeFiltersCount = 0;
                                if (request('search')) {
                                    $activeFiltersCount++;
                                }
                                if (request('parent_category')) {
                                    $activeFiltersCount++;
                                }
                                if (request('category')) {
                                    $activeFiltersCount++;
                                }
                                if (request('manufacturer')) {
                                    $activeFiltersCount++;
                                }
                                if (request('min_price') || request('max_price')) {
                                    $activeFiltersCount++;
                                }
                                if (request('stock_status')) {
                                    $activeFiltersCount++;
                                }
                                if (request('lead_time')) {
                                    $activeFiltersCount++;
                                }
                            @endphp
                            @if ($activeFiltersCount > 0)
                                <span
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-gradient-to-r from-medical-blue-100 to-medical-green-100 rounded-full text-sm font-bold text-gray-700 border border-medical-blue-200">
                                    <svg class="w-4 h-4 text-medical-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z">
                                        </path>
                                    </svg>
                                    <span>{{ $activeFiltersCount }}</span>
                                    <span>فلتر نشط</span>
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            @if (request()->anyFilled([
                                    'search',
                                    'parent_category',
                                    'category',
                                    'manufacturer',
                                    'min_price',
                                    'max_price',
                                    'stock_status',
                                    'lead_time',
                                ]))
                                <a href="{{ route('buyer.products.index') }}"
                                    class="px-6 py-3 text-gray-700 hover:text-white hover:bg-gradient-to-r hover:from-red-500 hover:to-red-600 rounded-xl font-bold text-sm flex items-center gap-2 transition-all border-2 border-gray-200 hover:border-red-500 shadow-sm hover:shadow-md">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    مسح جميع الفلاتر
                                </a>
                            @endif
                            <button type="submit"
                                class="px-6 py-3 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 font-bold text-sm flex items-center gap-2 transition-all shadow-md hover:shadow-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                تطبيق الفلاتر
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Active Filters Tags --}}
            @if (request()->anyFilled(['search', 'parent_category', 'category', 'manufacturer', 'min_price', 'max_price']))
                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-medical-blue-50 border-t-2 border-gray-100">
                    <div class="flex items-center gap-2 mb-3">
                        <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        <span class="text-sm font-bold text-gray-700">الفلاتر النشطة:</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        @if (request('search'))
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-gray-100 to-gray-50 text-gray-800 rounded-full text-sm font-bold border-2 border-gray-200 shadow-sm">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                "{{ request('search') }}"
                                <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                    class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if (request('parent_category'))
                            @php $parentCat = $categories->firstWhere('id', request('parent_category')); @endphp
                            @if ($parentCat)
                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-medical-blue-100 to-medical-blue-50 text-medical-blue-800 rounded-full text-sm font-bold border-2 border-medical-blue-300 shadow-sm">
                                    <svg class="w-4 h-4 text-medical-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z">
                                        </path>
                                    </svg>
                                    {{ $parentCat->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['parent_category' => null, 'category' => null]) }}"
                                        class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif
                        @endif
                        @if (request('category'))
                            @php
                                $subCat = null;
                                foreach ($categories as $cat) {
                                    $found = $cat->children->firstWhere('id', request('category'));
                                    if ($found) {
                                        $subCat = $found;
                                        break;
                                    }
                                }
                            @endphp
                            @if ($subCat)
                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-medical-green-100 to-medical-green-50 text-medical-green-800 rounded-full text-sm font-bold border-2 border-medical-green-300 shadow-sm">
                                    <svg class="w-4 h-4 text-medical-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                    {{ $subCat->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}"
                                        class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif
                        @endif
                        @if (request('manufacturer'))
                            @php $mfr = $manufacturers->firstWhere('id', request('manufacturer')); @endphp
                            @if ($mfr)
                                <span
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-100 to-purple-50 text-purple-800 rounded-full text-sm font-bold border-2 border-purple-300 shadow-sm">
                                    {{ $mfr->name }}
                                    <a href="{{ request()->fullUrlWithQuery(['manufacturer' => null]) }}"
                                        class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                                </span>
                            @endif
                        @endif
                        @if (request('min_price') || request('max_price'))
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-green-100 to-green-50 text-green-800 rounded-full text-sm font-bold border-2 border-green-300 shadow-sm">
                                السعر:
                                @if (request('min_price'))
                                    من {{ number_format(request('min_price'), 0) }}
                                @endif
                                @if (request('min_price') && request('max_price'))
                                    -
                                @endif
                                @if (request('max_price'))
                                    إلى {{ number_format(request('max_price'), 0) }}
                                @endif
                                د.ل
                                <a href="{{ request()->fullUrlWithQuery(['min_price' => null, 'max_price' => null]) }}"
                                    class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if (request('stock_status'))
                            @php
                                $stockLabels = [
                                    'in_stock' => 'متوفر في المخزون',
                                    'low_stock' => 'مخزون منخفض',
                                    'out_of_stock' => 'غير متوفر',
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-100 to-blue-50 text-blue-800 rounded-full text-sm font-bold border-2 border-blue-300 shadow-sm">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                    </path>
                                </svg>
                                {{ $stockLabels[request('stock_status')] ?? request('stock_status') }}
                                <a href="{{ request()->fullUrlWithQuery(['stock_status' => null]) }}"
                                    class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                        @if (request('lead_time'))
                            @php
                                $leadTimeLabels = [
                                    'fast' => 'سريع (1-7 أيام)',
                                    'medium' => 'متوسط (8-14 يوم)',
                                    'standard' => 'قياسي (15-30 يوم)',
                                    'extended' => 'ممتد (أكثر من 30 يوم)',
                                ];
                            @endphp
                            <span
                                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-indigo-100 to-indigo-50 text-indigo-800 rounded-full text-sm font-bold border-2 border-indigo-300 shadow-sm">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                                {{ $leadTimeLabels[request('lead_time')] ?? request('lead_time') }}
                                <a href="{{ request()->fullUrlWithQuery(['lead_time' => null]) }}"
                                    class="hover:text-red-600 font-bold text-lg leading-none">×</a>
                            </span>
                        @endif
                    </div>
                </div>
            @endif
        </div><br>

        {{-- Products Grid/List --}}
        @if ($products->count() > 0)
            {{-- Grid View --}}
            <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div
                        class="bg-white rounded-2xl shadow-md border-2 border-gray-100 overflow-hidden hover:shadow-2xl hover:border-medical-blue-300 transition-all duration-300 group">
                        {{-- Image --}}
                        <div class="relative aspect-[4/3] bg-gradient-to-br from-gray-50 to-gray-100 overflow-hidden">
                            @if ($product->hasMedia('product_images'))
                                @php
                                    $firstMedia = $product->getFirstMedia('product_images');
                                    $imageUrl = $firstMedia
                                        ? ($firstMedia->getUrl('preview') ?:
                                        $firstMedia->getUrl())
                                        : null;
                                @endphp
                                @if ($imageUrl)
                                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-20 h-20" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-20 h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @endif

                            {{-- Favorite Button --}}
                            <button @click.prevent="$refs.favoriteForm{{ $product->id }}.submit()"
                                class="absolute top-3 left-3 p-2.5 bg-white/95 backdrop-blur-sm rounded-xl shadow-lg hover:bg-red-50 hover:scale-110 transition-all duration-200 z-10">
                                @if (in_array($product->id, $favoriteIds))
                                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 text-gray-400 group-hover:text-red-500" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                @endif
                            </button>
                            <form x-ref="favoriteForm{{ $product->id }}"
                                action="{{ route('buyer.products.favorite', $product) }}" method="POST"
                                class="hidden">@csrf</form>

                            {{-- Compare Checkbox --}}
                            <label
                                class="absolute bottom-3 right-3 flex items-center gap-2 bg-white/95 backdrop-blur-sm rounded-xl px-3 py-1.5 shadow-lg cursor-pointer hover:bg-medical-blue-50 hover:scale-105 transition-all duration-200 z-10">
                                <input type="checkbox" :value="{{ $product->id }}" x-model="compareProducts"
                                    :disabled="!compareProducts.includes({{ $product->id }}) && compareProducts.length >= 4"
                                    class="w-4 h-4 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500">
                                <span class="text-xs font-semibold text-gray-700">قارن</span>
                            </label>
                        </div>

                        {{-- Content --}}
                        <div class="p-6">
                            {{-- Category & SKU --}}
                            <div class="flex items-center justify-between mb-4">
                                @if ($product->category)
                                    <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}"
                                        class="text-xs text-medical-blue-700 font-bold hover:text-white hover:bg-medical-blue-600 bg-medical-blue-50 px-3 py-1.5 rounded-full border border-medical-blue-200 transition-all">
                                        {{ $product->category->name }}
                                    </a>
                                @endif
                                @if ($product->sku)
                                    <span
                                        class="text-[10px] text-gray-600 font-mono bg-gray-100 px-2.5 py-1 rounded-lg border border-gray-200">{{ $product->sku }}</span>
                                @endif
                            </div>

                            {{-- Name --}}
                            <h3
                                class="font-bold text-gray-900 mb-3 line-clamp-2 min-h-[3.5rem] group-hover:text-medical-blue-600 transition-colors text-lg">
                                <a href="{{ route('buyer.products.show', $product) }}"
                                    class="hover:underline">{{ $product->name }}</a>
                            </h3>

                            {{-- Brand & Model --}}
                            @if ($product->brand || $product->model)
                                <div class="flex items-center gap-2 mb-4 text-sm text-gray-600">
                                    @if ($product->brand)
                                        <span class="font-semibold text-gray-700">{{ $product->brand }}</span>
                                    @endif
                                    @if ($product->model)
                                        <span class="text-gray-400">•</span>
                                        <span>{{ $product->model }}</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Availability badge (Phase 2) --}}
                            @php
                                $availability = $product->getAvailabilityStatus();
                                $sc = $product->suppliers_count ?? 0;
                            @endphp
                            <div class="flex items-center gap-2 mb-3">
                                <span
                                    class="text-xs font-bold px-3 py-1 rounded-full border {{ $availability['color'] }}">
                                    {{ $availability['badge'] }}
                                </span>
                                @if ($sc > 0)
                                    <span class="text-xs text-gray-500">{{ $sc }}
                                        {{ $sc == 1 ? 'مورد' : 'موردين' }}</span>
                                @endif
                            </div>

                            {{-- Price (min_price from denormalized column) --}}
                            @if (($product->min_price ?? null) && $sc > 0)
                                <div
                                    class="mb-5 p-4 bg-gradient-to-br from-medical-blue-50 via-medical-green-50 to-white rounded-xl border-2 border-medical-blue-200 shadow-sm">
                                    <div class="text-xs text-gray-600 mb-2 font-semibold">من</div>
                                    <p
                                        class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-medical-blue-600 to-medical-green-600">
                                        {{ number_format($product->min_price, 0) }} <span
                                            class="text-sm font-normal text-gray-600">د.ل</span>
                                    </p>
                                    @if ($product->suppliers->count() > 1)
                                        @php
                                            $maxPrice = $product->suppliers->pluck('pivot.price')->filter()->max();
                                        @endphp
                                        @if ($maxPrice && $maxPrice != $product->min_price)
                                            <p class="text-xs text-gray-500 mt-1">حتى
                                                {{ number_format($maxPrice, 0) }} د.ل</p>
                                        @endif
                                    @endif
                                </div>
                            @elseif ($sc > 0)
                                <div class="mb-5 p-4 bg-gray-50 rounded-xl border-2 border-gray-200">
                                    <span class="text-sm text-gray-600 font-semibold">اتصل بالسعر</span>
                                </div>
                            @else
                                <div class="mb-5 p-4 bg-gray-50 rounded-xl border-2 border-gray-200 text-center">
                                    <span class="text-xs text-gray-600 font-semibold">لا يوجد موردين متاحين
                                        حالياً</span>
                                </div>
                            @endif

                            {{-- Actions --}}
                            <div class="flex gap-3">
                                <a href="{{ route('buyer.products.show', $product) }}"
                                    class="flex-1 text-center px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all text-sm font-bold border-2 border-gray-200 shadow-sm">
                                    التفاصيل
                                </a>
                                @if ($sc > 0)
                                    <form action="{{ route('buyer.cart.add', $product) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="w-full px-4 py-3 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 transition-all text-sm font-bold shadow-md hover:shadow-lg flex items-center justify-center gap-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            أضف إلى السلة
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- List View --}}
            <div x-show="view === 'list'"
                class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 overflow-hidden">
                <div class="divide-y divide-gray-200">
                    @foreach ($products as $product)
                        <div
                            class="p-6 hover:bg-gradient-to-r hover:from-gray-50 hover:to-medical-blue-50 transition-all duration-200">
                            <div class="flex gap-6">
                                {{-- Image --}}
                                <div
                                    class="w-40 h-32 bg-gradient-to-br from-gray-100 to-gray-50 rounded-xl overflow-hidden flex-shrink-0 relative border-2 border-gray-200 shadow-sm">
                                    @if ($product->hasMedia('product_images'))
                                        @php
                                            $firstMedia = $product->getFirstMedia('product_images');
                                            $imageUrl = $firstMedia
                                                ? ($firstMedia->getUrl('thumb') ?:
                                                $firstMedia->getUrl())
                                                : null;
                                        @endphp
                                        @if ($imageUrl)
                                            <img src="{{ $imageUrl }}" alt="{{ $product->name }}"
                                                class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between gap-4">
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center gap-3 mb-2">
                                                @if ($product->category)
                                                    <span
                                                        class="text-xs text-medical-blue-700 font-bold bg-medical-blue-50 px-3 py-1 rounded-full border border-medical-blue-200">{{ $product->category->name }}</span>
                                                @endif
                                                @if ($product->sku)
                                                    <span
                                                        class="text-[10px] text-gray-600 font-mono bg-gray-100 px-2 py-1 rounded-lg border border-gray-200">{{ $product->sku }}</span>
                                                @endif
                                            </div>
                                            <h3
                                                class="font-bold text-gray-900 text-lg mb-2 hover:text-medical-blue-600 transition-colors">
                                                <a href="{{ route('buyer.products.show', $product) }}"
                                                    class="hover:underline">{{ $product->name }}</a>
                                            </h3>
                                            @if ($product->brand || $product->model)
                                                <p class="text-sm text-gray-600 mb-3">
                                                    <span
                                                        class="font-semibold">{{ $product->brand }}</span>{{ $product->brand && $product->model ? ' • ' : '' }}{{ $product->model }}
                                                </p>
                                            @endif

                                            {{-- Availability & Price (Phase 2) --}}
                                            @php
                                                $availability = $product->getAvailabilityStatus();
                                                $sc = $product->suppliers_count ?? 0;
                                            @endphp
                                            <div class="flex items-center gap-3 mb-3">
                                                <span
                                                    class="text-xs font-bold px-3 py-1 rounded-full border {{ $availability['color'] }}">
                                                    {{ $availability['badge'] }}
                                                </span>
                                                @if ($sc > 0)
                                                    <span class="text-xs text-gray-500">{{ $sc }}
                                                        {{ $sc == 1 ? 'مورد' : 'موردين' }}</span>
                                                @endif
                                            </div>
                                            @if (($product->min_price ?? null) && $sc > 0)
                                                <div class="flex items-center gap-4 mb-4">
                                                    <div class="flex items-baseline gap-2">
                                                        <span
                                                            class="text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-medical-blue-600 to-medical-green-600">
                                                            {{ number_format($product->min_price, 0) }}
                                                        </span>
                                                        <span class="text-sm text-gray-600 font-semibold">د.ل</span>
                                                    </div>
                                                </div>
                                            @elseif ($sc > 0)
                                                <div class="mb-4">
                                                    <span class="text-sm text-gray-600 font-semibold">اتصل
                                                        بالسعر</span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="flex items-center gap-3 flex-shrink-0">
                                            <label
                                                class="flex items-center gap-2 cursor-pointer px-3 py-2 bg-gray-50 rounded-xl border border-gray-200 hover:bg-medical-blue-50 hover:border-medical-blue-300 transition-all">
                                                <input type="checkbox" :value="{{ $product->id }}"
                                                    x-model="compareProducts"
                                                    :disabled="!compareProducts.includes({{ $product->id }}) && compareProducts
                                                        .length >= 4"
                                                    class="w-4 h-4 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500">
                                                <span class="text-xs text-gray-700 font-semibold">قارن</span>
                                            </label>
                                            <button
                                                @click.prevent="$refs.favoriteFormList{{ $product->id }}.submit()"
                                                class="p-2.5 hover:bg-red-50 rounded-xl border border-gray-200 hover:border-red-300 transition-all">
                                                @if (in_array($product->id, $favoriteIds))
                                                    <svg class="w-5 h-5 text-red-500" fill="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path
                                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                                    </svg>
                                                @else
                                                    <svg class="w-5 h-5 text-gray-400 hover:text-red-500"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                                        </path>
                                                    </svg>
                                                @endif
                                            </button>
                                            <form x-ref="favoriteFormList{{ $product->id }}"
                                                action="{{ route('buyer.products.favorite', $product) }}"
                                                method="POST" class="hidden">@csrf</form>
                                        </div>
                                    </div>

                                    <div class="mt-4 flex items-center gap-3">
                                        <a href="{{ route('buyer.products.show', $product) }}"
                                            class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 text-sm font-bold border-2 border-gray-200 transition-all">
                                            التفاصيل
                                        </a>
                                        @php
                                            $sc = $product->suppliers_count ?? 0;
                                        @endphp
                                        @if ($sc > 0)
                                            <form action="{{ route('buyer.cart.add', $product) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit"
                                                    class="px-5 py-2.5 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 text-sm font-bold shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                        </path>
                                                    </svg>
                                                    أضف إلى السلة
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-8 flex justify-center">
                <div class="bg-white rounded-2xl shadow-lg border-2 border-gray-100 p-2">
                    {{ $products->links() }}
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-2xl shadow-xl border-2 border-gray-200 p-20 text-center">
                <div
                    class="w-32 h-32 bg-gradient-to-br from-medical-blue-100 via-medical-green-100 to-medical-blue-100 rounded-full flex items-center justify-center mx-auto mb-8 shadow-lg">
                    <svg class="w-16 h-16 text-medical-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div><br>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">لا توجد منتجات</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto text-lg">لم يتم العثور على منتجات تطابق معايير البحث
                    الخاصة بك</p>
                @if (request()->anyFilled(['search', 'parent_category', 'category', 'manufacturer', 'min_price', 'max_price']))
                    <a href="{{ route('buyer.products.index') }}"
                        class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 font-bold shadow-lg hover:shadow-xl transition-all text-base">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                        إعادة تعيين الفلاتر
                    </a>
                @endif
            </div>
        @endif
    </div>

    {{-- Alpine.js Component --}}
    <script>
        function productCatalog() {
            return {
                view: 'grid',
                compareProducts: [],
                searchQuery: '{{ request('search') }}',
                isLoading: false,
                autocompleteResults: [],
                totalProducts: {{ $products->total() }},

                async searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.autocompleteResults = [];
                        return;
                    }

                    this.isLoading = true;

                    try {
                        const response = await fetch(
                            `/api/products/autocomplete?q=${encodeURIComponent(this.searchQuery)}`);
                        if (response.ok) {
                            this.autocompleteResults = await response.json();
                        }
                    } catch (error) {
                        console.error('Search error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }

        function categoryFilter() {
            return {
                selectedParentCategory: '{{ request('parent_category') ?? '' }}',
                selectedSubcategory: '{{ request('category') ?? '' }}',
                availableChildren: @json(
                    $categories->mapWithKeys(function ($category) {
                            return [
                                $category->id => $category->children->map(function ($child) {
                                    return ['id' => $child->id, 'name' => $child->name];
                                }),
                            ];
                        })->toArray()),

                onParentCategoryChange() {
                    // Reset subcategory when parent changes
                    this.selectedSubcategory = '';

                    // If no parent selected, submit form to clear filters
                    if (!this.selectedParentCategory) {
                        const form = document.querySelector('form[method="GET"]');
                        if (form) {
                            // Remove category from URL if parent is cleared
                            const url = new URL(window.location.href);
                            url.searchParams.delete('category');
                            url.searchParams.delete('parent_category');
                            window.location.href = url.toString();
                        }
                    } else {
                        // Auto-submit when parent category is selected to show subcategories
                        const form = document.querySelector('form[method="GET"]');
                        if (form) {
                            // Update hidden search field
                            const searchInput = form.querySelector('input[name="search"]');
                            if (searchInput) {
                                searchInput.value = document.querySelector('[x-data*="productCatalog"]')?.__x?.$data
                                    ?.searchQuery || '';
                            }
                            form.submit();
                        }
                    }
                },

                getChildrenForParent(parentId) {
                    if (!parentId) return [];
                    return this.availableChildren[parentId] || [];
                }
            }
        }
    </script>
</x-dashboard.layout>
