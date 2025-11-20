{{-- Admin Orders Management - List All Orders --}}
<x-dashboard.layout title="إدارة الطلبات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة الطلبات</h1>
            <p class="mt-2 text-medical-gray-600">عرض ومراقبة جميع الطلبات في النظام</p>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div
            class="bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl p-6 shadow-medical text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-blue-100">إجمالي الطلبات</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['total_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-medical-yellow-500 to-medical-yellow-600 rounded-2xl p-6 shadow-medical text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-yellow-100">قيد الانتظار</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['pending_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-medical-blue-400 to-medical-blue-500 rounded-2xl p-6 shadow-medical text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-blue-100">قيد المعالجة</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['processing_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl p-6 shadow-medical text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-green-100">تم التسليم</p>
                    <p class="text-3xl font-bold mt-2">{{ number_format($stats['delivered_orders']) }}</p>
                </div>
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Toggle Button --}}
    <div class="mb-4" x-data="{ showFilters: false }">
        <button @click="showFilters = !showFilters" type="button"
            class="inline-flex items-center px-4 py-2 border-2 border-medical-blue-600 text-medical-blue-600 rounded-xl hover:bg-medical-blue-50 transition-all duration-200 font-semibold">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>
            <span x-text="showFilters ? 'إخفاء الفلاتر' : 'الفلاتر والبحث'"></span>
        </button>

        {{-- Filters Card --}}
        <div x-show="showFilters" x-transition class="mt-4 bg-white rounded-2xl shadow-medical p-6">
            <form method="GET" action="{{ route('admin.orders') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    {{-- Buyer Filter --}}
                    <div>
                        <label for="buyer" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المشتري
                        </label>
                        <select name="buyer" id="buyer"
                            class="w-full px-4 py-2 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع المشترين</option>
                            @foreach ($buyers as $id => $name)
                                <option value="{{ $id }}" {{ request('buyer') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Supplier Filter --}}
                    <div>
                        <label for="supplier" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المورد
                        </label>
                        <select name="supplier" id="supplier"
                            class="w-full px-4 py-2 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع الموردين</option>
                            @foreach ($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status Filter --}}
                    <div>
                        <label for="status" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الحالة
                        </label>
                        <select name="status" id="status"
                            class="w-full px-4 py-2 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار
                            </option>
                            <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد
                                المعالجة</option>
                            <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن
                            </option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم
                                التسليم
                            </option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                            </option>
                        </select>
                    </div>

                    {{-- Search --}}
                    <div>
                        <label for="search" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            بحث
                        </label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="رقم الطلب، المشتري، المورد..."
                            class="w-full px-4 py-2 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                    </div>
                </div>

                {{-- Filter Actions --}}
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                        class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.orders') }}"
                        class="px-6 py-2 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">رقم الطلب</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المشتري</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المورد</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المبلغ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">التاريخ</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200">
                    {{-- Sample Order Row 1 --}}
                    <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <p class="font-medium text-medical-blue-600">#ORD-1234</p>
                        </td>
                        <td class="px-6 py-4 text-medical-gray-900">مستشفى طرابلس المركزي</td>
                        <td class="px-6 py-4 text-medical-gray-900">شركة المعدات الطبية</td>
                        <td class="px-6 py-4 text-medical-gray-900 font-semibold">12,500 د.ل</td>
                        <td class="px-6 py-4 text-medical-gray-600">2024-03-15</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-green-100 text-medical-green-700">
                                مكتمل
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', 1) }}"
                                class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-200 inline-flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>

                    {{-- Sample Order Row 2 --}}
                    <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <p class="font-medium text-medical-blue-600">#ORD-1235</p>
                        </td>
                        <td class="px-6 py-4 text-medical-gray-900">عيادة الأمل</td>
                        <td class="px-6 py-4 text-medical-gray-900">مؤسسة الصحة الليبية</td>
                        <td class="px-6 py-4 text-medical-gray-900 font-semibold">8,200 د.ل</td>
                        <td class="px-6 py-4 text-medical-gray-600">2024-03-16</td>
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-yellow-100 text-medical-yellow-700">
                                قيد المعالجة
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('admin.orders.show', 2) }}"
                                class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-200 inline-flex">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-dashboard.layout>
