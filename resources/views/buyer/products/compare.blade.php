<x-dashboard.layout title="مقارنة المنتجات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('buyer.products.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">مقارنة المنتجات</h1>
            </div>
            <p class="text-sm text-gray-500">قارن حتى 4 منتجات جنباً إلى جنب</p>
        </div>
    </div>

    @if($products->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    {{-- Product Images & Names --}}
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-500 sticky right-0 bg-gray-50 z-10 min-w-[200px]">
                                المنتج
                            </th>
                            @foreach($products as $product)
                            <th class="px-6 py-4 text-center min-w-[250px]">
                                <div class="flex flex-col items-center">
                                    <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden mb-3">
                                        @if($product->hasMedia('product_images'))
                                            @php
                                                $firstMedia = $product->getFirstMedia('product_images');
                                                $imageUrl = $firstMedia ? ($firstMedia->getUrl('thumb') ?: $firstMedia->getUrl()) : null;
                                            @endphp
                                            @if($imageUrl)
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $product->name }}"
                                                     class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <a href="{{ route('buyer.products.show', $product) }}" 
                                       class="font-medium text-gray-900 hover:text-medical-blue-600 text-center line-clamp-2">
                                        {{ $product->name }}
                                    </a>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        {{-- Category --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">الفئة</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $product->category?->name ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Brand --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-50 z-10">الماركة</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $product->brand ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Model --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">الموديل</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $product->model ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Manufacturer --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-50 z-10">المصنع</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $product->manufacturer?->name ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Price Range --}}
                        <tr class="bg-blue-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-blue-50 z-10">💰 نطاق السعر</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center">
                                @if($product->suppliers->count() > 0)
                                    @php
                                        $prices = $product->suppliers->pluck('pivot.price')->filter();
                                        $minPrice = $prices->min();
                                        $maxPrice = $prices->max();
                                    @endphp
                                    @if($minPrice)
                                        <span class="font-bold text-medical-blue-600">
                                            {{ number_format($minPrice, 2) }}
                                            @if($maxPrice && $maxPrice != $minPrice)
                                                - {{ number_format($maxPrice, 2) }}
                                            @endif
                                            د.ل
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Suppliers Count --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">عدد الموردين</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $product->suppliers->count() }} مورد
                            </td>
                            @endforeach
                        </tr>

                        {{-- Description --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-50 z-10">الوصف</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-sm text-gray-600 text-center">
                                {{ Str::limit($product->description, 100) ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Specifications (if available) --}}
                        @php
                            $allSpecs = [];
                            foreach($products as $product) {
                                if($product->specifications && is_array($product->specifications)) {
                                    $allSpecs = array_merge($allSpecs, array_keys($product->specifications));
                                }
                            }
                            $allSpecs = array_unique($allSpecs);
                        @endphp

                        @if(count($allSpecs) > 0)
                            <tr class="bg-gray-100">
                                <td colspan="{{ $products->count() + 1 }}" class="px-6 py-3 text-sm font-bold text-gray-700">
                                    📋 المواصفات التقنية
                                </td>
                            </tr>
                            @foreach($allSpecs as $spec)
                            <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="px-6 py-3 text-sm font-medium text-gray-700 sticky right-0 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }} z-10">
                                    {{ $spec }}
                                </td>
                                @foreach($products as $product)
                                <td class="px-6 py-3 text-center text-sm text-gray-900">
                                    {{ $product->specifications[$spec] ?? '-' }}
                                </td>
                                @endforeach
                            </tr>
                            @endforeach
                        @endif

                        {{-- Actions --}}
                        <tr class="bg-gray-100">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-100 z-10">الإجراءات</td>
                            @foreach($products as $product)
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <a href="{{ route('buyer.products.show', $product) }}" 
                                       class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                        عرض التفاصيل
                                    </a>
                                    <a href="{{ route('buyer.products.create-rfq', $product) }}" 
                                       class="px-4 py-2 bg-medical-blue-600 text-white text-sm rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                                        طلب سعر
                                    </a>
                                </div>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد منتجات للمقارنة</h3>
            <p class="text-gray-500 mb-4">اختر منتجين على الأقل من كتالوج المنتجات للمقارنة</p>
            <a href="{{ route('buyer.products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                تصفح المنتجات
            </a>
        </div>
    @endif
</div>
</x-dashboard.layout>
