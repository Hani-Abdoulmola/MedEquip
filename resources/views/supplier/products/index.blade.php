{{-- Supplier Products Management - Index --}}
<x-dashboard.layout title="منتجاتي" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">منتجاتي</h1>
                <p class="mt-2 text-medical-gray-600">إدارة منتجاتك وعروضك</p>
            </div>
            <a href="{{ route('supplier.products.create') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>إضافة منتج جديد</span>
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Cards (حسب حالة المراجعة) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        {{-- Total Products --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">
                        {{ $stats['total'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Pending --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-2">
                        {{ $stats['pending'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات معتمدة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ $stats['approved'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Needs Update / Rejected --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">تحتاج تعديل / مرفوضة</p>
                    <p class="text-3xl font-bold text-medical-purple-600 mt-2">
                        {{ ($stats['needs_update'] ?? 0) + ($stats['rejected'] ?? 0) }}
                    </p>
                    <p class="text-xs text-medical-gray-500 mt-1">
                        تحتاج تعديل: <span
                            class="font-semibold text-medical-blue-600">{{ $stats['needs_update'] ?? 0 }}</span> /
                        مرفوضة: <span class="font-semibold text-medical-red-600">{{ $stats['rejected'] ?? 0 }}</span>
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 5.636a9 9 0 11-12.728 0M9 11l6 6M15 11l-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" action="{{ route('supplier.products.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                    <input name="search" type="text" value="{{ request('search') }}"
                        placeholder="اسم المنتج، الموديل، أو العلامة التجارية..."
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الفئة</label>
                    <select name="category"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع الفئات</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status (Pivot: available / out_of_stock / suspended) --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">حالة التوفر</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>متوفر
                        </option>
                        <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>
                            نفد من المخزون
                        </option>
                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>معلق
                        </option>
                    </select>
                </div>

                {{-- Review Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">حالة المراجعة</label>
                    <select name="review_status"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">

                        <option value="">جميع الحالات</option>

                        <option value="pending" {{ request('review_status') == 'pending' ? 'selected' : '' }}>
                            قيد المراجعة
                        </option>

                        <option value="approved" {{ request('review_status') == 'approved' ? 'selected' : '' }}>
                            معتمد
                        </option>

                        <option value="needs_update"
                            {{ request('review_status') == 'needs_update' ? 'selected' : '' }}>
                            يحتاج تعديل
                        </option>

                        <option value="rejected" {{ request('review_status') == 'rejected' ? 'selected' : '' }}>
                            مرفوض
                        </option>
                    </select>
                </div>

                {{-- Date From --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button
                    class="px-6 py-2.5 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors font-medium">
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b-2 border-medical-gray-200">
                    <tr>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            المنتج</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            السعر</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            المخزون</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            مدة التوصيل</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            حالة التوفر</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            حالة المراجعة</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200 bg-white">
                    @forelse($products as $product)
                        <tr class="hover:bg-medical-gray-50 transition-colors duration-200">
                            {{-- Product Name & Model --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div
                                        class="w-12 h-12 bg-medical-blue-100 rounded-lg flex items-center justify-center overflow-hidden">
                                        @if ($product->hasMedia('product_images'))
                                            <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                                alt="{{ $product->name }}"
                                                class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <svg class="w-6 h-6 text-medical-blue-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                        @if ($product->model)
                                            <p class="text-sm text-medical-gray-500">موديل: {{ $product->model }}</p>
                                        @endif
                                        @if ($product->category)
                                            <span
                                                class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700 mt-1"
                                                title="{{ $product->category->full_path }}">
                                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01"></path>
                                                </svg>
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-900 font-bold text-lg">
                                    {{ number_format($product->pivot->price, 2) }} د.ل
                                </span>
                            </td>

                            {{-- Stock --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-700 font-semibold">
                                    {{ $product->pivot->stock_quantity }}
                                </span>
                            </td>

                            {{-- Lead Time --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-600">
                                    {{ $product->pivot->lead_time ?? '-' }}
                                </span>
                            </td>

                            {{-- Availability Status (Pivot) --}}
                            <td class="px-6 py-4">
                                @if ($product->pivot->status == 'available')
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-green-100 text-medical-green-700">
                                        <span class="w-2 h-2 bg-medical-green-600 rounded-full mr-2"></span>
                                        متوفر
                                    </span>
                                @elseif($product->pivot->status == 'out_of_stock')
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-red-100 text-medical-red-700">
                                        <span class="w-2 h-2 bg-medical-red-600 rounded-full mr-2"></span>
                                        نفد من المخزون
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-yellow-100 text-medical-yellow-700">
                                        <span class="w-2 h-2 bg-medical-yellow-600 rounded-full mr-2"></span>
                                        معلق
                                    </span>
                                @endif
                            </td>

                            {{-- Review Status (Product) --}}
                            <td class="px-6 py-4">
                                @switch($product->review_status)
                                    @case('pending')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-yellow-100 text-medical-yellow-700">
                                            <span class="w-2 h-2 bg-medical-yellow-600 rounded-full mr-2"></span>
                                            قيد المراجعة
                                        </span>
                                    @break

                                    @case('approved')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-green-100 text-medical-green-700">
                                            <span class="w-2 h-2 bg-medical-green-600 rounded-full mr-2"></span>
                                            معتمد
                                        </span>
                                    @break

                                    @case('needs_update')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-blue-100 text-medical-blue-700">
                                            <span class="w-2 h-2 bg-medical-blue-600 rounded-full mr-2"></span>
                                            يحتاج تعديل
                                        </span>
                                    @break

                                    @case('rejected')
                                        <span
                                            class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-red-100 text-medical-red-700">
                                            <span class="w-2 h-2 bg-medical-red-600 rounded-full mr-2"></span>
                                            مرفوض
                                        </span>
                                    @break

                                    @default
                                        <span class="text-xs text-medical-gray-500">غير محدد</span>
                                @endswitch
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">
                                    <a href="{{ route('supplier.products.show', $product->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-all"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('supplier.products.edit', $product->id) }}"
                                        class="p-2 text-medical-yellow-600 hover:bg-medical-yellow-50 rounded-lg transition-all"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('supplier.products.destroy', $product->id) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('هل أنت متأكد من إزالة هذا المنتج من قائمتك؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-all"
                                            title="إزالة">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p class="text-medical-gray-600 text-lg font-semibold">لا توجد منتجات</p>
                                        <p class="text-medical-gray-500 text-sm mt-1">ابدأ بإضافة منتج جديد إلى قائمتك</p>
                                        <a href="{{ route('supplier.products.create') }}"
                                            class="mt-4 px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors font-medium">
                                            إضافة منتج جديد
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-medical-gray-200 bg-white">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </x-dashboard.layout>
