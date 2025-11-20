{{-- Admin Products Management - View/Edit/Delete Only --}}
<x-dashboard.layout title="إدارة المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto px-6 py-8" x-data="{ showFilters: false }">

        {{-- Page Header with Filter Toggle --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة المنتجات</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراجعة جميع المنتجات المضافة من الموردين</p>
            </div>
            <button @click="showFilters = !showFilters"
                class="px-6 py-3 border-2 border-medical-blue-600 text-medical-blue-600 rounded-xl hover:bg-medical-blue-50 transition-all duration-200 font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>الفلاتر والبحث</span>
            </button>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-medical-blue-700">إجمالي المنتجات</p>
                        <p class="text-3xl font-bold text-medical-blue-900 mt-2">
                            {{ number_format($stats['total_products']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-medical-blue-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-medical-green-700">منتجات نشطة</p>
                        <p class="text-3xl font-bold text-medical-green-900 mt-2">
                            {{ number_format($stats['active_products']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-medical-green-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-medical-red-100 to-medical-red-200 rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-medical-red-700">منتجات غير نشطة</p>
                        <p class="text-3xl font-bold text-medical-red-900 mt-2">
                            {{ number_format($stats['inactive_products']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-medical-red-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-medical-yellow-100 to-medical-yellow-200 rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-medical-yellow-700">الفئات</p>
                        <p class="text-3xl font-bold text-medical-yellow-900 mt-2">
                            {{ number_format($stats['total_categories']) }}</p>
                    </div>
                    <div class="w-14 h-14 bg-white/50 rounded-xl flex items-center justify-center">
                        <svg class="w-7 h-7 text-medical-yellow-700" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Collapsible Filters --}}
        <div x-show="showFilters" x-transition class="bg-white rounded-2xl p-6 shadow-medical mb-6">
            <form method="GET" action="{{ route('admin.products') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Search --}}
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">البحث</label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="اسم المنتج، الموديل، أو العلامة التجارية..."
                            class="w-full px-4 py-2.5 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                    </div>

                    {{-- Supplier Filter --}}
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">المورد</label>
                        <select name="supplier"
                            class="w-full px-4 py-2.5 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع الموردين</option>
                            @foreach ($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Category Filter --}}
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">الفئة</label>
                        <select name="category"
                            class="w-full px-4 py-2.5 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع الفئات</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">الحالة</label>
                        <select name="status"
                            class="w-full px-4 py-2.5 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                            </option>
                        </select>
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.products') }}"
                        class="px-6 py-2.5 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        {{-- Products Table --}}
        <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المنتج</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">العلامة
                                التجارية
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الفئة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">عدد الموردين
                            </th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الحالة</th>
                            <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-medical-gray-200">
                        @forelse($products as $product)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                {{-- Product Name & Model --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3 space-x-reverse">
                                        <div
                                            class="w-12 h-12 bg-medical-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-6 h-6 text-medical-blue-600" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-medical-gray-900">{{ $product->name }}</p>
                                            @if ($product->model)
                                                <p class="text-sm text-medical-gray-600">موديل: {{ $product->model }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Brand --}}
                                <td class="px-6 py-4">
                                    <span class="text-medical-gray-900">{{ $product->brand ?? '-' }}</span>
                                </td>

                                {{-- Category --}}
                                <td class="px-6 py-4">
                                    @if ($product->category)
                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="text-medical-gray-500 text-sm">غير محدد</span>
                                    @endif
                                </td>

                                {{-- Suppliers Count --}}
                                <td class="px-6 py-4">
                                    <span class="text-medical-gray-900 font-semibold">
                                        {{ $product->suppliers->count() }} مورد
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    @if ($product->is_active)
                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-green-100 text-medical-green-700">
                                            نشط
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-red-100 text-medical-red-700">
                                            غير نشط
                                        </span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2 space-x-reverse">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                            class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-200"
                                            title="عرض التفاصيل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}"
                                            class="p-2 text-medical-yellow-600 hover:bg-medical-yellow-50 rounded-lg transition-colors duration-200"
                                            title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                                            method="POST" class="inline"
                                            onsubmit="return confirm('هل أنت متأكد من حذف هذا المنتج؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-200"
                                                title="حذف">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <p class="text-medical-gray-600 text-lg font-medium">لا توجد منتجات</p>
                                        <p class="text-medical-gray-500 text-sm mt-1">لم يتم العثور على أي منتجات
                                            مطابقة
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($products->hasPages())
                <div class="px-6 py-4 border-t border-medical-gray-200">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>

</x-dashboard.layout>
