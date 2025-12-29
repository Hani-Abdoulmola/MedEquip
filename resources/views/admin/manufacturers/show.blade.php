{{-- Admin Manufacturer Management - Show Manufacturer Details --}}
<x-dashboard.layout title="تفاصيل الشركة المصنعة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الشركة المصنعة</h1>
                <p class="mt-2 text-medical-gray-600">معلومات كاملة عن الشركة المصنعة</p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.manufacturers.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    العودة للقائمة
                </a>

                <a href="{{ route('admin.manufacturers.edit', $manufacturer->id) }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition shadow-lg shadow-medical-blue-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    تعديل الشركة
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- LEFT COLUMN: Basic Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                {{-- Manufacturer Name --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">اسم الشركة المصنعة</p>
                    <h2 class="text-2xl font-bold text-medical-gray-900">{{ $manufacturer->name }}</h2>
                    @if ($manufacturer->name_ar)
                        <p class="text-lg text-medical-gray-600 mt-1">{{ $manufacturer->name_ar }}</p>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">الحالة</p>
                    @if ($manufacturer->is_active)
                        <span class="inline-flex items-center px-3 py-1 bg-medical-green-100 text-medical-green-700 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            نشط
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 bg-medical-red-100 text-medical-red-700 rounded-lg text-sm font-semibold">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            غير نشط
                        </span>
                    @endif
                </div>

                {{-- Category --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">الفئة</p>
                    @if ($manufacturer->category)
                        <a href="{{ route('admin.categories.show', $manufacturer->category->id) }}"
                            class="inline-flex items-center px-3 py-1 bg-medical-purple-100 text-medical-purple-700 rounded-lg text-sm font-semibold hover:bg-medical-purple-200 transition">
                            {{ $manufacturer->category->name }}
                        </a>
                    @else
                        <span class="text-medical-gray-500">غير محدد</span>
                    @endif
                </div>

                {{-- Country --}}
                @if ($manufacturer->country)
                    <div class="mb-6">
                        <p class="text-sm text-medical-gray-600 mb-2">الدولة</p>
                        <p class="text-medical-gray-900 font-medium">{{ $manufacturer->country }}</p>
                    </div>
                @endif

                {{-- Website --}}
                @if ($manufacturer->website)
                    <div class="mb-6">
                        <p class="text-sm text-medical-gray-600 mb-2">الموقع الإلكتروني</p>
                        <a href="{{ $manufacturer->website }}" target="_blank"
                            class="text-medical-blue-600 hover:text-medical-blue-700 font-medium flex items-center">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                            </svg>
                            {{ $manufacturer->website }}
                        </a>
                    </div>
                @endif

                {{-- Slug --}}
                <div class="mb-6">
                    <p class="text-sm text-medical-gray-600 mb-2">الرابط المخصص</p>
                    <p class="text-sm text-medical-gray-900 font-mono">{{ $manufacturer->slug }}</p>
                </div>
            </div>

            {{-- RIGHT COLUMN: Products --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Products List --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900">المنتجات</h3>
                        <span class="px-3 py-1 bg-medical-blue-100 text-medical-blue-700 rounded-full text-sm font-semibold">
                            {{ $manufacturer->products->count() }} منتج
                        </span>
                    </div>

                    @if ($manufacturer->products->isEmpty())
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-medical-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <p class="text-medical-gray-600 text-lg font-semibold">لا توجد منتجات</p>
                            <p class="text-medical-gray-500 text-sm mt-1">لم يتم إضافة أي منتجات لهذه الشركة المصنعة بعد</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-medical-gray-50 border-b">
                                    <tr>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-700 uppercase">المنتج</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-700 uppercase">الفئة</th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-700 uppercase">الحالة</th>
                                        <th class="px-4 py-3 text-center text-xs font-semibold text-medical-gray-700 uppercase">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach ($manufacturer->products->take(10) as $product)
                                        <tr class="hover:bg-medical-gray-50">
                                            <td class="px-4 py-3">
                                                <p class="font-medium text-medical-gray-900">{{ $product->name }}</p>
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($product->category)
                                                    <span class="text-sm text-medical-gray-600">{{ $product->category->name }}</span>
                                                @else
                                                    <span class="text-medical-gray-400">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3">
                                                @if ($product->is_active)
                                                    <span class="px-2 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">نشط</span>
                                                @else
                                                    <span class="px-2 py-1 text-xs bg-medical-red-100 text-medical-red-700 rounded-full">غير نشط</span>
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="text-medical-blue-600 hover:text-medical-blue-700">
                                                    <svg class="w-5 h-5 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($manufacturer->products->count() > 10)
                            <div class="mt-4 text-center">
                                <a href="{{ route('admin.products.index', ['manufacturer_id' => $manufacturer->id]) }}"
                                    class="text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                    عرض جميع المنتجات ({{ $manufacturer->products->count() }})
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

