{{-- Admin Category Management - Show Category Details --}}
<x-dashboard.layout title="تفاصيل الفئة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto">

        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الفئة</h1>
                <p class="mt-2 text-medical-gray-600">معلومات كاملة عن الفئة</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.categories.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للقائمة
                </a>

                <a href="{{ route('admin.categories.edit', $category->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition shadow-lg shadow-medical-blue-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل الفئة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT COLUMN: Basic Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">

                {{-- Category Name --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">اسم الفئة</p>
                    <h2 class="text-2xl font-bold text-medical-gray-900">{{ $category->name }}</h2>
                    @if ($category->name_ar)
                        <p class="text-lg text-medical-gray-600 mt-1">{{ $category->name_ar }}</p>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">الحالة</p>
                    @if ($category->is_active)
                        <span
                            class="inline-flex items-center px-3 py-1 bg-medical-green-100 text-medical-green-700 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            نشط
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 bg-medical-red-100 text-medical-red-700 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            غير نشط
                        </span>
                    @endif
                </div>

                {{-- Parent Category --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">الفئة الأب</p>
                    @if ($category->parent)
                        <a href="{{ route('admin.categories.show', $category->parent->id) }}"
                            class="inline-flex items-center px-3 py-1 bg-medical-purple-100 text-medical-purple-700 rounded-lg text-sm font-semibold hover:bg-medical-purple-200 transition">
                            {{ $category->parent->name }}
                        </a>
                    @else
                        <span class="text-medical-gray-500">فئة رئيسية</span>
                    @endif
                </div>

                {{-- Sort Order --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">ترتيب العرض</p>
                    <span
                        class="inline-flex items-center px-3 py-1 bg-medical-gray-100 text-medical-gray-700 rounded-lg text-sm font-semibold">
                        {{ $category->sort_order }}
                    </span>
                </div>

                {{-- Full Path --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">المسار الكامل</p>
                    <p class="text-sm text-medical-gray-900 font-medium">{{ $category->full_path }}</p>
                </div>

                {{-- Meta --}}
                <div class="pt-6 border-t border-medical-gray-200 space-y-2 text-sm">
                    @if ($category->creator)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">أضيف بواسطة:</span>
                            <span class="font-medium">{{ $category->creator->name }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span class="text-medical-gray-600">تاريخ الإضافة:</span>
                        <span class="font-medium">{{ $category->created_at->format('Y-m-d') }}</span>
                    </div>
                    @if ($category->updated_at != $category->created_at)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">آخر تحديث:</span>
                            <span class="font-medium">{{ $category->updated_at->format('Y-m-d') }}</span>
                        </div>
                    @endif
                </div>

            </div>

            {{-- RIGHT COLUMN: Details --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Description --}}
                @if ($category->description)
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display">الوصف</h3>
                        <p class="text-medical-gray-800 leading-relaxed">{{ $category->description }}</p>
                    </div>
                @endif

                {{-- Child Categories --}}
                @if ($category->children->count() > 0)
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display flex items-center">
                            <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                            </svg>
                            الفئات الفرعية ({{ $category->children->count() }})
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach ($category->children as $child)
                                <a href="{{ route('admin.categories.show', $child->id) }}"
                                    class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition group">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-medical-blue-100 rounded-lg flex items-center justify-center ml-3 group-hover:bg-medical-blue-200 transition">
                                            <svg class="w-5 h-5 text-medical-blue-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-medical-gray-900">{{ $child->name }}</p>
                                            <p class="text-xs text-medical-gray-500">{{ $child->products_count }} منتج</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-medical-gray-400 group-hover:text-medical-blue-600 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Products --}}
                @if ($category->products->count() > 0)
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 font-display flex items-center">
                            <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            المنتجات في هذه الفئة ({{ $category->products->count() }})
                        </h3>

                        <div class="space-y-3">
                            @foreach ($category->products->take(10) as $product)
                                <a href="{{ route('admin.products.show', $product->id) }}"
                                    class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition group">
                                    <div class="flex items-center flex-1">
                                        <div
                                            class="w-12 h-12 bg-medical-gray-200 rounded-lg overflow-hidden flex items-center justify-center ml-3">
                                            @if ($product->hasMedia('product_images'))
                                                <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                                    class="w-full h-full object-cover" alt="{{ $product->name }}">
                                            @else
                                                <svg class="w-6 h-6 text-medical-gray-500" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                            @if ($product->model)
                                                <p class="text-xs text-medical-gray-500">موديل: {{ $product->model }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-medical-gray-400 group-hover:text-medical-blue-600 transition"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </a>
                            @endforeach

                            @if ($category->products->count() > 10)
                                <p class="text-sm text-medical-gray-500 text-center py-2">
                                    و {{ $category->products->count() - 10 }} منتج آخر...
                                </p>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="bg-white rounded-2xl shadow-medical p-8 text-center">
                        <svg class="w-16 h-16 text-medical-gray-400 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <p class="text-medical-gray-600 font-semibold">لا توجد منتجات في هذه الفئة</p>
                    </div>
                @endif

            </div>

        </div>

    </div>

</x-dashboard.layout>

