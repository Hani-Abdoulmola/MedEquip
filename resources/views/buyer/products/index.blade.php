<x-dashboard.layout title="كتالوج المنتجات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6" x-data="productCatalog()">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">كتالوج المنتجات</h1>
            <p class="mt-1 text-sm text-gray-500">
                <span x-text="totalProducts"></span> منتج متوفر
            </p>
        </div>
        <div class="flex items-center gap-3">
            {{-- View Toggle --}}
            <div class="flex items-center bg-gray-100 rounded-lg p-1">
                <button @click="view = 'grid'" 
                        :class="view === 'grid' ? 'bg-white shadow-sm' : 'text-gray-500'"
                        class="p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
                <button @click="view = 'list'" 
                        :class="view === 'list' ? 'bg-white shadow-sm' : 'text-gray-500'"
                        class="p-2 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
            
            {{-- Compare Button --}}
            <template x-if="compareProducts.length >= 2">
                <a :href="'{{ route('buyer.products.compare') }}?products=' + compareProducts.join(',')" 
                   class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    مقارنة (<span x-text="compareProducts.length"></span>)
                </a>
            </template>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        {{-- Main Search Bar --}}
        <div class="p-4 bg-gradient-to-l from-medical-blue-50 to-white">
            <div class="relative" x-data="{ showAutocomplete: false }">
                <div class="relative">
                    <input type="text" 
                           x-model="searchQuery"
                           @input.debounce.300ms="searchProducts"
                           @focus="showAutocomplete = searchQuery.length >= 2"
                           @keydown.escape="showAutocomplete = false"
                           placeholder="ابحث بالاسم، الموديل، SKU، العلامة التجارية..."
                           class="w-full pl-12 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-lg">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2">
                        <svg x-show="!isLoading" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <svg x-show="isLoading" class="w-5 h-5 text-medical-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- Autocomplete Dropdown --}}
                <div x-show="showAutocomplete && autocompleteResults.length > 0"
                     x-transition
                     @click.away="showAutocomplete = false"
                     class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-lg border border-gray-200 max-h-80 overflow-y-auto">
                    <template x-for="item in autocompleteResults" :key="item.id">
                        <a :href="'/buyer/products/' + item.id"
                           class="flex items-center gap-3 p-3 hover:bg-gray-50 border-b border-gray-100 last:border-0">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-medium text-gray-900 truncate" x-text="item.name"></p>
                                <p class="text-sm text-gray-500">
                                    <span x-text="item.brand || ''"></span>
                                    <span x-show="item.sku" class="text-xs bg-gray-100 px-2 py-0.5 rounded mr-2" x-text="item.sku"></span>
                                </p>
                            </div>
                            <span x-show="item.category" class="text-xs text-medical-blue-600 bg-medical-blue-50 px-2 py-1 rounded-full" x-text="item.category"></span>
                        </a>
                    </template>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="p-4 border-t border-gray-100">
            <form method="GET" action="{{ route('buyer.products.index') }}" class="flex flex-wrap gap-3 items-end">
                {{-- Category Filter --}}
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-gray-500 mb-1">الفئة</label>
                    <select name="category" @change="$el.form.submit()"
                            class="w-full sm:w-48 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500">
                        <option value="">جميع الفئات</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @foreach($category->children as $child)
                                <option value="{{ $child->id }}" {{ request('category') == $child->id ? 'selected' : '' }}>
                                    &nbsp;&nbsp;↳ {{ $child->name }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>

                {{-- Manufacturer Filter --}}
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-gray-500 mb-1">الشركة المصنعة</label>
                    <select name="manufacturer" @change="$el.form.submit()"
                            class="w-full sm:w-40 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500">
                        <option value="">جميع المصنعين</option>
                        @foreach($manufacturers as $manufacturer)
                            <option value="{{ $manufacturer->id }}" {{ request('manufacturer') == $manufacturer->id ? 'selected' : '' }}>
                                {{ $manufacturer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Sort --}}
                <div class="w-full sm:w-auto">
                    <label class="block text-xs font-medium text-gray-500 mb-1">الترتيب</label>
                    <select name="sort" @change="$el.form.submit()"
                            class="w-full sm:w-36 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500">
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الاسم</option>
                        <option value="created_at" {{ request('sort', 'created_at') == 'created_at' ? 'selected' : '' }}>الأحدث</option>
                        <option value="suppliers" {{ request('sort') == 'suppliers' ? 'selected' : '' }}>الأكثر موردين</option>
                    </select>
                </div>

                {{-- Hidden search field --}}
                <input type="hidden" name="search" :value="searchQuery">
                
                {{-- Actions --}}
                <div class="flex items-center gap-2 mr-auto">
                    @if(request()->anyFilled(['search', 'category', 'manufacturer']))
                        <a href="{{ route('buyer.products.index') }}" 
                           class="px-4 py-2 text-gray-500 hover:text-gray-700 font-medium text-sm flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            مسح الفلاتر
                        </a>
                    @endif
                </div>
            </form>
        </div>

    {{-- Active Filters Tags --}}
    @if(request()->anyFilled(['search', 'category', 'manufacturer']))
        <div class="px-4 pb-4 flex flex-wrap gap-2">
            @if(request('search'))
                <span class="inline-flex items-center gap-1 px-3 py-1 bg-gray-100 text-gray-700 rounded-full text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    "{{ request('search') }}"
                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="hover:text-red-500">×</a>
                </span>
            @endif
            @if(request('category'))
                @php $cat = $categories->firstWhere('id', request('category')) ?? $categories->pluck('children')->flatten()->firstWhere('id', request('category')); @endphp
                @if($cat)
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-medical-blue-50 text-medical-blue-700 rounded-full text-sm">
                        {{ $cat->name }}
                        <a href="{{ request()->fullUrlWithQuery(['category' => null]) }}" class="hover:text-red-500">×</a>
                    </span>
                @endif
            @endif
            @if(request('manufacturer'))
                @php $mfr = $manufacturers->firstWhere('id', request('manufacturer')); @endphp
                @if($mfr)
                    <span class="inline-flex items-center gap-1 px-3 py-1 bg-purple-50 text-purple-700 rounded-full text-sm">
                        {{ $mfr->name }}
                        <a href="{{ request()->fullUrlWithQuery(['manufacturer' => null]) }}" class="hover:text-red-500">×</a>
                    </span>
                @endif
            @endif
        </div>
    @endif
    </div>

    {{-- Products Grid/List --}}
    @if($products->count() > 0)
        {{-- Grid View --}}
        <div x-show="view === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
            @foreach($products as $product)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:border-medical-blue-200 transition-all duration-200 group">
                {{-- Image --}}
                <div class="relative aspect-[4/3] bg-gray-50">
                    @if($product->getFirstMediaUrl('product_images'))
                        <img src="{{ $product->getFirstMediaUrl('product_images', 'preview') }}" 
                             alt="{{ $product->name }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                    @endif
                    
                    {{-- Favorite Button --}}
                    <button @click.prevent="$refs.favoriteForm{{ $product->id }}.submit()"
                            class="absolute top-2 left-2 p-2 bg-white/90 backdrop-blur rounded-full shadow-sm hover:bg-red-50 transition-colors">
                        @if(in_array($product->id, $favoriteIds))
                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-gray-400 group-hover:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                        @endif
                    </button>
                    <form x-ref="favoriteForm{{ $product->id }}" action="{{ route('buyer.products.favorite', $product) }}" method="POST" class="hidden">@csrf</form>

                    {{-- Compare Checkbox --}}
                    <label class="absolute bottom-2 right-2 flex items-center gap-1.5 bg-white/90 backdrop-blur rounded-full px-2.5 py-1 shadow-sm cursor-pointer hover:bg-white">
                        <input type="checkbox" 
                               :value="{{ $product->id }}"
                               x-model="compareProducts"
                               :disabled="!compareProducts.includes({{ $product->id }}) && compareProducts.length >= 4"
                               class="w-3.5 h-3.5 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500">
                        <span class="text-xs text-gray-600">قارن</span>
                    </label>
                </div>

                {{-- Content --}}
                <div class="p-4">
                    {{-- Category & SKU --}}
                    <div class="flex items-center justify-between mb-2">
                        @if($product->category)
                            <span class="text-xs text-medical-blue-600 font-medium">{{ $product->category->name }}</span>
                        @endif
                        @if($product->sku)
                            <span class="text-[10px] text-gray-400 font-mono">{{ $product->sku }}</span>
                        @endif
                    </div>
                    
                    {{-- Name --}}
                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-medical-blue-600 transition-colors">
                        {{ $product->name }}
                    </h3>
                    
                    {{-- Brand & Model --}}
                    @if($product->brand || $product->model)
                        <p class="text-sm text-gray-500 mb-3">
                            {{ $product->brand }}{{ $product->brand && $product->model ? ' • ' : '' }}{{ $product->model }}
                        </p>
                    @endif
                    
                    {{-- Price & Suppliers --}}
                    <div class="flex items-end justify-between">
                        @if($product->suppliers->count() > 0)
                            @php
                                $prices = $product->suppliers->pluck('pivot.price')->filter();
                                $minPrice = $prices->min();
                            @endphp
                            @if($minPrice)
                                <div>
                                    <span class="text-xs text-gray-500">يبدأ من</span>
                                    <p class="text-lg font-bold text-medical-blue-600">{{ number_format($minPrice, 0) }} <span class="text-xs">د.ل</span></p>
                                </div>
                            @endif
                            <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                {{ $product->suppliers->count() }} مورد
                            </span>
                        @else
                            <span class="text-xs text-gray-400">لا يوجد موردين</span>
                        @endif
                    </div>

                    {{-- Actions --}}
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('buyer.products.show', $product) }}" 
                           class="flex-1 text-center px-3 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                            التفاصيل
                        </a>
                        @if($product->suppliers->count() > 0)
                            <form action="{{ route('buyer.cart.add', $product) }}" method="POST" class="flex-1">
                                @csrf
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="w-full px-3 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    للسلة
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- List View --}}
        <div x-show="view === 'list'" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @foreach($products as $product)
                <div class="p-4 hover:bg-gray-50 transition-colors">
                    <div class="flex gap-4">
                        {{-- Image --}}
                        <div class="w-32 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 relative">
                            @if($product->getFirstMediaUrl('product_images'))
                                <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}" 
                                     alt="{{ $product->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-1">
                                        @if($product->category)
                                            <span class="text-xs text-medical-blue-600 font-medium">{{ $product->category->name }}</span>
                                        @endif
                                        @if($product->sku)
                                            <span class="text-[10px] text-gray-400 font-mono bg-gray-100 px-1.5 py-0.5 rounded">{{ $product->sku }}</span>
                                        @endif
                                    </div>
                                    <h3 class="font-semibold text-gray-900 truncate">{{ $product->name }}</h3>
                                    @if($product->brand || $product->model)
                                        <p class="text-sm text-gray-500">
                                            {{ $product->brand }}{{ $product->brand && $product->model ? ' • ' : '' }}{{ $product->model }}
                                        </p>
                                    @endif
                                </div>
                                
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <label class="flex items-center gap-1.5 cursor-pointer">
                                        <input type="checkbox" 
                                               :value="{{ $product->id }}"
                                               x-model="compareProducts"
                                               :disabled="!compareProducts.includes({{ $product->id }}) && compareProducts.length >= 4"
                                               class="w-4 h-4 text-medical-blue-600 border-gray-300 rounded">
                                        <span class="text-xs text-gray-600">قارن</span>
                                    </label>
                                    <button @click.prevent="$refs.favoriteFormList{{ $product->id }}.submit()" class="p-1.5 hover:bg-gray-100 rounded-full">
                                        @if(in_array($product->id, $favoriteIds))
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                            </svg>
                                        @endif
                                    </button>
                                    <form x-ref="favoriteFormList{{ $product->id }}" action="{{ route('buyer.products.favorite', $product) }}" method="POST" class="hidden">@csrf</form>
                                </div>
                            </div>
                            
                            <div class="mt-3 flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    @if($product->suppliers->count() > 0)
                                        @php $minPrice = $product->suppliers->pluck('pivot.price')->filter()->min(); @endphp
                                        @if($minPrice)
                                            <span class="font-bold text-medical-blue-600">{{ number_format($minPrice, 0) }} د.ل</span>
                                        @endif
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $product->suppliers->count() }} مورد</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('buyer.products.show', $product) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm font-medium">
                                        التفاصيل
                                    </a>
                                    @if($product->suppliers->count() > 0)
                                        <form action="{{ route('buyer.cart.add', $product) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 text-sm font-medium">
                                                أضف للسلة
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $products->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">لا توجد منتجات</h3>
            <p class="text-gray-500 mb-6">لم يتم العثور على منتجات تطابق معايير البحث</p>
            @if(request()->anyFilled(['search', 'category', 'manufacturer']))
                <a href="{{ route('buyer.products.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
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
                const response = await fetch(`/api/products/autocomplete?q=${encodeURIComponent(this.searchQuery)}`);
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
</script>
</x-dashboard.layout>
