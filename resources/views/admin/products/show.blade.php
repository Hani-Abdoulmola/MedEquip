{{-- Admin Product Catalog - Product Details --}}
<x-dashboard.layout title="تفاصيل المنتج" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل المنتج</h1>
                <p class="mt-2 text-medical-gray-600">معلومات كاملة عن المنتج ومورديه</p>
            </div>

            <div class="flex items-center gap-3">

                {{-- ⇦ Back --}}
                <a href="{{ route('admin.products.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للكتالوج
                </a>

                {{-- Show Review Button ONLY if product is pending --}}
                @if ($product->review_status === 'pending')
                    <a href="{{ route('admin.products.review', $product->id) }}"
                        class="inline-flex items-center px-6 py-3 bg-medical-purple-600 text-white rounded-xl hover:bg-medical-purple-700 transition shadow-lg shadow-medical-purple-200">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                            </path>
                        </svg>
                        مراجعة المنتج
                    </a>
                @endif

            </div>
        </div>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN --}}
            <div class="bg-white rounded-2xl shadow-medical p-6 lg:col-span-1">

                {{-- Product Image Gallery --}}
                <div class="mb-6">
                    @if ($product->hasMedia('product_images'))
                        <div
                            class="w-full h-64 bg-medical-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                            <img src="{{ $product->getFirstMediaUrl('product_images', 'preview') }}"
                                alt="{{ $product->name }}" class="w-full h-full object-contain">
                        </div>

                        {{-- Thumbnail Gallery --}}
                        @if ($product->getMedia('product_images')->count() > 1)
                            <div class="mt-4 grid grid-cols-4 gap-2">
                                @foreach ($product->getMedia('product_images')->take(4) as $image)
                                    <div
                                        class="w-full h-16 bg-medical-gray-100 rounded-lg overflow-hidden cursor-pointer hover:ring-2 hover:ring-medical-blue-500 transition">
                                        <img src="{{ $image->getUrl('thumb') }}" alt="{{ $product->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @else
                        <div class="w-full h-64 bg-medical-gray-100 rounded-xl flex items-center justify-center">
                            <div class="text-center">
                                <svg class="w-24 h-24 text-medical-gray-400 mx-auto" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <p class="mt-2 text-sm text-medical-gray-500">لا توجد صورة</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Basic Info --}}
                <div class="space-y-4">

                    {{-- Name --}}
                    <div>
                        <p class="text-sm text-medical-gray-600">اسم المنتج</p>
                        <p class="font-semibold text-medical-gray-900 text-lg mt-1">{{ $product->name }}</p>
                    </div>

                    {{-- Model --}}
                    @if ($product->model)
                        <div>
                            <p class="text-sm text-medical-gray-600">الموديل</p>
                            <p class="font-medium text-medical-gray-900 mt-1">{{ $product->model }}</p>
                        </div>
                    @endif

                    {{-- Brand --}}
                    @if ($product->brand)
                        <div>
                            <p class="text-sm text-medical-gray-600">العلامة التجارية</p>
                            <p class="font-medium text-medical-gray-900 mt-1">{{ $product->brand }}</p>
                        </div>
                    @endif

                    {{-- Manufacturer --}}
                    <div>
                        <p class="text-sm text-medical-gray-600 mb-2">الشركة المصنّعة</p>

                        @if ($product->manufacturer)
                            <div class="space-y-2">
                                <div
                                    class="inline-flex items-center px-3 py-1.5 bg-medical-purple-100 text-medical-purple-700 rounded-lg text-sm font-semibold">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                    {{ $product->manufacturer->name }}
                                </div>

                                @if ($product->manufacturer->country)
                                    <p class="text-xs text-medical-gray-600 flex items-center">
                                        <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9">
                                            </path>
                                        </svg>
                                        {{ $product->manufacturer->country }}
                                    </p>
                                @endif

                                @if ($product->manufacturer->website)
                                    <p class="text-xs">
                                        <a href="{{ $product->manufacturer->website }}" target="_blank"
                                            class="text-medical-blue-600 hover:text-medical-blue-700 flex items-center">
                                            <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14">
                                                </path>
                                            </svg>
                                            زيارة الموقع
                                        </a>
                                    </p>
                                @endif
                            </div>
                        @else
                            <span class="text-sm text-medical-gray-500">غير محدد</span>
                        @endif
                    </div>

                    {{-- Category --}}
                    <div>
                        <p class="text-sm text-medical-gray-600">الفئة</p>

                        @if ($product->category)
                            <span
                                class="px-3 py-1 rounded-full bg-medical-blue-100 text-medical-blue-700 text-xs font-semibold mt-1">
                                {{ $product->category->name }}
                            </span>
                        @else
                            <span class="text-sm text-medical-gray-500">غير محدد</span>
                        @endif
                    </div>

                    {{-- Review Status --}}
                    <div>
                        <p class="text-sm text-medical-gray-600">حالة المراجعة</p>

                        @php
                            $colors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'approved' => 'bg-green-100 text-green-700',
                                'needs_update' => 'bg-blue-100 text-blue-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp

                        <span
                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$product->review_status] }}">
                            {{ [
                                'pending' => 'قيد المراجعة',
                                'approved' => 'معتمد',
                                'needs_update' => 'يحتاج تعديل',
                                'rejected' => 'مرفوض',
                            ][$product->review_status] }}
                        </span>
                    </div>

                    {{-- Active Status --}}
                    <div>
                        <p class="text-sm text-medical-gray-600">حالة المنتج</p>

                        @if ($product->is_active)
                            <span
                                class="px-3 py-1 bg-medical-green-100 text-medical-green-700 rounded-full text-xs font-semibold mt-1">
                                نشط
                            </span>
                        @else
                            <span
                                class="px-3 py-1 bg-medical-red-100 text-medical-red-700 rounded-full text-xs font-semibold mt-1">
                                غير نشط
                            </span>
                        @endif
                    </div>

                    {{-- Supplier Count --}}
                    <div class="pt-4 border-t border-medical-gray-200">
                        <p class="text-sm text-medical-gray-600 mb-2">عدد الموردين</p>
                        <div class="flex items-center">
                            <div
                                class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <div class="mr-3">
                                <p class="text-3xl font-bold text-medical-blue-600">
                                    {{ $product->suppliers->count() }}
                                </p>
                                <p class="text-sm text-medical-gray-500">مورد نشط</p>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Metadata --}}
                <div class="mt-6 pt-6 border-t border-medical-gray-200 space-y-2 text-sm">

                    @if ($product->creator)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">أضيف بواسطة:</span>
                            <span class="font-medium">{{ $product->creator->name }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between">
                        <span class="text-medical-gray-600">تاريخ الإضافة:</span>
                        <span class="font-medium">{{ $product->created_at->format('Y-m-d') }}</span>
                    </div>

                    @if ($product->updated_at != $product->created_at)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">آخر تحديث:</span>
                            <span class="font-medium">{{ $product->updated_at->format('Y-m-d') }}</span>
                        </div>
                    @endif

                </div>
            </div>

            {{-- RIGHT COLUMN: Detailed Data --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Description --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">الوصف</h3>
                    <p class="text-medical-gray-800 leading-relaxed">
                        {{ $product->description ?: 'لا يوجد وصف' }}
                    </p>
                </div>

                {{-- Specifications --}}
                @if (!empty($product->specifications))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">المواصفات</h3>
                        <ul class="list-disc pr-6 space-y-1 text-medical-gray-800">
                            @foreach ($product->specifications as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Features --}}
                @if (!empty($product->features))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">المميزات</h3>
                        <ul class="list-disc pr-6 space-y-1 text-medical-gray-800">
                            @foreach ($product->features as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Technical Data --}}
                @if (!empty($product->technical_data))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">البيانات التقنية</h3>
                        <ul class="list-disc pr-6 space-y-1 text-medical-gray-800">
                            @foreach ($product->technical_data as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Certifications --}}
                @if (!empty($product->certifications))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">الشهادات والاعتمادات</h3>
                        <ul class="list-disc pr-6 space-y-1 text-medical-gray-800">
                            @foreach ($product->certifications as $item)
                                <li>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Installation Requirements --}}
                @if (!empty($product->installation_requirements))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">متطلبات التركيب</h3>
                        <p class="text-medical-gray-800">{{ $product->installation_requirements }}</p>
                    </div>
                @endif


                {{-- Suppliers List --}}
                @if ($product->suppliers->count() > 0)
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-medical-gray-900 font-display flex items-center">
                                <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                                الموردون المتاحون
                            </h3>
                            <span
                                class="px-3 py-1 bg-medical-blue-100 text-medical-blue-700 rounded-full text-sm font-semibold">
                                {{ $product->suppliers->count() }} مورد
                            </span>
                        </div>

                        <div class="space-y-3">
                            @foreach ($product->suppliers as $supplier)
                                <div
                                    class="flex items-center justify-between bg-gradient-to-r from-medical-gray-50 to-white p-4 rounded-xl border border-medical-gray-100 hover:shadow-md transition-shadow duration-200">

                                    <div class="flex items-center space-x-4 space-x-reverse flex-1">
                                        <div
                                            class="w-14 h-14 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold text-xl shadow-lg">
                                            {{ mb_substr($supplier->company_name, 0, 1) }}
                                        </div>

                                        <div class="flex-1">
                                            <p class="font-semibold text-medical-gray-900 text-lg">
                                                {{ $supplier->company_name }}
                                            </p>

                                            <div class="flex items-center gap-4 mt-2">
                                                {{-- Price --}}
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                        </path>
                                                    </svg>
                                                    <span class="text-sm text-medical-gray-600">
                                                        السعر:
                                                    </span>
                                                    <span class="font-bold text-medical-blue-600 mr-1">
                                                        {{ number_format($supplier->pivot->price, 2) }} د.ل
                                                    </span>
                                                </div>

                                                {{-- Stock --}}
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4">
                                                        </path>
                                                    </svg>
                                                    <span class="text-sm text-medical-gray-600">
                                                        المخزون:
                                                    </span>
                                                    <span class="font-semibold text-medical-gray-900 mr-1">
                                                        {{ $supplier->pivot->stock_quantity }}
                                                    </span>
                                                </div>

                                                {{-- Lead Time --}}
                                                @if ($supplier->pivot->lead_time)
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        <span class="text-sm text-medical-gray-600">
                                                            {{ $supplier->pivot->lead_time }} يوم
                                                        </span>
                                                    </div>
                                                @endif

                                                {{-- Warranty --}}
                                                @if ($supplier->pivot->warranty)
                                                    <div class="flex items-center">
                                                        <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                                                            </path>
                                                        </svg>
                                                        <span class="text-sm text-medical-gray-600">
                                                            ضمان: {{ $supplier->pivot->warranty }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Offer Status --}}
                                    <div class="mr-4">
                                        @if ($supplier->pivot->status === 'available')
                                            <span
                                                class="px-3 py-1.5 text-xs font-semibold bg-medical-green-100 text-medical-green-700 rounded-lg">
                                                متوفر
                                            </span>
                                        @elseif ($supplier->pivot->status === 'out_of_stock')
                                            <span
                                                class="px-3 py-1.5 text-xs font-semibold bg-medical-red-100 text-medical-red-700 rounded-lg">
                                                نفد من المخزون
                                            </span>
                                        @else
                                            <span
                                                class="px-3 py-1.5 text-xs font-semibold bg-medical-yellow-100 text-medical-yellow-700 rounded-lg">
                                                معلق
                                            </span>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-medical p-8">
                        <div class="text-center">
                            <svg class="w-16 h-16 text-medical-gray-400 mx-auto mb-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                </path>
                            </svg>
                            <h3 class="text-lg font-semibold text-medical-gray-900 mb-2">لا يوجد موردون</h3>
                            <p class="text-medical-gray-600">لم يتم إضافة أي موردين لهذا المنتج بعد</p>
                        </div>
                    </div>
                @endif


                {{-- Documents --}}
                @if ($product->hasMedia('product_documents'))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">المستندات</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($product->getMedia('product_documents') as $document)
                                <a href="{{ $document->getUrl() }}" target="_blank"
                                    class="flex items-center p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition">

                                    <svg class="w-8 h-8 text-medical-red-600" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 00112.586 3H7a2 2 0 00-2 2v14a2
                                          2 0 002 2z" />
                                    </svg>

                                    <div class="mr-3 flex-1">
                                        <p class="font-medium text-medical-gray-900">{{ $document->file_name }}</p>
                                        <p class="text-sm text-medical-gray-600">{{ $document->human_readable_size }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>

</x-dashboard.layout>
