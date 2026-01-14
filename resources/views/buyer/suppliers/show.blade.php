{{-- Buyer Supplier Details Page --}}
<x-dashboard.layout title="{{ $supplier->company_name }}" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-medical-blue-600">لوحة التحكم</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <a href="{{ route('buyer.suppliers.index') }}" class="hover:text-medical-blue-600">دليل الموردين</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="text-gray-900 font-medium">{{ $supplier->company_name }}</span>
        </nav>

        {{-- Supplier Header --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            {{-- Cover --}}
            <div class="h-40 bg-gradient-to-br from-medical-blue-500 via-medical-blue-600 to-medical-blue-700 relative">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,...')] opacity-10"></div>
                {{-- Verified Badge --}}
                <div class="absolute top-4 left-4 flex items-center gap-2 bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm text-white font-medium">مورد معتمد</span>
                </div>
            </div>

            {{-- Profile Info --}}
            <div class="relative px-6 pb-6">
                {{-- Avatar --}}
                <div class="absolute -top-16 right-6 w-32 h-32 bg-white rounded-2xl shadow-lg flex items-center justify-center border-4 border-white overflow-hidden">
                    @if($supplier->getFirstMediaUrl('supplier_images'))
                        <img src="{{ $supplier->getFirstMediaUrl('supplier_images') }}" 
                             alt="{{ $supplier->company_name }}"
                             class="w-full h-full object-cover">
                    @else
                        <span class="text-5xl font-bold text-medical-blue-600">
                            {{ mb_substr($supplier->company_name, 0, 1) }}
                        </span>
                    @endif
                </div>

                <div class="pt-20 sm:pt-4 sm:pr-40">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $supplier->company_name }}</h1>
                            @if($supplier->user)
                                <p class="text-gray-600">{{ $supplier->user->name }}</p>
                            @endif
                            
                            <div class="flex flex-wrap items-center gap-4 mt-3 text-sm text-gray-500">
                                @if($supplier->city || $supplier->country)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        <span>{{ $supplier->city }}{{ $supplier->country ? ', ' . $supplier->country : '' }}</span>
                                    </div>
                                @endif
                                
                                @if($supplier->phone)
                                    <div class="flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                        <span dir="ltr">{{ $supplier->phone }}</span>
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span>عضو منذ {{ $stats['member_since'] }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex gap-2">
                            <a href="{{ route('buyer.rfqs.create', ['supplier' => $supplier->id]) }}" 
                               class="inline-flex items-center px-4 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                طلب عرض سعر
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="w-12 h-12 bg-medical-blue-50 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['products_count'] }}</div>
                <div class="text-sm text-gray-500">منتج معتمد</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="w-12 h-12 bg-medical-green-50 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['accepted_quotations'] }}</div>
                <div class="text-sm text-gray-500">عرض مقبول</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['completed_orders'] }}</div>
                <div class="text-sm text-gray-500">طلب مكتمل</div>
            </div>
            
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
                <div class="w-12 h-12 bg-orange-50 rounded-lg flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">{{ $stats['member_since'] }}</div>
                <div class="text-sm text-gray-500">عضو منذ</div>
            </div>
        </div>

        {{-- About & Categories --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- About --}}
            @if($supplier->description)
                <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">نبذة عن المورد</h2>
                    <p class="text-gray-600 leading-relaxed">{{ $supplier->description }}</p>
                </div>
            @endif
            
            {{-- Categories --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 {{ !$supplier->description ? 'lg:col-span-3' : '' }}">
                <h2 class="text-lg font-bold text-gray-900 mb-4">فئات المنتجات</h2>
                @if($productCategories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($productCategories as $category)
                            <span class="inline-flex items-center px-3 py-1.5 bg-medical-blue-50 text-medical-blue-700 rounded-full text-sm font-medium">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">لا توجد فئات</p>
                @endif
            </div>
        </div>

        {{-- Products Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">منتجات المورد</h2>
                    <span class="text-sm text-gray-500">{{ $products->total() }} منتج</span>
                </div>
            </div>
            
            @if($products->count() > 0)
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($products as $product)
                            <div class="bg-gray-50 rounded-xl overflow-hidden hover:shadow-md transition-shadow group">
                                {{-- Image --}}
                                <div class="relative aspect-[4/3] bg-gray-100">
                                    @if($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ $product->getFirstMediaUrl('product_images', 'preview') }}" 
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    @if($product->pivot->stock_quantity > 0)
                                        <span class="absolute top-2 right-2 bg-medical-green-500 text-white text-xs px-2 py-1 rounded-full">
                                            متوفر
                                        </span>
                                    @else
                                        <span class="absolute top-2 right-2 bg-gray-500 text-white text-xs px-2 py-1 rounded-full">
                                            حسب الطلب
                                        </span>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="p-4">
                                    @if($product->category)
                                        <span class="text-xs text-medical-blue-600 font-medium">{{ $product->category->name }}</span>
                                    @endif
                                    <h3 class="font-medium text-gray-900 mt-1 line-clamp-2">{{ $product->name }}</h3>
                                    
                                    @if($product->pivot->price)
                                        <div class="mt-2 text-lg font-bold text-medical-blue-600">
                                            {{ number_format($product->pivot->price, 2) }} د.ل
                                        </div>
                                    @endif
                                    
                                    @if($product->pivot->warranty)
                                        <div class="mt-1 text-xs text-gray-500">
                                            ضمان: {{ $product->pivot->warranty }}
                                        </div>
                                    @endif

                                    {{-- Actions --}}
                                    <div class="mt-4 flex gap-2">
                                        <a href="{{ route('buyer.products.show', $product) }}" 
                                           class="flex-1 text-center px-3 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors text-sm font-medium">
                                            التفاصيل
                                        </a>
                                        <a href="{{ route('buyer.products.create-rfq', $product) }}" 
                                           class="flex-1 text-center px-3 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
                                            طلب سعر
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد منتجات</h3>
                    <p class="text-gray-500">هذا المورد لم يضف منتجات بعد</p>
                </div>
            @endif
        </div>

        {{-- Reviews Section --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <h2 class="text-lg font-bold text-gray-900">تقييمات المشترين</h2>
                        @if($supplier->reviews_count > 0)
                            <div class="flex items-center gap-2">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= round($supplier->average_rating) ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-600">{{ $supplier->average_rating }} من 5 ({{ $supplier->reviews_count }} تقييم)</span>
                            </div>
                        @endif
                    </div>
                    <a href="{{ route('buyer.reviews.create', ['supplier' => $supplier->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                        أضف تقييمك
                    </a>
                </div>
            </div>

            @if(isset($reviews) && $reviews->count() > 0)
                <div class="divide-y divide-gray-100">
                    @foreach($reviews as $review)
                        <div class="p-6">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-medical-blue-100 rounded-full flex items-center justify-center text-medical-blue-600 font-bold flex-shrink-0">
                                    {{ mb_substr($review->buyer->organization_name ?? 'م', 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-2">
                                        <div>
                                            <h4 class="font-medium text-gray-900">{{ $review->buyer->organization_name ?? 'مشتري' }}</h4>
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                             fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                @if($review->is_verified_purchase)
                                                    <span class="inline-flex items-center text-xs text-green-600">
                                                        <svg class="w-3.5 h-3.5 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        عملية شراء موثقة
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>

                                    @if($review->title)
                                        <h5 class="font-medium text-gray-900 mb-1">{{ $review->title }}</h5>
                                    @endif

                                    @if($review->review)
                                        <p class="text-gray-600 text-sm">{{ $review->review }}</p>
                                    @endif

                                    @if($review->pros || $review->cons)
                                        <div class="flex gap-6 mt-3 text-sm">
                                            @if($review->pros)
                                                <div>
                                                    <span class="text-green-600 font-medium">المميزات:</span>
                                                    <p class="text-gray-600">{{ $review->pros }}</p>
                                                </div>
                                            @endif
                                            @if($review->cons)
                                                <div>
                                                    <span class="text-red-600 font-medium">العيوب:</span>
                                                    <p class="text-gray-600">{{ $review->cons }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    @if($review->would_recommend)
                                        <p class="mt-2 text-sm text-green-600 flex items-center">
                                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                                            </svg>
                                            يوصي بهذا المورد
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($reviews->hasMorePages())
                    <div class="p-4 border-t border-gray-100 text-center">
                        <a href="#" class="text-medical-blue-600 hover:text-medical-blue-800 text-sm font-medium">
                            عرض المزيد من التقييمات
                        </a>
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تقييمات بعد</h3>
                    <p class="text-gray-500 mb-4">كن أول من يقيّم هذا المورد</p>
                    <a href="{{ route('buyer.reviews.create', ['supplier' => $supplier->id]) }}" 
                       class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                        أضف تقييمك
                    </a>
                </div>
            @endif
        </div>

        {{-- Back Button --}}
        <div class="flex justify-start">
            <a href="{{ route('buyer.suppliers.index') }}" 
               class="inline-flex items-center px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة إلى دليل الموردين
            </a>
        </div>
    </div>
</x-dashboard.layout>

