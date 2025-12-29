{{-- Admin Products Catalog - Professional View --}}
<x-dashboard.layout title="كتالوج المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">كتالوج المنتجات</h1>
            <p class="mt-2 text-medical-gray-600">عرض ومراجعة جميع المنتجات المضافة من الموردين</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        {{-- Total Products --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات نشطة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $stats['active_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Inactive --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات غير نشطة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-2">{{ $stats['inactive_products'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الفئات</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">{{ $stats['total_categories'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">البحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="الاسم، الموديل، العلامة التجارية"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">المورد</label>
                    <select name="supplier" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">جميع الموردين</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">الفئة</label>
                    <select name="category" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">جميع الفئات</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Manufacturer --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">المُصنّع</label>
                    <select name="manufacturer" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">جميع المصنّعين</option>
                        @foreach ($manufacturers as $id => $name)
                            <option value="{{ $id }}"
                                {{ request('manufacturer') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">حالة النشاط</label>
                    <select name="status" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                        </option>
                    </select>
                </div>

                {{-- Review Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">حالة المراجعة</label>
                    <select name="review_status" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">كل الحالات</option>
                        <option value="pending" {{ request('review_status') == 'pending' ? 'selected' : '' }}>قيد
                            المراجعة</option>
                        <option value="approved" {{ request('review_status') == 'approved' ? 'selected' : '' }}>معتمد
                        </option>
                        <option value="needs_update"
                            {{ request('review_status') == 'needs_update' ? 'selected' : '' }}>يحتاج تعديل</option>
                        <option value="rejected" {{ request('review_status') == 'rejected' ? 'selected' : '' }}>مرفوض
                        </option>
                    </select>
                </div>

            </div>

            <div class="mt-4 flex items-center gap-3 justify-end">
                <a href="{{ route('admin.products.index') }}" 
                    class="px-6 py-2.5 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                    إعادة تعيين
                </a>
                <button type="submit" class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition">
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المنتج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المصنّع</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الفئة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">عدد الموردين</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">حالة المراجعة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y bg-white">

                    @forelse($products as $product)
                        <tr class="hover:bg-medical-gray-50">

                            {{-- Product Name + Image --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div
                                        class="w-12 h-12 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                                        @if ($product->hasMedia('product_images'))
                                            <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                                class="w-full h-full object-cover" alt="{{ $product->name }}">
                                        @else
                                            <svg class="w-6 h-6 text-medical-gray-500" fill="none"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                            </svg>
                                        @endif
                                    </div>

                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                        @if ($product->model)
                                            <p class="text-sm text-medical-gray-500">موديل: {{ $product->model }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Manufacturer --}}
                            <td class="px-6 py-4 text-medical-gray-900">
                                @if($product->manufacturer)
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-medical-purple-100 text-medical-purple-800">
                                            {{ $product->manufacturer->name }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-medical-gray-400 text-sm">غير محدد</span>
                                @endif
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4">
                                @if ($product->category)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">
                                        {{ $product->category->name }}
                                    </span>
                                @else
                                    <span class="text-medical-gray-400 text-sm">غير محدد</span>
                                @endif
                            </td>

                            {{-- Supplier Count --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    <span class="text-medical-gray-900 font-medium">{{ $product->suppliers->count() }}</span>
                                    <span class="text-medical-gray-500 text-sm mr-1">مورد</span>
                                </div>
                            </td>

                            {{-- Review Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $badgeColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'needs_update' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span
                                    class="px-3 py-1 text-xs rounded-full {{ $badgeColors[$product->review_status] }}">
                                    {{ [
                                        'pending' => 'قيد المراجعة',
                                        'approved' => 'معتمد',
                                        'needs_update' => 'يحتاج تعديل',
                                        'rejected' => 'مرفوض',
                                    ][$product->review_status] }}
                                </span>
                            </td>

                            {{-- Active Status --}}
                            <td class="px-6 py-4">
                                @if ($product->is_active)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">
                                        نشط
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-red-100 text-medical-red-700 rounded-full">
                                        غير نشط
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">

                                    {{-- Review --}}
                                    @if ($product->review_status === 'pending')
                                        <a href="{{ route('admin.products.review', $product->id) }}"
                                            class="p-2 text-medical-purple-600 hover:bg-medical-purple-50 rounded-lg transition-colors duration-150"
                                            title="مراجعة المنتج">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Show --}}
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-150"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    @if ($product->review_status !== 'pending')
                                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                                            method="POST" 
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟\nسيتم حذفه من جميع الموردين.');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-150"
                                                title="حذف المنتج">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <p class="text-medical-gray-600 text-lg font-semibold">لا توجد منتجات</p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($products->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $products->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>
