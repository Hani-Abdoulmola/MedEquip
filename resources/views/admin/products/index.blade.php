{{-- Admin Products Management - View/Edit/Delete Only --}}
<x-dashboard.layout title="إدارة المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة المنتجات</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراجعة جميع المنتجات المضافة من الموردين</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total_products'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات نشطة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ $stats['active_products'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">منتجات غير نشطة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-2">
                        {{ $stats['inactive_products'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الفئات</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                        {{ $stats['total_categories'] ?? 0 }}
                    </p>
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

                <div>
                    <label class="text-sm font-medium text-medical-gray-700">البحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="الاسم، الموديل، العلامة"
                        class="w-full px-4 py-2.5 border rounded-xl">
                </div>

                <div>
                    <label class="text-sm font-medium text-medical-gray-700">المورد</label>
                    <select name="supplier" class="w-full px-4 py-2.5 border rounded-xl">
                        <option value="">جميع الموردين</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-medical-gray-700">الفئة</label>
                    <select name="category" class="w-full px-4 py-2.5 border rounded-xl">
                        <option value="">جميع الفئات</option>
                        @foreach ($categories as $id => $name)
                            <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-medical-gray-700">الحالة</label>
                    <select name="status" class="w-full px-4 py-2.5 border rounded-xl">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                        </option>
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-medical-gray-700">حالة المراجعة</label>
                    <select name="review_status" class="w-full px-4 py-2.5 border rounded-xl">
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

            <div class="mt-4 flex justify-end">
                <button class="px-6 py-2.5 bg-medical-blue-500 text-white rounded-xl">تطبيق الفلاتر</button>
            </div>
        </form>
    </div>

    {{-- Products Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold">المنتج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">العلامة التجارية</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">الفئة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">عدد الموردين</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">حالة المراجعة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y bg-white">

                    @forelse($products as $product)
                        <tr class="hover:bg-medical-gray-50">

                            {{-- Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div
                                        class="w-12 h-12 bg-medical-blue-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-6 h-6 text-medical-blue-600" fill="none"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                        </svg>
                                    </div>

                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $product->name }}</p>
                                        @if ($product->model)
                                            <p class="text-sm text-medical-gray-500">موديل: {{ $product->model }}</p>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- Brand --}}
                            <td class="px-6 py-4">
                                <span>{{ $product->brand ?? '-' }}</span>
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

                            {{-- Suppliers Count --}}
                            <td class="px-6 py-4">
                                {{ $product->suppliers->count() }} مورد
                            </td>

                            {{-- Review Status --}}
                            {{-- <td class="px-6 py-4">
                                @include('admin.products.partials.review-badge', [
                                    'status' => $product->review_status,
                                ])
                            </td> --}}

                            {{-- Active / Inactive --}}
                            <td class="px-6 py-4">
                                @if ($product->is_active)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">نشط</span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-red-100 text-medical-red-700 rounded-full">غير
                                        نشط</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">

                                    {{-- Review Button (pending only) --}}
                                    @if ($product->review_status === 'pending')
                                        <a href="{{ route('admin.products.review', $product->id) }}"
                                            class="p-2 text-medical-purple-600 hover:bg-medical-purple-50 rounded-lg">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 20l9-4-9-4-9 4 9 4zm0-8l9-4-9-4-9 4 9 4z" />
                                            </svg>
                                        </a>
                                    @endif

                                    {{-- Show --}}
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7" />
                                        </svg>
                                    </a>

                                    {{-- Delete (Not allowed when pending) --}}
                                    @if ($product->review_status !== 'pending')
                                        <form action="{{ route('admin.products.destroy', $product->id) }}"
                                            method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862A2 2 0 015.867 7M10 11v6m4-6v6m1-10V4H9v3" />
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
                                <div class="flex flex-col items-center">
                                    <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                                    </svg>
                                    <p class="text-medical-gray-600 text-lg font-semibold">لا توجد منتجات</p>
                                </div>
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
