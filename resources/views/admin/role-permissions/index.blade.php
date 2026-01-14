{{-- Unified Roles & Permissions Management --}}
<x-dashboard.layout title="الأدوار و الصلاحيات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display mb-2">الأدوار و الصلاحيات</h1>
                <p class="text-medical-gray-600">إدارة الأدوار والصلاحيات للمستخدمين الداخليين</p>
            </div>
        </div>
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

    {{-- Only show error messages here, don't duplicate success --}}
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
        {{-- User Selector Section --}}
        <div class="px-6 py-10 border-b border-medical-gray-200 bg-medical-gray-50">
            <form method="GET" action="{{ route('admin.role-permissions.index') }}"
                class="flex flex-col md:flex-row items-center gap-6">
                <div class="w-full max-w-lg">
                    <label for="user_id" class="block text-base font-bold text-medical-gray-800 mb-3">
                        <span class="inline-flex items-center gap-2">
                            <svg class="h-5 w-5 text-medical-blue-500" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Zm2 10a6 6 0 1 0-12 0h12Z" />
                            </svg>
                            اختر المستخدم
                        </span>
                    </label>
                    <div class="relative">
                        <select name="user_id" id="user_id" onchange="this.form.submit()"
                            class="w-full px-5 py-3 border-2 border-medical-blue-200 rounded-2xl shadow-md bg-white text-medical-gray-800 focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all appearance-none cursor-pointer pr-12 text-base font-medium">
                            <option value="">-- اختر مستخدم --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                            <svg class="h-4 w-4 text-medical-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                    </div>
                    <small class="block mt-2 text-medical-gray-400 text-sm">
                        ابحث عن المستخدم حسب الاسم أو البريد الإلكتروني أو راجع الأدوار الحالية.
                    </small>
                </div>
            </form>
        </div>

        {{-- Permissions Section (Only shown when user is selected) --}}
        @if ($selectedUser)
            <form method="POST" action="{{ route('admin.role-permissions.assign', $selectedUser) }}"
                id="permissions-form">
                @csrf
                @method('POST')

                <div class="px-6 py-6">
                    {{-- Selected User Info --}}
                    <div
                        class="mb-6 p-4 bg-gradient-to-r from-medical-blue-50 to-medical-green-50 rounded-xl border border-medical-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white text-2xl font-bold">
                                    {{ mb_substr($selectedUser->name, 0, 1) }}
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-medical-gray-900">{{ $selectedUser->name }}</h3>
                                    <p class="text-sm text-medical-gray-600">{{ $selectedUser->email }}</p>
                                    @if ($selectedUser->roles->count() > 0)
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            @foreach ($selectedUser->roles as $role)
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-blue-100 text-medical-blue-700">
                                                    {{ $role->ar_name ?? $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-medical-gray-600">الصلاحيات المحددة</p>
                                <p class="text-2xl font-bold text-medical-gray-900" id="selected-count">0</p>
                            </div>
                        </div>
                    </div>

                    {{-- Section Title --}}
                    <div class="mb-6">
                        <h4 class="text-xl font-bold text-medical-gray-900 mb-2">اذونات الصلاحية</h4>
                        <p class="text-sm text-medical-gray-600">اختر الصلاحيات المطلوبة لهذا المستخدم</p>
                    </div>

                    {{-- Permissions Grid by Module (DocuTechHub Style) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div
                                class="border-2 border-medical-gray-200 rounded-2xl p-4 hover:border-medical-blue-300 transition-all bg-white shadow-sm">
                                {{-- Module Header with Select All --}}
                                <div
                                    class="flex items-center justify-between mb-3 pb-3 border-b border-medical-gray-100">
                                    <div class="flex items-center">
                                        <input type="checkbox"
                                            class="select-all-module w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500"
                                            id="selectAll_{{ $module }}" data-module="{{ $module }}">
                                        <label for="selectAll_{{ $module }}"
                                            class="mr-2 text-sm font-bold text-medical-blue-600 cursor-pointer">
                                            تحديد الكل
                                        </label>
                                    </div>
                                    <span
                                        class="text-xs font-semibold text-medical-gray-500 bg-medical-gray-100 px-2 py-1 rounded-full">
                                        {{ $moduleLabels[$module] ?? ucfirst($module) }}
                                    </span>
                                </div>

                                {{-- Permissions in this module --}}
                                <div class="space-y-3">
                                    @foreach ($modulePermissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="permissions[]"
                                                value="{{ $permission->id }}"
                                                {{ in_array($permission->id, $userPermissions) ? 'checked' : '' }}
                                                class="permission-checkbox w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500"
                                                id="permission_{{ $permission->id }}"
                                                data-module="{{ $module }}">
                                            <label for="permission_{{ $permission->id }}"
                                                class="mr-2 text-sm text-medical-gray-700 cursor-pointer hover:text-medical-blue-600 transition-colors">
                                                {{ $permission->ar_name ?? $permission->name }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="selectAllPermissions()"
                                class="px-5 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors font-medium shadow-md">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    تحديد الكل
                                </span>
                            </button>
                            <button type="button" onclick="deselectAllPermissions()"
                                class="px-5 py-2.5 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-colors font-medium">
                                <span class="flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    إلغاء التحديد
                                </span>
                            </button>
                        </div>
                        <button type="submit"
                            class="px-8 py-3 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white rounded-xl hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-semibold">
                            <span class="flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                حفظ الصلاحيات
                            </span>
                        </button>
                    </div>

                    {{-- Info Box --}}
                    <div class="mt-6 p-4 bg-medical-blue-50 border border-medical-blue-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-medical-blue-600 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-medical-blue-900 mb-1">ملاحظة مهمة</p>
                                <p class="text-sm text-medical-blue-700">
                                    الصلاحيات تُعطى يدوياً فقط. الأدوار للتصنيف فقط ولا تعطي صلاحيات تلقائياً.
                                    المستخدم سيحصل فقط على الصلاحيات التي تحددها هنا.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @else
            {{-- Empty State --}}
            <div class="px-6 py-12 text-center">
                <svg class="w-24 h-24 mx-auto text-medical-gray-300 mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                <h3 class="text-xl font-semibold text-medical-gray-900 mb-2">اختر مستخدم للبدء</h3>
                <p class="text-medical-gray-600">يرجى اختيار مستخدم من القائمة أعلاه لإدارة صلاحياته</p>
            </div>
        @endif
    </div>

    {{-- JavaScript --}}
    <script>
        // Select all permissions
        function selectAllPermissions() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = true;
            });
            document.querySelectorAll('.select-all-module').forEach(checkbox => {
                checkbox.checked = true;
            });
            updateSelectedCount();
        }

        // Deselect all permissions
        function deselectAllPermissions() {
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.querySelectorAll('.select-all-module').forEach(checkbox => {
                checkbox.checked = false;
            });
            updateSelectedCount();
        }

        // Update selected count
        function updateSelectedCount() {
            const checked = document.querySelectorAll('.permission-checkbox:checked').length;
            const countElement = document.getElementById('selected-count');
            if (countElement) {
                countElement.textContent = checked;
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Update count on load
            updateSelectedCount();

            // Handle "Select All" for each module
            document.querySelectorAll('.select-all-module').forEach(selectAll => {
                selectAll.addEventListener('change', function() {
                    const moduleName = this.dataset.module;
                    const moduleCheckboxes = document.querySelectorAll(
                        `.permission-checkbox[data-module="${moduleName}"]`);
                    moduleCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateSelectedCount();
                });
            });

            // Handle individual permission checkbox change
            document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const moduleName = this.dataset.module;
                    const moduleCheckboxes = document.querySelectorAll(
                        `.permission-checkbox[data-module="${moduleName}"]`);
                    const selectAllCheckbox = document.querySelector(
                        `.select-all-module[data-module="${moduleName}"]`);

                    // Check if all checkboxes in the module are checked
                    const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                    if (selectAllCheckbox) {
                        selectAllCheckbox.checked = allChecked;
                    }

                    updateSelectedCount();
                });
            });

            // Set initial state of "Select All" checkboxes
            document.querySelectorAll('.select-all-module').forEach(selectAll => {
                const moduleName = selectAll.dataset.module;
                const moduleCheckboxes = document.querySelectorAll(
                    `.permission-checkbox[data-module="${moduleName}"]`);
                if (moduleCheckboxes.length > 0) {
                    const allChecked = Array.from(moduleCheckboxes).every(cb => cb.checked);
                    selectAll.checked = allChecked;
                }
            });
        });
    </script>

</x-dashboard.layout>
