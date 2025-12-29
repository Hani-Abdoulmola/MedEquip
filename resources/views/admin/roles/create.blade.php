{{-- Admin Roles Management - Create Role (DocuTechHub Style) --}}
<x-dashboard.layout title="إضافة دور جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

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
                        <a href="{{ route('admin.roles.index') }}"
                            class="mr-2 text-sm font-medium text-medical-gray-500 hover:text-medical-blue-600">الأدوار</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-medical-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="mr-2 text-sm font-medium text-medical-gray-700">إضافة دور</span>
                    </div>
                </li>
            </ol>
        </nav>
    </div>

    {{-- Page Header --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-medical-gray-900 mb-2">إضافة دور جديد للموظفين</h2>
        <p class="text-medical-gray-600">إنشاء دور مخصص للموظفين (Staff) مع تعيين الصلاحيات الإدارية</p>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="mb-6 bg-red-50 border-r-4 border-red-500 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="mr-3">
                    <h3 class="text-sm font-medium text-red-800">يرجى تصحيح الأخطاء التالية:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <form action="{{ route('admin.roles.store') }}" method="post">
        @csrf

        {{-- Role Name Card --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-medical-gray-200 bg-medical-gray-50">
                <h5 class="text-lg font-semibold text-medical-gray-900">معلومات الدور</h5>
                <p class="text-sm text-medical-gray-600 mt-1">هذا الدور مخصص للموظفين (Staff) فقط</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2" for="ar_name">
                            اسم الدور بالعربية <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="ar_name" id="ar_name" value="{{ old('ar_name') }}"
                            class="w-full px-4 py-2 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('ar_name') border-red-500 @enderror"
                            required placeholder="مثال: مدير المبيعات، محاسب، مسؤول المنتجات"
                            oninvalid="this.setCustomValidity('الرجاء ادخال اسم الدور بالعربية')"
                            oninput="this.setCustomValidity('')">
                        @error('ar_name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2" for="name">
                            اسم الدور بالإنجليزية <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}"
                            class="w-full px-4 py-2 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('name') border-red-500 @enderror"
                            required placeholder="مثال: sales-manager, accountant, product-manager"
                            oninvalid="this.setCustomValidity('الرجاء ادخال اسم الدور بالإنجليزية')"
                            oninput="this.setCustomValidity('')">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-medical-gray-500">سيتم استخدام هذا الدور لتعيينه للموظفين (Staff)
                            عند إنشاء حساباتهم</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Permissions Card --}}
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-medical-gray-200">
                <h4 class="text-lg font-semibold text-medical-gray-900">أذونات الدور</h4>
                <p class="text-sm text-medical-gray-600 mt-1">اختر الصلاحيات الإدارية التي سيتم منحها للموظفين الذين
                    لديهم هذا الدور</p>
            </div>
            <div class="p-6">
                <div class="space-y-6">
                    @php
                        $allPermissions = collect();
                        foreach ($permissions as $modulePermissions) {
                            $allPermissions = $allPermissions->merge($modulePermissions);
                        }
                        $count = 0;
                    @endphp

                    @foreach ($allPermissions as $permission)
                        @if ($count % 12 == 0)
                            @if ($count != 0)
                </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-3">
                    @endif
                    @if ($count % 4 == 0)
                        <div class="border-2 border-medical-gray-200 rounded-2xl p-4">
                            <div class="mb-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox"
                                        class="select-all w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-medical-blue-500"
                                        id="selectAll{{ floor($count / 4) }}">
                                    <span class="mr-2 text-sm font-medium text-medical-gray-700">تحديد الكل</span>
                                </label>
                            </div>
                    @endif
                    <div class="mb-3">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                class="permission-checkbox w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-medical-blue-500"
                                id="permission{{ $permission->id }}">
                            <span
                                class="mr-2 text-sm text-medical-gray-700">{{ $permission->ar_name ?? $permission->name }}</span>
                        </label>
                    </div>
                    @if (($count + 1) % 4 == 0)
                </div>
                @endif
                @php $count++; @endphp
                @endforeach
            </div>
        </div>
        </div>

        {{-- Submit Button --}}
        <div class="mb-6">
            <button type="submit"
                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-lg font-medium shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                حفظ الدور
            </button>
        </div>
    </form>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.select-all').forEach(function(selectAllCheckbox) {
                    selectAllCheckbox.addEventListener('change', function() {
                        let group = this.closest('.border-2');
                        group.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                            checkbox.checked = selectAllCheckbox.checked;
                        });
                    });
                });

                document.querySelectorAll('.permission-checkbox').forEach(function(permissionCheckbox) {
                    permissionCheckbox.addEventListener('change', function() {
                        let group = this.closest('.border-2');
                        let allChecked = Array.from(group.querySelectorAll('.permission-checkbox'))
                            .every(c => c.checked);
                        group.querySelector('.select-all').checked = allChecked;
                    });
                });
            });
        </script>
    @endpush

</x-dashboard.layout>
