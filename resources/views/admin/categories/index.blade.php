{{-- Admin Category Management --}}
<x-dashboard.layout title="فئات المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">فئات المنتجات</h1>
            <p class="mt-2 text-medical-gray-600">إدارة فئات وتصنيفات المنتجات الطبية</p>
        </div>

        <a href="{{ route('admin.categories.create') }}"
            class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition shadow-lg shadow-medical-blue-200">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            إضافة فئة جديدة
        </a>
    </div>

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 bg-medical-green-50 border-r-4 border-medical-green-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-medical-green-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-medical-green-700 font-medium">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="mb-6 bg-medical-red-50 border-r-4 border-medical-red-500 p-4 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 text-medical-red-500 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-medical-red-700 font-medium">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        {{-- Total Categories --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الفئات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 7h.01M7 3h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 11h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 19h5c1.1046 0 2 .8954 2 2v0" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Active --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">فئات نشطة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $stats['active'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <p class="text-sm text-medical-gray-600">فئات غير نشطة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-2">{{ $stats['inactive'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Root Categories --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">فئات رئيسية</p>
                    <p class="text-3xl font-bold text-medical-purple-600 mt-2">{{ $stats['roots'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">البحث</label>
                    <input name="search" value="{{ request('search') }}" placeholder="اسم الفئة أو الوصف"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                </div>

                {{-- Status Filter --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">الحالة</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">كل الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                        </option>
                    </select>
                </div>

                {{-- Level Filter --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-1">المستوى</label>
                    <select name="level"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="">كل المستويات</option>
                        <option value="root" {{ request('level') == 'root' ? 'selected' : '' }}>فئة رئيسية</option>
                        <option value="sub" {{ request('level') == 'sub' ? 'selected' : '' }}>فئة فرعية</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <a href="{{ route('admin.categories.index') }}"
                        class="flex-1 px-4 py-2.5 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition text-center">
                        إعادة تعيين
                    </a>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition">
                        تطبيق
                    </button>
                </div>

            </div>
        </form>
    </div>

    {{-- Categories Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الفئة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الفئة الأب</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            عدد المنتجات</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            فئات فرعية</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الترتيب</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الحالة</th>
                        <th
                            class="px-6 py-4 text-center text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y bg-white">

                    @forelse($categories as $category)
                        <tr class="hover:bg-medical-gray-50 transition-colors">

                            {{-- Category Name --}}
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $category->name }}</p>
                                    @if ($category->name_ar)
                                        <p class="text-sm text-medical-gray-500 mt-0.5">{{ $category->name_ar }}</p>
                                    @endif
                                    @if ($category->description)
                                        <p class="text-xs text-medical-gray-400 mt-1">{{ Str::limit($category->description, 50) }}</p>
                                    @endif
                                </div>
                            </td>

                            {{-- Parent Category --}}
                            <td class="px-6 py-4">
                                @if ($category->parent)
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-medical-purple-100 text-medical-purple-800">
                                        {{ $category->parent->name }}
                                    </span>
                                @else
                                    <span class="text-medical-gray-400 text-sm">—</span>
                                @endif
                            </td>

                            {{-- Products Count --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <span class="text-medical-gray-900 font-medium">{{ $category->products_count }}</span>
                                </div>
                            </td>

                            {{-- Children Count --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-medical-gray-400 ml-1" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                    </svg>
                                    <span
                                        class="text-medical-gray-900 font-medium">{{ $category->children_count }}</span>
                                </div>
                            </td>

                            {{-- Sort Order --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex items-center px-2 py-1 bg-medical-gray-100 text-medical-gray-700 rounded text-xs font-medium">
                                    {{ $category->sort_order }}
                                </span>
                            </td>

                            {{-- Active Status --}}
                            <td class="px-6 py-4">
                                @if ($category->is_active)
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full font-semibold">
                                        نشط
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 text-xs bg-medical-red-100 text-medical-red-700 rounded-full font-semibold">
                                        غير نشط
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2 space-x-reverse">

                                    {{-- Show --}}
                                    <a href="{{ route('admin.categories.show', $category->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-150"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.categories.edit', $category->id) }}"
                                        class="p-2 text-medical-purple-600 hover:bg-medical-purple-50 rounded-lg transition-colors duration-150"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذه الفئة؟\nلا يمكن حذف الفئات التي تحتوي على منتجات أو فئات فرعية.');"
                                        class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-150"
                                            title="حذف">
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
                                            d="M7 7h.01M7 3h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 11h5c1.1046 0 2 .8954 2 2v0c0 1.1046-.8954 2-2 2H9m-2 4h.01M7 19h5c1.1046 0 2 .8954 2 2v0" />
                                    </svg>
                                    <p class="text-medical-gray-600 text-lg font-semibold">لا توجد فئات</p>
                                    <p class="text-medical-gray-500 text-sm mt-1">ابدأ بإضافة فئة جديدة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($categories->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

