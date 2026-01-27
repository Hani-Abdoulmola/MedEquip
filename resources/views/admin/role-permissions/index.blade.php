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
        
        {{-- Security Notice --}}
        <div class="mt-4 bg-gradient-to-r from-medical-green-50 to-medical-blue-50 border-2 border-medical-green-200 rounded-xl p-4">
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 text-medical-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
                <div>
                    <p class="text-sm font-semibold text-medical-green-900 mb-1">🔒 نظام الحماية محدث</p>
                    <p class="text-sm text-medical-green-700">
                        جميع المسارات الإدارية محمية الآن بصلاحيات على مستوى المسار. المستخدمون (Staff) يمكنهم الوصول فقط إلى الصفحات التي لديهم صلاحيات لها. المسؤولون (Admin) لديهم وصول كامل تلقائياً.
                    </p>
                </div>
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

    {{-- Tabs Navigation --}}
    <div class="mb-6">
        <div class="border-b border-medical-gray-200">
            <nav class="-mb-px flex space-x-reverse space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.role-permissions.index', ['tab' => 'users']) }}"
                    class="border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'users' ? 'border-medical-blue-500 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        صلاحيات المستخدمين
                    </span>
                </a>
                <a href="{{ route('admin.role-permissions.index', ['tab' => 'roles']) }}"
                    class="border-b-2 py-4 px-1 text-sm font-medium transition-colors {{ $activeTab === 'roles' ? 'border-medical-blue-500 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300' }}">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        صلاحيات الأدوار
                    </span>
                </a>
            </nav>
        </div>
    </div>

    {{-- Bulk Mode Toggle (Users Tab Only) --}}
    @if ($activeTab === 'users')
        <div class="mb-4 bg-yellow-50 border-2 border-yellow-300 rounded-xl p-4" x-data="{ bulkMode: false }">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <label class="flex items-center cursor-pointer">
                        <input type="checkbox" x-model="bulkMode"
                            class="w-5 h-5 text-yellow-600 border-yellow-300 rounded focus:ring-2 focus:ring-yellow-500">
                        <span class="mr-2 text-sm font-bold text-yellow-900">
                            وضع التعيين الجماعي (Bulk Mode)
                        </span>
                    </label>
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <span class="text-xs text-yellow-700" x-show="!bulkMode">
                    قم بالتفعيل لتطبيق الصلاحيات على عدة مستخدمين دفعة واحدة
                </span>
                <span class="text-xs text-yellow-800 font-semibold" x-show="bulkMode" x-cloak>
                    ✓ الوضع الجماعي نشط - اختر المستخدمين أدناه
                </span>
            </div>

            {{-- Bulk Assignment Form (shown when bulk mode is active) --}}
            <div x-show="bulkMode" x-cloak x-transition class="mt-6 pt-6 border-t border-yellow-200">
                <form action="{{ route('admin.role-permissions.bulk-assign') }}" method="POST" id="bulk-form">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- User Selection --}}
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-900 mb-3">اختر المستخدمين</label>
                            <div class="max-h-60 overflow-y-auto border-2 border-yellow-300 rounded-xl p-4 bg-white space-y-2">
                                <label class="flex items-center gap-2 p-2 hover:bg-yellow-50 rounded-lg cursor-pointer border-b border-yellow-100">
                                    <input type="checkbox" id="select-all-users" onclick="toggleAllUsers(this)"
                                        class="w-5 h-5 text-yellow-600 border-yellow-300 rounded">
                                    <span class="text-sm font-bold text-yellow-900">تحديد الكل</span>
                                </label>
                                @foreach($users as $user)
                                    <label class="flex items-center gap-2 p-2 hover:bg-yellow-50 rounded-lg cursor-pointer">
                                        <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="bulk-user-checkbox w-4 h-4 text-yellow-600 border-yellow-300 rounded">
                                        <span class="text-sm text-medical-gray-900">{{ $user->name }}</span>
                                        <span class="text-xs text-medical-gray-500">({{ $user->roles->first()->ar_name ?? 'بدون دور' }})</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-yellow-700 mt-2">
                                <span id="selected-users-count">0</span> مستخدم محدد
                            </p>
                        </div>

                        {{-- Action & Permissions --}}
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-900 mb-3">الإجراء</label>
                            <select name="action" required
                                class="w-full px-4 py-2 border-2 border-yellow-300 rounded-xl focus:ring-2 focus:ring-yellow-500 mb-4">
                                <option value="replace">استبدال (مسح القديم + تطبيق الجديد)</option>
                                <option value="merge">دمج (إضافة للموجود)</option>
                                <option value="remove">حذف (إزالة الصلاحيات المحددة)</option>
                            </select>

                            <label class="block text-sm font-bold text-medical-gray-900 mb-3">الصلاحيات</label>
                            <div class="max-h-48 overflow-y-auto border-2 border-yellow-300 rounded-xl p-3 bg-white">
                                @foreach ($permissions as $module => $modulePermissions)
                                    <div class="mb-3">
                                        <strong class="text-xs text-medical-gray-600 block mb-1">{{ $moduleLabels[$module] ?? ucfirst($module) }}</strong>
                                        <div class="space-y-1">
                                            @foreach ($modulePermissions as $permission)
                                                <label class="flex items-center gap-2 text-xs cursor-pointer hover:bg-yellow-50 p-1 rounded">
                                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                        class="w-3 h-3 text-yellow-600 border-yellow-300 rounded">
                                                    <span class="text-medical-gray-700">{{ $permission->ar_name ?? $permission->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit"
                                class="w-full mt-4 px-6 py-3 bg-gradient-to-r from-yellow-500 to-orange-500 text-white rounded-xl hover:shadow-lg transition font-bold">
                                <span class="flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    تطبيق على المستخدمين المحددين
                                </span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Main Content Card --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        @if ($activeTab === 'users')
            {{-- User Selector Section --}}
            <div class="px-6 py-10 border-b border-medical-gray-200 bg-medical-gray-50">
                <form method="GET" action="{{ route('admin.role-permissions.index') }}"
                    class="flex flex-col md:flex-row items-center gap-6">
                    <input type="hidden" name="tab" value="users">
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
                                        {{ $user->name }} ({{ $user->email }})
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
                            اختر مستخدماً لإدارة صلاحياته المباشرة (إضافة للصلاحيات من الدور)
                        </small>
                    </div>
                </form>
            </div>
        @else
            {{-- Role Selector Section --}}
            <div class="px-6 py-10 border-b border-medical-gray-200 bg-medical-gray-50">
                <form method="GET" action="{{ route('admin.role-permissions.index') }}"
                    class="flex flex-col md:flex-row items-center gap-6">
                    <input type="hidden" name="tab" value="roles">
                    <div class="w-full max-w-lg">
                        <label for="role_id" class="block text-base font-bold text-medical-gray-800 mb-3">
                            <span class="inline-flex items-center gap-2">
                                <svg class="h-5 w-5 text-medical-blue-500" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                اختر الدور
                            </span>
                        </label>
                        <div class="relative">
                            <select name="role_id" id="role_id" onchange="this.form.submit()"
                                class="w-full px-5 py-3 border-2 border-medical-blue-200 rounded-2xl shadow-md bg-white text-medical-gray-800 focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all appearance-none cursor-pointer pr-12 text-base font-medium">
                                <option value="">-- اختر دور --</option>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}"
                                        {{ request('role_id') == $role->id ? 'selected' : '' }}>
                                        {{ $role->ar_name ?? $role->name }}
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
                            اختر دوراً لإدارة صلاحياته (جميع المستخدمين بهذا الدور سيرثون هذه الصلاحيات)
                        </small>
                    </div>
                </form>
            </div>
        @endif

        {{-- Permissions Section for Users --}}
        @if ($activeTab === 'users' && $selectedUser)
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
                        <h4 class="text-xl font-bold text-medical-gray-900 mb-2">اذونات الصلاحية المخصصة</h4>
                        <p class="text-sm text-medical-gray-600">أو اختر الصلاحيات يدوياً حسب الحاجة</p>
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
                                    هذه صلاحيات مباشرة للمستخدم (إضافة للصلاحيات من دوره). المستخدم سيحصل على: صلاحيات دوره + الصلاحيات المباشرة المحددة هنا.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        @elseif ($activeTab === 'roles' && $selectedRole)
            {{-- Permissions Section for Roles --}}
            <form method="POST" action="{{ route('admin.role-permissions.update-role', $selectedRole) }}"
                id="role-permissions-form">
                @csrf
                @method('POST')

                <div class="px-6 py-6">
                    {{-- Selected Role Info --}}
                    <div
                        class="mb-6 p-4 bg-gradient-to-r from-medical-blue-50 to-medical-green-50 rounded-xl border border-medical-blue-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-medical-gray-900">{{ $selectedRole->ar_name ?? $selectedRole->name }}</h3>
                                    <p class="text-sm text-medical-gray-600">{{ $selectedRole->name }}</p>
                                    @php
                                        $usersWithRole = \App\Models\User::role($selectedRole->name)->count();
                                    @endphp
                                    <p class="text-sm text-medical-gray-600 mt-1">
                                        عدد المستخدمين: {{ $usersWithRole }}
                                    </p>
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
                        <h4 class="text-xl font-bold text-medical-gray-900 mb-2">صلاحيات الدور</h4>
                        <p class="text-sm text-medical-gray-600">اختر الصلاحيات المطلوبة لهذا الدور (سيرثها جميع المستخدمين)</p>
                    </div>

                    {{-- Permissions Grid by Module (Same as users) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div
                                class="border-2 border-medical-gray-200 rounded-2xl p-4 hover:border-medical-blue-300 transition-all bg-white shadow-sm">
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

                                <div class="space-y-3">
                                    @foreach ($modulePermissions as $permission)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="permissions[]"
                                                value="{{ $permission->id }}"
                                                {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                                class="permission-checkbox w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500"
                                                id="permission_role_{{ $permission->id }}"
                                                data-module="{{ $module }}">
                                            <label for="permission_role_{{ $permission->id }}"
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
                                حفظ صلاحيات الدور
                            </span>
                        </button>
                    </div>

                    {{-- Warning Box for Roles --}}
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-yellow-900 mb-1">⚠️ تحذير مهم</p>
                                <p class="text-sm text-yellow-700">
                                    تعديل صلاحيات الدور سيؤثر على جميع المستخدمين ({{ $usersWithRole }}) الذين لديهم هذا الدور. التغييرات ستطبق فوراً.
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
        // Bulk Mode: Toggle all users
        function toggleAllUsers(checkbox) {
            const userCheckboxes = document.querySelectorAll('.bulk-user-checkbox');
            userCheckboxes.forEach(cb => cb.checked = checkbox.checked);
            updateSelectedUsersCount();
        }

        // Bulk Mode: Update selected users count
        function updateSelectedUsersCount() {
            const count = document.querySelectorAll('.bulk-user-checkbox:checked').length;
            const countElement = document.getElementById('selected-users-count');
            if (countElement) {
                countElement.textContent = count;
            }
        }

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

            // Bulk Mode: Listen to user checkbox changes
            document.querySelectorAll('.bulk-user-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateSelectedUsersCount);
            });

            // Initialize bulk users count
            updateSelectedUsersCount();
        });
    </script>

</x-dashboard.layout>
