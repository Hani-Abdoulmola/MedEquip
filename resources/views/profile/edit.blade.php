@php
    // Determine user role and type for dashboard layout
    $userRole = $userRole ?? 'admin';
    $userType = $userType ?? 'مستخدم';
@endphp

<x-dashboard.layout title="الملف الشخصي" :userRole="$userRole" :userName="$user->name" :userType="$userType">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الملف الشخصي</h1>
                <p class="mt-2 text-medical-gray-600">تحديث معلوماتك الشخصية وإعدادات الحساب</p>
            </div>
        </div>
    </div>

    {{-- Profile Edit Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 md:p-8" x-data="{ showPassword: false }" x-cloak>
        <form method="POST" action="{{ route('profile.update') }}">
            @csrf
            @method('PATCH')

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">
                    المعلومات الأساسية
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">

                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            الاسم الكامل <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                            autofocus autocomplete="name"
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
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                            autocomplete="username"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('email') border-red-500 @enderror placeholder-medical-gray-400"
                            placeholder="example@email.com">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                            <p class="mt-1 text-xs text-medical-yellow-600">
                                بريدك الإلكتروني غير موثق. 
                                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="underline hover:text-medical-yellow-700">
                                        إرسال رابط التحقق
                                    </button>
                                </form>
                            </p>
                        @endif
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            رقم الهاتف
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('phone') border-red-500 @enderror placeholder-medical-gray-400"
                            placeholder="+218 91 234 5678">
                        @error('phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Password Change Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">
                    تغيير كلمة المرور (اختياري)
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    {{-- New Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-medical-gray-700 mb-1">
                            كلمة المرور الجديدة
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password"
                                class="w-full px-4 py-2.5 pl-11 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('password') border-red-500 @enderror placeholder-medical-gray-400"
                                placeholder="اتركه فارغاً إذا لم ترغب في التغيير">
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
                        <p class="mt-1 text-xxs text-medical-gray-500">اتركه فارغاً إذا لم ترغب في تغيير كلمة المرور.
                        </p>
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-medium text-medical-gray-700 mb-1">
                            تأكيد كلمة المرور
                        </label>
                        <input :type="showPassword ? 'text' : 'password'" id="password_confirmation"
                            name="password_confirmation"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 placeholder-medical-gray-400"
                            placeholder="أعد كتابة كلمة المرور الجديدة">
                    </div>
                </div>
            </div>

            {{-- Additional Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-2 border-b border-medical-gray-200">
                    معلومات إضافية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address"
                            class="block text-sm font-medium text-medical-gray-700 mb-1">العنوان</label>
                        <textarea id="address" name="address" rows="3"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 placeholder-medical-gray-400"
                            placeholder="العنوان الكامل">{{ old('address', $user->address) }}</textarea>
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city"
                            class="block text-sm font-medium text-medical-gray-700 mb-1">المدينة</label>
                        <input type="text" id="city" name="city" value="{{ old('city', $user->city) }}"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 placeholder-medical-gray-400"
                            placeholder="المدينة">
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country"
                            class="block text-sm font-medium text-medical-gray-700 mb-1">الدولة</label>
                        <input type="text" id="country" name="country"
                            value="{{ old('country', $user->country) }}"
                            class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 placeholder-medical-gray-400"
                            placeholder="الدولة">
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-medical-gray-200">
                <button type="submit"
                    class="inline-flex items-center gap-1 px-6 py-3 bg-medical-blue-600 text-white rounded-md hover:bg-medical-blue-700 font-medium transition-all shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>حفظ التغييرات</span>
                </button>
            </div>
        </form>
    </div>

    {{-- Delete Account Section --}}
    <div class="mt-8 bg-white rounded-2xl shadow-medical p-6 md:p-8">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-medical-gray-900 mb-2">حذف الحساب</h2>
            <p class="text-sm text-medical-gray-600">
                بمجرد حذف حسابك، سيتم حذف جميع مواردك وبياناتك نهائياً. قبل حذف حسابك، يرجى تنزيل أي بيانات أو معلومات تريد الاحتفاظ بها.
            </p>
        </div>

        <form method="POST" action="{{ route('profile.destroy') }}" x-data="{ showConfirm: false }">
            @csrf
            @method('DELETE')

            <div x-show="!showConfirm" x-transition>
                <button type="button" @click="showConfirm = true"
                    class="px-6 py-3 bg-medical-red-50 text-medical-red-600 rounded-md hover:bg-medical-red-100 transition-colors duration-200 font-medium">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    حذف الحساب
                </button>
            </div>

            <div x-show="showConfirm" x-transition class="space-y-4">
                <div>
                    <label for="delete_password" class="block text-sm font-medium text-medical-gray-700 mb-1">
                        كلمة المرور <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="delete_password" name="password" required
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-md focus:ring-2 focus:ring-medical-red-500 focus:border-medical-red-500 transition-all duration-200 @error('password', 'userDeletion') border-red-500 @enderror"
                        placeholder="أدخل كلمة المرور للتأكيد">
                    @error('password', 'userDeletion')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit"
                        class="px-6 py-3 bg-medical-red-600 text-white rounded-md hover:bg-medical-red-700 transition-colors duration-200 font-medium">
                        تأكيد الحذف
                    </button>
                    <button type="button" @click="showConfirm = false"
                        class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-md hover:bg-medical-gray-200 transition-colors duration-200 font-medium">
                        إلغاء
                    </button>
                </div>
            </div>
        </form>
    </div>

</x-dashboard.layout>
