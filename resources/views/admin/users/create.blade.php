<x-dashboard.layout title="إضافة مستخدم جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة مستخدم جديد</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء حساب مستخدم جديد في النظام</p>
            </div>
            <a href="{{ route('admin.users') }}"
                class="inline-flex items-center gap-1 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 font-medium transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- RBAC Info Box --}}
    {{-- <div class="mb-6 bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 rounded-xl p-4">
        <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-medical-blue-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="flex-1">
                <h3 class="font-semibold text-medical-gray-900 mb-1">نظام إدارة الصلاحيات (RBAC)</h3>
                <p class="text-sm text-medical-gray-700 leading-relaxed">
                    <strong>مدير النظام (Admin):</strong> يمتلك جميع الصلاحيات تلقائياً عبر نظام Spatie Roles & Permissions. لا يحتاج إلى تعيين صلاحيات إضافية.
                    <br>
                    <strong>موظف إداري (Staff):</strong> يحتاج إلى تعيين صلاحيات صريحة بعد الإنشاء. يمكنك إدارة صلاحياته من صفحة <a href="{{ route('admin.role-permissions.index') }}" class="text-medical-blue-600 hover:text-medical-blue-700 underline font-medium">الأدوار والصلاحيات</a>.
                </p>
            </div>
        </div>
    </div> --}}

    {{-- Create User Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 md:p-8" x-data="{
        showPassword: false,
        userTypeRole: '{{ old('user_type_role', '') }}',
        get selectedRole() {
            if (!this.userTypeRole || this.userTypeRole === '') return null;
            const parts = String(this.userTypeRole).split(':');
            return parts.length === 2 ? parts[1] : null;
        },
        validOptions: @json(array_keys($combinedOptions)),
        isValidOption(value) {
            return this.validOptions.includes(value);
        }
    }" x-cloak>
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">المعلومات
                    الأساسية</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            الاسم الكامل <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            autofocus autocomplete="off"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror placeholder-medical-gray-400"
                            placeholder="مثال: محمد علي">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            autocomplete="off"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('email') border-red-500 @enderror placeholder-medical-gray-400"
                            placeholder="example@email.com">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('phone') border-red-500 @enderror placeholder-medical-gray-400"
                            placeholder="+218 91 234 5678">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- User Type & Role (RBAC-based) --}}
                    <div class="sm:col-span-2 lg:col-span-1">
                        <label for="user_type_role"
                            class="block mb-1 text-sm font-medium text-medical-gray-700 text-right w-full">
                            نوع المستخدم والدور (RBAC) <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <select id="user_type_role" name="user_type_role" required
                                class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('user_type_role') border-red-500 @enderror appearance-none bg-white cursor-pointer pr-10">
                                <option value="">-- اختر نوع المستخدم والدور --</option>
                                @foreach($combinedOptions as $key => $label)
                                    <option value="{{ $key }}" {{ old('user_type_role') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center">
                                <svg class="h-5 w-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </div>
                        @error('user_type_role')
                            <p class="mt-1 text-xs text-red-600 text-right">{{ $message }}</p>
                        @enderror
                        <div class="mt-2 space-y-1">
                            <p class="text-xxs text-medical-gray-500 text-right">
                                يتم تحديد نوع المستخدم والدور معاً وفقاً لنظام RBAC
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Password Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">
                    كلمة المرور</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                class="w-full px-4 py-2.5 pl-11 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('password') border-red-500 @enderror placeholder-medical-gray-400"
                                placeholder="كلمة المرور">
                            <button type="button" tabindex="-1" @click="showPassword = !showPassword"
                                class="absolute left-3 top-1/2 -translate-y-1/2 text-medical-gray-400 hover:text-medical-gray-600 transition-colors duration-200">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xxs text-medical-gray-500">كلمة المرور لا تقل عن 8 أحرف/أرقام، مع مراعاة
                            الأمان.</p>
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-medical-gray-700 mb-1">
                            تأكيد كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <input :type="showPassword ? 'text' : 'password'" id="password_confirmation"
                            name="password_confirmation" required
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 placeholder-medical-gray-400"
                            placeholder="أعد كتابة كلمة المرور">
                    </div>
                </div>
            </div>

            {{-- RBAC & Permissions Note (for Staff users) --}}
            <template x-if="selectedRole === 'Staff'">
                <div x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform translate-y-2"
                    class="mb-6 bg-medical-blue-50 border-l-4 border-medical-blue-500 p-4 rounded-r-lg">
                    <div class="flex items-start gap-2">
                        <svg class="w-5 h-5 text-medical-blue-600 mt-0.5 flex-shrink-0" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-medical-gray-900 mb-1">إدارة الصلاحيات للموظف الإداري
                            </p>
                            <p class="text-xs text-medical-gray-700">
                                بعد إنشاء المستخدم، يمكنك تعيين الصلاحيات الصريحة له من صفحة
                                <a href="{{ route('admin.role-permissions.index') }}"
                                    class="text-medical-blue-600 hover:text-medical-blue-700 underline font-semibold">الأدوار
                                    والصلاحيات</a>.
                                الموظفون الإداريون يحتاجون إلى صلاحيات صريحة للوصول إلى أقسام النظام.
                            </p>
                        </div>
                    </div>
            </template>

            {{-- Account Settings Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">
                    إعدادات الحساب
                </h2>

                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Status --}}
                        <div>
                            <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-1">
                                حالة الحساب <span class="text-red-500">*</span>
                            </label>
                            <select id="status" name="status" required
                                class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط
                                </option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط
                                </option>
                                <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>موقوف
                                </option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email Verification --}}
                        <div class="flex items-center gap-2 mt-2 md:mt-0">
                            <input type="checkbox" id="email_verified" name="email_verified" value="1"
                                {{ old('email_verified') ? 'checked' : '' }}
                                class="w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                            <label for="email_verified"
                                class="text-sm font-medium text-medical-gray-700 cursor-pointer">
                                تفعيل البريد الإلكتروني تلقائياً
                            </label>
                        </div>
                    </div>

                    {{-- Send Welcome Email --}}
                    <div class="flex items-center gap-2 mt-2">
                        <input type="checkbox" id="send_welcome_email" name="send_welcome_email" value="1"
                            {{ old('send_welcome_email', '1') ? 'checked' : '' }}
                            class="w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                        <label for="send_welcome_email"
                            class="text-sm font-medium text-medical-gray-700 cursor-pointer">
                            إرسال بريد إلكتروني ترحيبي للمستخدم
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.users') }}"
                    class="inline-flex items-center gap-1 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-md hover:bg-medical-gray-200 font-medium transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-1 px-6 py-3 bg-medical-blue-600 text-white rounded-md hover:bg-medical-blue-700 font-medium transition-all shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>إنشاء المستخدم</span>
                </button>
            </div>
        </form>
    </div>
</x-dashboard.layout>
