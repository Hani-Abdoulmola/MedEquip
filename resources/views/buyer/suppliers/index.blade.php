{{-- Buyer Suppliers Directory --}}
<x-dashboard.layout title="دليل الموردين" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">دليل الموردين المعتمدين</h1>
                <p class="mt-1 text-sm text-gray-500">تصفح الموردين المعتمدين واستعرض منتجاتهم</p>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <div class="flex items-center gap-2 bg-medical-blue-50 px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span class="font-medium text-medical-blue-700">{{ $stats['total_suppliers'] }} مورد معتمد</span>
                </div>
                <div class="flex items-center gap-2 bg-medical-green-50 px-4 py-2 rounded-lg">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <span class="font-medium text-medical-green-700">{{ $stats['total_products'] }} منتج</span>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <form method="GET" action="{{ route('buyer.suppliers.index') }}" class="flex flex-col lg:flex-row gap-4">
                {{-- Search --}}
                <div class="flex-1">
                    <div class="relative">
                        <input type="text" 
                               name="search" 
                               value="{{ request('search') }}"
                               placeholder="ابحث عن مورد بالاسم أو الموقع..."
                               class="w-full pl-4 pr-10 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
                
                {{-- City Filter --}}
                <div class="w-full lg:w-48">
                    <select name="city" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">جميع المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>
                                {{ $city }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                {{-- Category Filter --}}
                <div class="w-full lg:w-56">
                    <select name="category" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
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
                
                {{-- Sort --}}
                <div class="w-full lg:w-44">
                    <select name="sort" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="products_count" {{ request('sort') == 'products_count' ? 'selected' : '' }}>الأكثر منتجات</option>
                        <option value="orders" {{ request('sort') == 'orders' ? 'selected' : '' }}>الأكثر طلبات</option>
                        <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>الاسم</option>
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>الأحدث</option>
                    </select>
                </div>
                
                <button type="submit" class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    بحث
                </button>
                
                @if(request()->hasAny(['search', 'city', 'category', 'sort']))
                    <a href="{{ route('buyer.suppliers.index') }}" class="px-6 py-2.5 text-gray-500 hover:text-gray-700 font-medium">
                        مسح
                    </a>
                @endif
            </form>
        </div>

        {{-- Suppliers Grid --}}
        @if($suppliers->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($suppliers as $supplier)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                    {{-- Header with gradient --}}
                    <div class="relative h-32 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 p-4">
                        {{-- Verified Badge --}}
                        <div class="absolute top-3 left-3 flex items-center gap-1 bg-white/20 backdrop-blur-sm px-2 py-1 rounded-full">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="text-xs text-white font-medium">معتمد</span>
                        </div>
                        
                        {{-- Company Initial/Logo --}}
                        <div class="absolute -bottom-10 right-4 w-20 h-20 bg-white rounded-xl shadow-lg flex items-center justify-center border-4 border-white">
                            @if($supplier->getFirstMediaUrl('supplier_images'))
                                <img src="{{ $supplier->getFirstMediaUrl('supplier_images', 'thumb') }}" 
                                     alt="{{ $supplier->company_name }}"
                                     class="w-full h-full object-cover rounded-lg">
                            @else
                                <span class="text-3xl font-bold text-medical-blue-600">
                                    {{ mb_substr($supplier->company_name, 0, 1) }}
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Content --}}
                    <div class="pt-12 px-4 pb-4">
                        <div class="mb-3">
                            <h3 class="font-bold text-lg text-gray-900 group-hover:text-medical-blue-600 transition-colors">
                                {{ $supplier->company_name }}
                            </h3>
                            @if($supplier->city || $supplier->country)
                                <div class="flex items-center gap-1 text-sm text-gray-500 mt-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <span>{{ $supplier->city }}{{ $supplier->country ? ', ' . $supplier->country : '' }}</span>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Stats --}}
                        <div class="grid grid-cols-3 gap-2 py-3 border-y border-gray-100">
                            <div class="text-center">
                                <div class="text-lg font-bold text-medical-blue-600">{{ $supplier->products_count }}</div>
                                <div class="text-xs text-gray-500">منتج</div>
                            </div>
                            <div class="text-center border-x border-gray-100">
                                <div class="text-lg font-bold text-medical-green-600">{{ $supplier->quotations_count }}</div>
                                <div class="text-xs text-gray-500">عرض مقبول</div>
                            </div>
                            <div class="text-center">
                                <div class="text-lg font-bold text-purple-600">{{ $supplier->orders_count }}</div>
                                <div class="text-xs text-gray-500">طلب</div>
                            </div>
                        </div>
                        
                        {{-- Products Preview --}}
                        @if($supplier->products->count() > 0)
                            <div class="mt-3">
                                <p class="text-xs text-gray-500 mb-2">بعض المنتجات:</p>
                                <div class="flex gap-2 overflow-hidden">
                                    @foreach($supplier->products->take(4) as $product)
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                            @if($product->getFirstMediaUrl('product_images'))
                                                <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}" 
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        {{-- Actions --}}
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('buyer.suppliers.show', $supplier) }}" 
                               class="flex-1 text-center px-4 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
                                عرض الملف الكامل
                            </a>
                            <a href="{{ route('buyer.products.index', ['supplier' => $supplier->id]) }}" 
                               class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $suppliers->links() }}
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا يوجد موردين</h3>
                <p class="text-gray-500 mb-4">لم يتم العثور على موردين يطابقون معايير البحث</p>
                <a href="{{ route('buyer.suppliers.index') }}" class="inline-flex items-center text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                    <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    عرض جميع الموردين
                </a>
            </div>
        @endif
    </div>
</x-dashboard.layout>

