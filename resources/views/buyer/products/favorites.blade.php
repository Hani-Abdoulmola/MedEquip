<x-dashboard.layout title="المنتجات المفضلة" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">المنتجات المفضلة</h1>
            <p class="mt-1 text-sm text-gray-500">المنتجات التي قمت بحفظها للرجوع إليها لاحقاً</p>
        </div>
        <a href="{{ route('buyer.products.index') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            تصفح المنتجات
        </a>
    </div>

    @if($favorites->count() > 0)
        {{-- Products Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($favorites as $favorite)
                @php $product = $favorite->product; @endphp
                @if($product)
                <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 overflow-hidden hover:shadow-lg hover:border-medical-blue-300 transition-all duration-200 group">
                    {{-- Image --}}
                    <div class="relative aspect-[4/3] bg-gray-100">
                        @if($product->getFirstMediaUrl('product_images'))
                            <img src="{{ $product->getFirstMediaUrl('product_images', 'preview') }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        
                        {{-- Remove from Favorites --}}
                        <form action="{{ route('buyer.products.favorite', $product) }}" method="POST" 
                              class="absolute top-3 left-3">
                            @csrf
                            <button type="submit" 
                                    class="p-2.5 bg-white/90 backdrop-blur rounded-full shadow-md hover:bg-red-50 transition-all"
                                    title="إزالة من المفضلة">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>

                        {{-- Added Date --}}
                        <div class="absolute bottom-3 right-3 bg-black/70 backdrop-blur text-white text-xs px-2.5 py-1 rounded-full">
                            أضيف {{ $favorite->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5">
                        <div class="mb-3">
                            @if($product->category)
                                <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}" 
                                   class="text-xs text-medical-blue-600 font-semibold hover:text-medical-blue-800 bg-medical-blue-50 px-2 py-1 rounded-full inline-block">
                                    {{ $product->category->name }}
                                </a>
                            @endif
                        </div>
                        <h3 class="font-bold text-gray-900 mb-2 line-clamp-2 min-h-[3rem] group-hover:text-medical-blue-600 transition-colors">
                            <a href="{{ route('buyer.products.show', $product) }}">{{ $product->name }}</a>
                        </h3>
                        @if($product->brand)
                            <p class="text-sm text-gray-600 mb-3 font-medium">{{ $product->brand }}</p>
                        @endif

                        {{-- Price --}}
                        @if($product->suppliers->count() > 0)
                            @php
                                $prices = $product->suppliers->pluck('pivot.price')->filter();
                                $minPrice = $prices->min();
                            @endphp
                            @if($minPrice)
                                <div class="mb-4 p-3 bg-gradient-to-br from-medical-blue-50 to-medical-green-50 rounded-xl border border-medical-blue-200">
                                    <div class="text-xs text-gray-600 mb-1">يبدأ من</div>
                                    <div class="text-xl font-bold text-medical-blue-600">
                                        {{ number_format($minPrice, 0) }} <span class="text-sm font-normal">د.ل</span>
                                    </div>
                                </div>
                            @endif
                        @endif

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('buyer.products.show', $product) }}" 
                               class="flex-1 text-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all text-sm font-semibold">
                                التفاصيل
                            </a>
                            <a href="{{ route('buyer.products.create-rfq', $product) }}" 
                               class="flex-1 text-center px-4 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all text-sm font-semibold shadow-sm hover:shadow-md">
                                طلب سعر
                            </a>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $favorites->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد منتجات مفضلة</h3>
            <p class="text-gray-500 mb-4">لم تقم بإضافة أي منتجات للمفضلة بعد</p>
            <a href="{{ route('buyer.products.index') }}" 
               class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                تصفح المنتجات
            </a>
        </div>
    @endif
</div>
</x-dashboard.layout>
