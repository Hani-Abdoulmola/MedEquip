<x-dashboard.layout :title="$product->name" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-500">
        <a href="{{ route('buyer.products.index') }}" class="hover:text-medical-blue-600">المنتجات</a>
        <span>←</span>
        @if($product->category)
            <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}" class="hover:text-medical-blue-600">
                {{ $product->category->name }}
            </a>
            <span>←</span>
        @endif
        <span class="text-gray-900">{{ $product->name }}</span>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Left Column - Images --}}
        <div class="space-y-4">
            {{-- Main Image --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden aspect-square" x-data="{ currentImage: '{{ $product->getFirstMediaUrl('product_images', 'preview') ?: '' }}' }">
                @if($product->getFirstMediaUrl('product_images'))
                    <img :src="currentImage || '{{ $product->getFirstMediaUrl('product_images', 'preview') }}'" 
                         alt="{{ $product->name }}"
                         class="w-full h-full object-contain p-4">
                @else
                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                        <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                @endif
            </div>

            {{-- Thumbnails --}}
            @if($product->getMedia('product_images')->count() > 1)
                <div class="flex gap-2 overflow-x-auto pb-2">
                    @foreach($product->getMedia('product_images') as $media)
                        <button @click="currentImage = '{{ $media->getUrl('preview') }}'"
                                class="flex-shrink-0 w-20 h-20 rounded-lg border-2 border-gray-200 overflow-hidden hover:border-medical-blue-500 transition-colors">
                            <img src="{{ $media->getUrl('thumb') }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        </button>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Right Column - Details --}}
        <div class="space-y-6">
            {{-- Header --}}
            <div>
                @if($product->category)
                    <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}" 
                       class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                        {{ $product->category->name }}
                    </a>
                @endif
                <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>
                
                <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                    @if($product->brand)
                        <span>الماركة: <strong class="text-gray-900">{{ $product->brand }}</strong></span>
                    @endif
                    @if($product->model)
                        <span>الموديل: <strong class="text-gray-900">{{ $product->model }}</strong></span>
                    @endif
                    @if($product->sku)
                        <span>SKU: <strong class="text-gray-900 font-mono">{{ $product->sku }}</strong></span>
                    @endif
                </div>
            </div>

            {{-- Price Range --}}
            @if($product->suppliers->count() > 0)
                @php
                    $prices = $product->suppliers->pluck('pivot.price')->filter();
                    $minPrice = $prices->min();
                    $maxPrice = $prices->max();
                @endphp
                @if($minPrice)
                    <div class="bg-medical-blue-50 rounded-xl p-4">
                        <div class="text-sm text-medical-blue-700 mb-1">نطاق الأسعار</div>
                        <div class="text-2xl font-bold text-medical-blue-600">
                            {{ number_format($minPrice, 2) }}
                            @if($maxPrice && $maxPrice != $minPrice)
                                - {{ number_format($maxPrice, 2) }}
                            @endif
                            د.ل
                        </div>
                        <div class="text-sm text-medical-blue-600 mt-1">
                            من {{ $product->suppliers->count() }} مورد
                        </div>
                    </div>
                @endif
            @endif

            {{-- Actions --}}
            <div class="flex gap-3">
                <a href="{{ route('buyer.products.create-rfq', $product) }}" 
                   class="flex-1 text-center px-6 py-3 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    طلب عرض سعر
                </a>
                <form action="{{ route('buyer.products.favorite', $product) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="px-6 py-3 border-2 {{ $isFavorite ? 'border-red-500 text-red-500' : 'border-gray-300 text-gray-700' }} rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        @if($isFavorite)
                            <svg class="w-5 h-5 inline-block ml-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            في المفضلة
                        @else
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                            </svg>
                            أضف للمفضلة
                        @endif
                    </button>
                </form>
            </div>

            {{-- Description --}}
            @if($product->description)
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">الوصف</h2>
                    <p class="text-gray-600">{{ $product->description }}</p>
                </div>
            @endif

            {{-- Specifications --}}
            @if($product->specifications && is_array($product->specifications))
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">المواصفات</h2>
                    <dl class="grid grid-cols-2 gap-x-4 gap-y-2 text-sm">
                        @foreach($product->specifications as $key => $value)
                            <dt class="text-gray-500">{{ $key }}</dt>
                            <dd class="text-gray-900 font-medium">{{ $value }}</dd>
                        @endforeach
                    </dl>
                </div>
            @endif

            {{-- Manufacturer --}}
            @if($product->manufacturer)
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">المصنع</h2>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $product->manufacturer->name }}</div>
                            @if($product->manufacturer->country)
                                <div class="text-sm text-gray-500">{{ $product->manufacturer->country }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Suppliers --}}
    @if($product->suppliers->count() > 0)
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">الموردين المتاحين ({{ $product->suppliers->count() }})</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المورد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">السعر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المخزون</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">مدة التوريد</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الضمان</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($product->suppliers as $supplier)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold">
                                        {{ substr($supplier->user?->name ?? 'م', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-gray-900">{{ $supplier->user?->name ?? 'مورد' }}</div>
                                        <div class="text-sm text-gray-500">{{ $supplier->company_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplier->pivot->price)
                                    <span class="font-bold text-gray-900">{{ number_format($supplier->pivot->price, 2) }} د.ل</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($supplier->pivot->stock_quantity)
                                    <span class="{{ $supplier->pivot->stock_quantity > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                                        {{ $supplier->pivot->stock_quantity }} وحدة
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                {{ $supplier->pivot->lead_time ?? '-' }} يوم
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-900">
                                {{ $supplier->pivot->warranty ?? '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- Related Products --}}
    @if($relatedProducts->count() > 0)
        <div>
            <h2 class="text-lg font-semibold text-gray-900 mb-4">منتجات ذات صلة</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('buyer.products.show', $related) }}" 
                       class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-square bg-gray-100">
                            @if($related->getFirstMediaUrl('product_images'))
                                <img src="{{ $related->getFirstMediaUrl('product_images', 'thumb') }}" 
                                     alt="{{ $related->name }}"
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="p-3">
                            <h3 class="font-medium text-gray-900 text-sm line-clamp-2">{{ $related->name }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</div>
</x-dashboard.layout>

