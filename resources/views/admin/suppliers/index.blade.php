{{-- Admin Suppliers Management - List All Suppliers --}}
<x-dashboard.layout title="إدارة الموردين" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة الموردين</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراجعة جميع الموردين المسجلين</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الموردين</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">78</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">موردين نشطين</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">65</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-2">8</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-yellow-100 to-medical-yellow-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">موقوفين</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-2">5</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-red-100 to-medical-red-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" placeholder="ابحث عن مورد..."
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">الكل</option>
                    <option value="active">نشط</option>
                    <option value="pending">قيد المراجعة</option>
                    <option value="suspended">موقوف</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">المدينة</label>
                <select class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="">الكل</option>
                    <option value="tripoli">طرابلس</option>
                    <option value="benghazi">بنغازي</option>
                    <option value="misrata">مصراتة</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full px-6 py-2.5 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors duration-200 font-medium">
                    تطبيق الفلاتر
                </button>
            </div>
        </div>
    </div>

    {{-- Suppliers Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الشركة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المسؤول</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">البريد الإلكتروني</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المنتجات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200">
                    {{-- Sample Supplier Row 1 --}}
                    <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold">
                                    م
                                </div>
                                <div>
                                    <p class="font-medium text-medical-gray-900">شركة المعدات الطبية</p>
                                    <p class="text-sm text-medical-gray-600">طرابلس</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-medical-gray-900">محمد علي</td>
                        <td class="px-6 py-4 text-medical-gray-600">mohamed@medequip.ly</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700">
                                45 منتج
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-green-100 text-medical-green-700">
                                نشط
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2 space-x-reverse">
                                <a href="{{ route('admin.suppliers.show', 1) }}" class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <button class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</x-dashboard.layout>

