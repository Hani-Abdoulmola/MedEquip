{{-- Admin Roles Management - DocuTechHub Style --}}
<x-dashboard.layout title="الأدوار" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Breadcrumb --}}
    <div class="mb-6">
        <nav class="flex" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-2 space-x-reverse">
                <li class="inline-flex items-center">
                    <a href="{{ route('dashboard') }}"
                        class="inline-flex items-center text-sm font-medium text-medical-gray-700 hover:text-medical-blue-600">
                        <svg class="w-4 h-4 ml-2" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z">
                            </path>
                        </svg>
                        الرئيسية
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-medical-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="mr-2 text-sm font-medium text-medical-gray-500">الأدوار</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-medical-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="mr-2 text-sm font-medium text-medical-gray-700">القائمة</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-medical-gray-900 mb-2">أدوار الموظفين</h2>
        <p class="text-medical-gray-600">إدارة الأدوار المخصصة للموظفين (Staff) وتعيين الصلاحيات</p>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="mb-6 bg-gradient-to-r from-medical-green-500 to-medical-green-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-semibold">{{ session('success') }}</span>
            </div>
            <button @click="show = false" class="hover:bg-white/20 rounded-lg p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition
            class="mb-6 bg-gradient-to-r from-medical-red-500 to-medical-red-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span class="font-semibold">{{ session('error') ?? $errors->first() }}</span>
            </div>
            <button @click="show = false" class="hover:bg-white/20 rounded-lg p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Main Content Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        {{-- Card Header with Create Button --}}
        <div class="px-6 py-4 border-b border-medical-gray-200 bg-medical-gray-50">
            <div class="flex items-center justify-between">
                <p class="text-sm text-medical-gray-600">الأدوار المخصصة للموظفين فقط (Staff)</p>
                <a href="{{ route('admin.roles.create') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة دور جديد للموظفين</span>
                </a>
            </div>
        </div>

        {{-- Table Container --}}
        <div class="p-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200 text-center">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-sm font-semibold text-medical-gray-700 uppercase tracking-wider">
                                اسم الدور
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-sm font-semibold text-medical-gray-700 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @forelse ($roles as $role)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                                                {{ mb_substr($role->name, 0, 1) }}
                                            </div>
                                        </div>
                                        <div class="mr-4">
                                            <div class="text-sm font-medium text-medical-gray-900">{{ $role->ar_name ?? $role->name }}</div>
                                            <div class="text-xs text-medical-gray-500">
                                                {{ $role->users_count ?? $role->users->count() }} موظف
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.roles.show', $role) }}"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-medical-blue-700 bg-medical-blue-50 rounded-lg hover:bg-medical-blue-100 transition-colors duration-200">
                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                        </svg>
                                        <p class="text-medical-gray-600 font-medium mb-2">لا توجد أدوار للموظفين</p>
                                        <p class="text-sm text-medical-gray-500 mb-4">ابدأ بإنشاء دور جديد للموظفين (Staff)</p>
                                        <a href="{{ route('admin.roles.create') }}"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transition-all duration-200">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>إضافة دور جديد للموظفين</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if ($roles->hasPages())
                <div class="mt-6">
                    {{ $roles->links() }}
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

