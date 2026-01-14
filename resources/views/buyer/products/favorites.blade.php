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
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
                    {{-- Image --}}
                    <div class="relative aspect-[4/3] bg-gray-100">
                        @if($product->getFirstMediaUrl('product_images'))
                            <img src="{{ $product->getFirstMediaUrl('product_images', 'preview') }}" 
                                 alt="{{ $product->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
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
                                    class="p-2 bg-white rounded-full shadow-sm hover:bg-red-50 transition-colors"
                                    title="إزالة من المفضلة">
                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </form>

                        {{-- Added Date --}}
                        <div class="absolute bottom-3 right-3 bg-black/50 text-white text-xs px-2 py-1 rounded">
                            أضيف {{ $favorite->created_at->diffForHumans() }}
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-4">
                        <div class="mb-2">
                            @if($product->category)
                                <span class="text-xs text-medical-blue-600 font-medium">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        <h3 class="font-medium text-gray-900 mb-1 line-clamp-2">{{ $product->name }}</h3>
                        @if($product->brand)
                            <p class="text-sm text-gray-500 mb-2">{{ $product->brand }}</p>
                        @endif

                        {{-- Actions --}}
                        <div class="mt-4 flex gap-2">
                            <a href="{{ route('buyer.products.show', $product) }}" 
                               class="flex-1 text-center px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                التفاصيل
                            </a>
                            <a href="{{ route('buyer.products.create-rfq', $product) }}" 
                               class="flex-1 text-center px-3 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
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
