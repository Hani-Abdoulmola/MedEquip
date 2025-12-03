{{-- Supplier Products - Show --}}
<x-dashboard.layout title="تفاصيل المنتج" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    <div class="max-w-7xl mx-auto px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل المنتج</h1>
                    <p class="mt-2 text-medical-gray-600">معلومات كاملة عن المنتج وعرضك</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('supplier.products.edit', $product->id) }}"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>تعديل</span>
                    </a>
                    <a href="{{ route('supplier.products.index') }}"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-semibold">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>العودة للقائمة</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Product Image and Basic Info --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    {{-- Product Image --}}
                    <div
                        class="w-full h-64 bg-medical-gray-100 rounded-xl flex items-center justify-center mb-6 overflow-hidden">
                        @if ($product->hasMedia('product_images'))
                            <img src="{{ $product->getFirstMediaUrl('product_images') }}" alt="{{ $product->name }}"
                                class="w-full h-full object-cover">
                        @else
                            <svg class="w-24 h-24 text-medical-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-medical-gray-600">رقم المنتج</p>
                            <p class="font-medium text-medical-gray-900 mt-1">#{{ $product->id }}</p>
                        </div>
                        @if ($product->model)
                            <div>
                                <p class="text-sm text-medical-gray-600">الموديل</p>
                                <p class="font-medium text-medical-gray-900 mt-1">{{ $product->model }}</p>
                            </div>
                        @endif
                        @if ($product->brand)
                            <div>
                                <p class="text-sm text-medical-gray-600">العلامة التجارية</p>
                                <p class="font-medium text-medical-gray-900 mt-1">{{ $product->brand }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-medical-gray-600">الفئة</p>
                            @if ($product->category)
                                <span
                                    class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700 mt-1">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-medical-gray-500 text-sm mt-1">غير محدد</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Product Details and Supplier Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">المعلومات الأساسية</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-medical-gray-600 font-semibold">اسم المنتج</p>
                            <p class="font-medium text-medical-gray-900 mt-1 text-lg">{{ $product->name }}</p>
                        </div>
                        @if ($product->description)
                            <div>
                                <p class="text-sm text-medical-gray-600 font-semibold">الوصف</p>
                                <p class="text-medical-gray-900 mt-1">{{ $product->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Supplier Offer Information --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">معلومات عرضك</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-medical-blue-50 rounded-xl">
                            <p class="text-sm text-medical-gray-600 font-semibold">السعر</p>
                            <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                                {{ number_format($pivotData->price, 2) }} د.ل
                            </p>
                        </div>
                        <div class="p-4 bg-medical-green-50 rounded-xl">
                            <p class="text-sm text-medical-gray-600 font-semibold">الكمية المتوفرة</p>
                            <p class="text-3xl font-bold text-medical-green-600 mt-2">
                                {{ $pivotData->stock_quantity }}
                            </p>
                        </div>
                        @if ($pivotData->lead_time)
                            <div class="p-4 bg-medical-yellow-50 rounded-xl">
                                <p class="text-sm text-medical-gray-600 font-semibold">مدة التوصيل</p>
                                <p class="text-xl font-bold text-medical-yellow-600 mt-2">
                                    {{ $pivotData->lead_time }}
                                </p>
                            </div>
                        @endif
                        @if ($pivotData->warranty)
                            <div class="p-4 bg-medical-purple-50 rounded-xl">
                                <p class="text-sm text-medical-gray-600 font-semibold">الضمان</p>
                                <p class="text-xl font-bold text-medical-purple-600 mt-2">
                                    {{ $pivotData->warranty }}
                                </p>
                            </div>
                        @endif
                        <div class="p-4 bg-medical-gray-50 rounded-xl">
                            <p class="text-sm text-medical-gray-600 font-semibold">الحالة</p>
                            <div class="mt-2">
                                @if ($pivotData->status == 'available')
                                    <span
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-medical-green-100 text-medical-green-700">
                                        <span class="w-2 h-2 bg-medical-green-600 rounded-full mr-2"></span>
                                        متوفر
                                    </span>
                                @elseif($pivotData->status == 'out_of_stock')
                                    <span
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-medical-red-100 text-medical-red-700">
                                        <span class="w-2 h-2 bg-medical-red-600 rounded-full mr-2"></span>
                                        نفد من المخزون
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-4 py-2 text-sm font-semibold rounded-full bg-medical-yellow-100 text-medical-yellow-700">
                                        <span class="w-2 h-2 bg-medical-yellow-600 rounded-full mr-2"></span>
                                        معلق
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @if ($pivotData->notes)
                        <div class="mt-6 pt-6 border-t border-medical-gray-200">
                            <p class="text-sm text-medical-gray-600 font-semibold mb-2">ملاحظات</p>
                            <p class="text-medical-gray-900">{{ $pivotData->notes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Product Documents --}}
                @if ($product->hasMedia('product_documents'))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">المستندات</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($product->getMedia('product_documents') as $document)
                                <a href="{{ $document->getUrl() }}" target="_blank"
                                    class="flex items-center space-x-3 space-x-reverse p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition-colors duration-200">
                                    <svg class="w-8 h-8 text-medical-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div class="flex-1">
                                        <p class="font-medium text-medical-gray-900">{{ $document->file_name }}</p>
                                        <p class="text-sm text-medical-gray-600">{{ $document->human_readable_size }}
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-dashboard.layout>

