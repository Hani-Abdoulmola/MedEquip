<x-dashboard.layout :title="$product->name" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6">
        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('buyer.products.index') }}" class="hover:text-medical-blue-600">المنتجات</a>
            <span>←</span>
            @if ($product->category)
                <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}"
                    class="hover:text-medical-blue-600">
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
                @php
                    $firstMedia = $product->hasMedia('product_images')
                        ? $product->getFirstMedia('product_images')
                        : null;
                    $mainImageUrl = $firstMedia ? ($firstMedia->getUrl('preview') ?: $firstMedia->getUrl()) : '';
                @endphp
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden aspect-square"
                    x-data="{ currentImage: '{{ $mainImageUrl }}' }">
                    @if ($firstMedia && $mainImageUrl)
                        <img :src="currentImage || '{{ $mainImageUrl }}'" alt="{{ $product->name }}"
                            class="w-full h-full object-contain p-4">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-32 h-32" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                    @endif
                </div>

                {{-- Thumbnails --}}
                @if ($product->hasMedia('product_images') && $product->getMedia('product_images')->count() > 1)
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach ($product->getMedia('product_images') as $media)
                            @php
                                $thumbUrl = $media->getUrl('thumb') ?: $media->getUrl();
                                $previewUrl = $media->getUrl('preview') ?: $media->getUrl();
                            @endphp
                            <button @click="currentImage = '{{ $previewUrl }}'"
                                class="flex-shrink-0 w-20 h-20 rounded-lg border-2 border-gray-200 overflow-hidden hover:border-medical-blue-500 transition-colors">
                                <img src="{{ $thumbUrl }}" alt="{{ $product->name }}"
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
                    @if ($product->category)
                        <a href="{{ route('buyer.products.index', ['category' => $product->category->id]) }}"
                            class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                            {{ $product->category->name }}
                        </a>
                    @endif
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $product->name }}</h1>

                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                        @if ($product->brand)
                            <span>الماركة: <strong class="text-gray-900">{{ $product->brand }}</strong></span>
                        @endif
                        @if ($product->model)
                            <span>الموديل: <strong class="text-gray-900">{{ $product->model }}</strong></span>
                        @endif
                        @if ($product->sku)
                            <span>SKU: <strong class="text-gray-900 font-mono">{{ $product->sku }}</strong></span>
                        @endif
                    </div>
                </div>

                {{-- Availability & Price Range (Phase 2) --}}
                @php
                    $availability = $product->getAvailabilityStatus();
                    $sc = $product->suppliers_count ?? 0;
                @endphp
                @if ($sc > 0)
                    @php
                        $prices = $product->suppliers->pluck('pivot.price')->filter();
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                    @endphp
                    @if ($minPrice)
                        <div
                            class="bg-gradient-to-br from-medical-blue-50 via-medical-green-50 to-medical-blue-50 rounded-xl p-5 border-2 border-medical-blue-200 shadow-sm">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <span
                                        class="text-xs font-bold px-3 py-1 rounded-full border {{ $availability['color'] }}">
                                        {{ $availability['badge'] }}
                                    </span>
                                    <span
                                        class="text-xs font-bold text-medical-blue-600 bg-white px-2 py-1 rounded-full">
                                        {{ $sc }} {{ $sc == 1 ? 'مورد' : 'موردين' }}
                                    </span>
                                </div>
                            </div>
                            <div class="text-3xl font-bold text-medical-blue-600 mb-1">
                                {{ number_format($minPrice, 2) }}
                                @if ($maxPrice && $maxPrice != $minPrice)
                                    <span class="text-xl font-normal text-gray-600">-
                                        {{ number_format($maxPrice, 2) }}</span>
                                @endif
                                <span class="text-lg font-normal"> د.ل</span>
                            </div>
                            <div class="text-xs text-medical-blue-600 mt-2">
                                💡 قارن الأسعار، مدة التوريد، والضمان من جميع الموردين أدناه
                            </div>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200">
                            <div class="flex items-center gap-2 mb-2">
                                <span
                                    class="text-xs font-bold px-3 py-1 rounded-full border {{ $availability['color'] }}">
                                    {{ $availability['badge'] }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">اتصل بالسعر</div>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 rounded-xl p-5 border-2 border-gray-200 text-center">
                        <div class="text-sm text-gray-600">لا يوجد موردين متاحين حالياً</div>
                        <div class="text-xs text-gray-500 mt-1">يمكنك طلب عرض سعر للمنتج</div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="flex gap-3">
                    @if ($sc > 0)
                        <form action="{{ route('buyer.cart.add', $product) }}" method="POST" class="flex-1">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit"
                                class="w-full text-center px-6 py-3.5 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 transition-all font-semibold shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                أضف إلى السلة
                            </button>
                        </form>
                    @else
                        <a href="{{ route('buyer.products.create-rfq', $product) }}"
                            class="flex-1 text-center px-6 py-3.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all font-semibold shadow-sm hover:shadow-md">
                            <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            طلب عرض سعر
                        </a>
                    @endif
                    <div class="flex-shrink-0 flex gap-2">
                        <form action="{{ route('buyer.products.favorite', $product) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-4 py-3.5 border-2 {{ $isFavorite ? 'border-red-500 bg-red-50 text-red-600' : 'border-gray-300 text-gray-700 hover:bg-gray-50' }} rounded-xl transition-all font-semibold">
                                @if ($isFavorite)
                                    <svg class="w-5 h-5 inline-block ml-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" />
                                    </svg>
                                    في المفضلة
                                @else
                                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                                        </path>
                                    </svg>
                                    أضف للمفضلة
                                @endif
                            </button>
                        </form>
                        @if ($isFavorite && $sc > 0 && ($product->min_price ?? null))
                            <div class="relative" x-data="{ showPriceAlert: false }">
                                <button @click="showPriceAlert = true"
                                    class="px-4 py-3.5 border-2 border-blue-300 bg-blue-50 text-blue-600 rounded-xl hover:bg-blue-100 transition-all font-semibold">
                                    🔔 تنبيه السعر
                                </button>
                                <div x-show="showPriceAlert" x-cloak @click.away="showPriceAlert = false"
                                    class="absolute bottom-full mb-2 right-0 bg-white rounded-lg shadow-lg border border-gray-200 p-4 min-w-[250px] z-10">
                                    <h4 class="font-bold text-gray-900 mb-2">تنبيه انخفاض السعر</h4>
                                    <form action="{{ route('buyer.products.price-alert', $product) }}"
                                        method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">عندما ينخفض
                                                السعر إلى</label>
                                            <input type="number" name="target_price" step="0.01" min="0"
                                                value="{{ number_format($product->min_price * 0.9, 2) }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            <p class="text-xs text-gray-500 mt-1">السعر الحالي:
                                                {{ number_format($product->min_price, 2) }} د.ل</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <button type="button" @click="showPriceAlert = false"
                                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                                إلغاء
                                            </button>
                                            <button type="submit"
                                                class="flex-1 px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                                حفظ
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Description --}}
                @if ($product->description)
                    <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            الوصف
                        </h2>
                        <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif

                {{-- Specifications --}}
                @if ($product->specifications && is_array($product->specifications))
                    <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-5">
                        <h2 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01">
                                </path>
                            </svg>
                            المواصفات التقنية
                        </h2>
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($product->specifications as $key => $value)
                                <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <dt class="text-xs font-semibold text-gray-600 mb-1 uppercase tracking-wide">
                                        {{ $key }}</dt>
                                    <dd class="text-sm font-bold text-gray-900">{{ $value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endif

                {{-- Manufacturer --}}
                @if ($product->manufacturer)
                    <div>
                        <h2 class="font-semibold text-gray-900 mb-2">المصنع</h2>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center text-gray-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <div class="font-medium text-gray-900">{{ $product->manufacturer->name }}</div>
                                @if ($product->manufacturer->country)
                                    <div class="text-sm text-gray-500">{{ $product->manufacturer->country }}</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Suppliers Comparison Table (Phase 2) --}}
        @if ($product->suppliers->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 overflow-hidden"
                x-data="{ view: 'table' }">
                <div class="p-6 border-b-2 border-gray-200 bg-gradient-to-r from-medical-blue-50 to-white">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-xl font-bold text-gray-900">مقارنة الموردين</h2>
                        <div class="flex items-center gap-3">
                            <span
                                class="text-sm font-semibold text-medical-blue-600 bg-white px-3 py-1 rounded-full border border-medical-blue-200">
                                {{ $product->suppliers->count() }}
                                {{ $product->suppliers->count() == 1 ? 'مورد' : 'موردين' }}
                            </span>
                            <div class="flex items-center bg-white rounded-lg p-1 border border-gray-200">
                                <button @click="view = 'table'"
                                    :class="view === 'table' ? 'bg-medical-blue-600 text-white' : 'text-gray-600'"
                                    class="px-3 py-1 rounded text-sm font-semibold transition-colors">
                                    جدول
                                </button>
                                <button @click="view = 'cards'"
                                    :class="view === 'cards' ? 'bg-medical-blue-600 text-white' : 'text-gray-600'"
                                    class="px-3 py-1 rounded text-sm font-semibold transition-colors">
                                    بطاقات
                                </button>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">قارن الأسعار، مدة التوريد، والضمان من جميع الموردين</p>
                </div>

                {{-- Table View --}}
                <div x-show="view === 'table'" class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b-2 border-gray-200">
                            <tr>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">المورد</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">السعر</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">المخزون</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">مدة التوريد</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">الضمان</th>
                                <th class="px-4 py-3 text-right text-sm font-bold text-gray-700">القيمة</th>
                                <th class="px-4 py-3 text-center text-sm font-bold text-gray-700">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($product->suppliers->sortByDesc('value_score') as $supplier)
                                <tr
                                    class="hover:bg-gray-50 transition-colors {{ $supplier->is_best_value ?? false ? 'bg-green-50 border-l-4 border-green-500' : '' }}">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($supplier->is_best_value ?? false)
                                                <span class="text-yellow-500 text-lg">⭐</span>
                                            @endif
                                            <div>
                                                <div class="font-bold text-gray-900">{{ $supplier->company_name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $supplier->user?->name ?? 'مورد' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($supplier->pivot->price)
                                            <div class="font-bold text-medical-blue-600">
                                                {{ number_format($supplier->pivot->price, 2) }} د.ل</div>
                                            @php
                                                $prices = $product->suppliers->pluck('pivot.price')->filter();
                                                $minPrice = $prices->min();
                                                $isBestPrice = $supplier->pivot->price == $minPrice;
                                            @endphp
                                            @if ($isBestPrice && $product->suppliers->count() > 1)
                                                <span class="text-xs text-green-600 font-semibold">أفضل سعر</span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400">عند الطلب</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($supplier->pivot->stock_quantity)
                                            <span
                                                class="text-sm font-semibold {{ $supplier->pivot->stock_quantity > 10 ? 'text-green-600' : ($supplier->pivot->stock_quantity > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                                {{ $supplier->pivot->stock_quantity }} وحدة
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($supplier->pivot->lead_time)
                                            <span
                                                class="text-sm font-semibold text-gray-900">{{ $supplier->pivot->lead_time }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if ($supplier->pivot->warranty)
                                            <span
                                                class="text-sm font-semibold text-blue-600">{{ $supplier->pivot->warranty }}</span>
                                        @else
                                            <span class="text-sm text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        @if (isset($supplier->value_score))
                                            <div class="flex items-center gap-2">
                                                <div class="flex-1 bg-gray-200 rounded-full h-2 max-w-[60px]">
                                                    <div class="bg-gradient-to-r from-medical-blue-500 to-medical-green-500 h-2 rounded-full"
                                                        style="width: {{ min(100, $supplier->value_score / 10) }}%">
                                                    </div>
                                                </div>
                                                <span
                                                    class="text-xs font-bold text-gray-700">{{ number_format($supplier->value_score, 0) }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-2 justify-center">
                                            <a href="{{ route('buyer.suppliers.show', $supplier) }}"
                                                class="px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-semibold">
                                                عرض
                                            </a>
                                            <form action="{{ route('buyer.cart.add', $product) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="supplier_id"
                                                    value="{{ $supplier->id }}">
                                                <button type="submit"
                                                    class="px-3 py-1.5 text-xs bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-semibold flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                        </path>
                                                    </svg>
                                                    أضف إلى السلة
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Cards View (Original) --}}
                <div x-show="view === 'cards'" class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($product->suppliers->sortByDesc('value_score') as $supplier)
                            @php
                                $prices = $product->suppliers->pluck('pivot.price')->filter();
                                $minPrice = $prices->min();
                                $isBestPrice = $supplier->pivot->price && $supplier->pivot->price == $minPrice;
                            @endphp
                            <div
                                class="border-2 {{ $supplier->is_best_value ?? false ? 'border-green-300 bg-green-50' : ($isBestPrice ? 'border-yellow-300 bg-yellow-50' : 'border-gray-200 bg-white') }} rounded-xl p-5 hover:shadow-md transition-all">
                                {{-- Best Value / Best Price Badge --}}
                                @if (($supplier->is_best_value ?? false) && $product->suppliers->count() > 1)
                                    <div class="flex items-center gap-2 mb-3">
                                        <span
                                            class="text-xs font-bold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                                            ⭐ أفضل قيمة
                                        </span>
                                    </div>
                                @elseif($isBestPrice && $product->suppliers->count() > 1)
                                    <div class="flex items-center gap-2 mb-3">
                                        <span
                                            class="text-xs font-bold text-yellow-700 bg-yellow-100 px-2 py-1 rounded-full">
                                            💰 أفضل سعر
                                        </span>
                                    </div>
                                @endif

                                {{-- Supplier Info --}}
                                <div class="flex items-center gap-3 mb-4">
                                    <div
                                        class="w-14 h-14 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-full flex items-center justify-center text-medical-blue-700 font-bold text-lg flex-shrink-0">
                                        {{ substr($supplier->user?->name ?? 'م', 0, 1) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-bold text-gray-900 truncate">
                                            {{ $supplier->user?->name ?? 'مورد' }}</h3>
                                        <p class="text-sm text-gray-600 truncate">{{ $supplier->company_name }}</p>
                                    </div>
                                </div>

                                {{-- Price --}}
                                @if ($supplier->pivot->price)
                                    <div
                                        class="mb-4 p-3 bg-gradient-to-br from-medical-blue-50 to-medical-green-50 rounded-lg border border-medical-blue-200">
                                        <div class="text-xs text-gray-600 mb-1">السعر</div>
                                        <div class="text-2xl font-bold text-medical-blue-600">
                                            {{ number_format($supplier->pivot->price, 2) }} <span
                                                class="text-sm font-normal">د.ل</span>
                                        </div>
                                    </div>
                                @else
                                    <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200 text-center">
                                        <span class="text-sm text-gray-500">السعر عند الطلب</span>
                                    </div>
                                @endif

                                {{-- Details Grid --}}
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    @if ($supplier->pivot->stock_quantity)
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-600 mb-1">المخزون</div>
                                            <div
                                                class="text-sm font-bold {{ $supplier->pivot->stock_quantity > 10 ? 'text-green-600' : 'text-yellow-600' }}">
                                                {{ $supplier->pivot->stock_quantity }} وحدة
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-600 mb-1">المخزون</div>
                                            <div class="text-sm text-gray-400">-</div>
                                        </div>
                                    @endif

                                    @if ($supplier->pivot->lead_time)
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-600 mb-1">مدة التوريد</div>
                                            <div class="text-sm font-bold text-gray-900">
                                                {{ $supplier->pivot->lead_time }} يوم</div>
                                        </div>
                                    @else
                                        <div class="text-center p-2 bg-gray-50 rounded-lg">
                                            <div class="text-xs text-gray-600 mb-1">مدة التوريد</div>
                                            <div class="text-sm text-gray-400">-</div>
                                        </div>
                                    @endif
                                </div>

                                @if ($supplier->pivot->warranty)
                                    <div class="mb-4 text-center p-2 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="text-xs text-blue-700 mb-1">الضمان</div>
                                        <div class="text-sm font-semibold text-blue-900">
                                            {{ $supplier->pivot->warranty }}</div>
                                    </div>
                                @endif

                                @if ($supplier->pivot->notes)
                                    <div class="mb-4 p-2 bg-gray-50 rounded-lg">
                                        <div class="text-xs text-gray-600 mb-1">ملاحظات</div>
                                        <div class="text-sm text-gray-700">
                                            {{ Str::limit($supplier->pivot->notes, 80) }}</div>
                                    </div>
                                @endif

                                {{-- Value Score (Phase 2) --}}
                                @if (isset($supplier->value_score))
                                    <div class="mb-4 p-2 bg-blue-50 rounded-lg border border-blue-200">
                                        <div class="text-xs text-blue-700 mb-1">نقاط القيمة</div>
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-medical-blue-500 to-medical-green-500 h-2 rounded-full"
                                                    style="width: {{ min(100, $supplier->value_score / 10) }}%">
                                                </div>
                                            </div>
                                            <span
                                                class="text-xs font-bold text-blue-900">{{ number_format($supplier->value_score, 0) }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Actions --}}
                                <div class="flex gap-2">
                                    <a href="{{ route('buyer.suppliers.show', $supplier) }}"
                                        class="flex-1 text-center px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all text-sm font-semibold">
                                        عرض الملف
                                    </a>
                                    <form action="{{ route('buyer.cart.add', $product) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                                        <button type="submit"
                                            class="w-full px-4 py-2 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-green-700 transition-all text-sm font-semibold shadow-sm hover:shadow-md flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                                </path>
                                            </svg>
                                            أضف إلى السلة
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        {{-- Related Products --}}
        @if ($relatedProducts->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z">
                        </path>
                    </svg>
                    منتجات ذات صلة
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach ($relatedProducts as $related)
                        <a href="{{ route('buyer.products.show', $related) }}"
                            class="bg-white rounded-xl shadow-sm border-2 border-gray-200 overflow-hidden hover:shadow-lg hover:border-medical-blue-300 transition-all duration-200 group">
                            <div class="aspect-square bg-gray-100 relative overflow-hidden">
                                @if ($related->hasMedia('product_images'))
                                    @php
                                        $relatedMedia = $related->getFirstMedia('product_images');
                                        $relatedImageUrl = $relatedMedia
                                            ? ($relatedMedia->getUrl('thumb') ?:
                                            $relatedMedia->getUrl())
                                            : null;
                                    @endphp
                                    @if ($relatedImageUrl)
                                        <img src="{{ $relatedImageUrl }}" alt="{{ $related->name }}"
                                            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-300">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-300">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3
                                    class="font-semibold text-gray-900 text-sm line-clamp-2 group-hover:text-medical-blue-600 transition-colors">
                                    {{ $related->name }}</h3>
                                @if ($related->suppliers->count() > 0)
                                    @php $minPrice = $related->suppliers->pluck('pivot.price')->filter()->min(); @endphp
                                    @if ($minPrice)
                                        <p class="text-xs text-medical-blue-600 font-bold mt-1">
                                            {{ number_format($minPrice, 0) }} د.ل</p>
                                    @endif
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-dashboard.layout>
