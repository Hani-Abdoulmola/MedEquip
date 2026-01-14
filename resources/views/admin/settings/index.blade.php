{{-- Admin Settings - Professional System Configuration --}}
<x-dashboard.layout title="إعدادات النظام" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Success/Error Messages --}}
    {{-- Note: Success messages are displayed in dashboard layout component --}}
    {{-- Only show error messages here to avoid duplicates --}}

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="mb-6 bg-gradient-to-r from-medical-red-500 to-medical-red-600 text-white px-6 py-4 rounded-xl shadow-lg flex items-center justify-between">
            <div class="flex items-center gap-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span class="font-semibold">{{ session('error') }}</span>
            </div>
            <button @click="show = false" class="hover:bg-white/20 rounded-lg p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    @endif

    {{-- Premium Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">إعدادات النظام</h1>
                <p class="mt-3 text-base text-medical-gray-600">إدارة وتكوين إعدادات المنصة والأنظمة المتكاملة</p>
            </div>
        </div>
    </div>

    {{-- Settings Navigation Tabs --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-2 mb-8" x-data="{ activeTab: 'general' }">
        <div class="flex flex-wrap gap-2">
            <button @click="activeTab = 'general'"
                :class="activeTab === 'general' ?
                    'bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white shadow-lg' :
                    'bg-transparent text-medical-gray-600 hover:bg-medical-gray-50'"
                class="px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                الإعدادات العامة
            </button>

            <button @click="activeTab = 'email'"
                :class="activeTab === 'email' ?
                    'bg-gradient-to-r from-medical-purple-500 to-medical-purple-600 text-white shadow-lg' :
                    'bg-transparent text-medical-gray-600 hover:bg-medical-gray-50'"
                class="px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                البريد الإلكتروني
            </button>

            <button @click="activeTab = 'payment'"
                :class="activeTab === 'payment' ?
                    'bg-gradient-to-r from-medical-green-500 to-medical-green-600 text-white shadow-lg' :
                    'bg-transparent text-medical-gray-600 hover:bg-medical-gray-50'"
                class="px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                الدفع والعمولات
            </button>

            <button @click="activeTab = 'security'"
                :class="activeTab === 'security' ?
                    'bg-gradient-to-r from-medical-red-500 to-medical-red-600 text-white shadow-lg' :
                    'bg-transparent text-medical-gray-600 hover:bg-medical-gray-50'"
                class="px-6 py-3 rounded-xl font-bold transition-all duration-200 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                الأمان
            </button>
        </div>

        {{-- General Settings Tab --}}
        <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-6">
            <div
                class="bg-gradient-to-br from-white to-medical-gray-50 rounded-2xl p-8 border-2 border-medical-gray-200">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-medical-gray-900">الإعدادات العامة</h2>
                        <p class="text-sm text-medical-gray-600">تكوين معلومات المنصة الأساسية</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update.general') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                اسم المنصة
                            </label>
                            <input type="text" name="platform_name"
                                value="{{ old('platform_name', $settings['general']['platform_name'] ?? 'MediEquip') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('platform_name') border-medical-red-500 @enderror">
                            @error('platform_name')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                البريد الإلكتروني للدعم
                            </label>
                            <input type="email" name="support_email"
                                value="{{ old('support_email', $settings['general']['support_email'] ?? 'support@mediequip.ly') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('support_email') border-medical-red-500 @enderror">
                            @error('support_email')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                رقم الهاتف
                            </label>
                            <input type="tel" name="support_phone"
                                value="{{ old('support_phone', $settings['general']['support_phone'] ?? '+218 21 123 4567') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('support_phone') border-medical-red-500 @enderror">
                            @error('support_phone')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                العملة الافتراضية
                            </label>
                            <select name="default_currency"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('default_currency') border-medical-red-500 @enderror">
                                <option value="LYD"
                                    {{ old('default_currency', $settings['general']['default_currency'] ?? 'LYD') == 'LYD' ? 'selected' : '' }}>
                                    💵 دينار ليبي (LYD)</option>
                                <option value="USD"
                                    {{ old('default_currency', $settings['general']['default_currency'] ?? 'LYD') == 'USD' ? 'selected' : '' }}>
                                    💲 دولار أمريكي (USD)</option>
                                <option value="EUR"
                                    {{ old('default_currency', $settings['general']['default_currency'] ?? 'LYD') == 'EUR' ? 'selected' : '' }}>
                                    💶 يورو (EUR)</option>
                            </select>
                            @error('default_currency')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                </svg>
                                اللغة الافتراضية
                            </label>
                            <select name="default_language"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('default_language') border-medical-red-500 @enderror">
                                <option value="ar"
                                    {{ old('default_language', $settings['general']['default_language'] ?? 'ar') == 'ar' ? 'selected' : '' }}>
                                    🇱🇾 العربية</option>
                                <option value="en"
                                    {{ old('default_language', $settings['general']['default_language'] ?? 'ar') == 'en' ? 'selected' : '' }}>
                                    🇬🇧 English</option>
                            </select>
                            @error('default_language')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                المنطقة الزمنية
                            </label>
                            <select name="timezone"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('timezone') border-medical-red-500 @enderror">
                                <option value="Africa/Tripoli"
                                    {{ old('timezone', $settings['general']['timezone'] ?? 'Africa/Tripoli') == 'Africa/Tripoli' ? 'selected' : '' }}>
                                    🌍 طرابلس (GMT+2)</option>
                                <option value="UTC"
                                    {{ old('timezone', $settings['general']['timezone'] ?? 'Africa/Tripoli') == 'UTC' ? 'selected' : '' }}>
                                    ⏰ UTC</option>
                            </select>
                            @error('timezone')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7" />
                            </svg>
                            وصف المنصة
                        </label>
                        <textarea name="platform_description" rows="4"
                            class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-blue-100 focus:border-medical-blue-500 transition-all duration-200 text-medical-gray-900 font-medium @error('platform_description') border-medical-red-500 @enderror">{{ old('platform_description', $settings['general']['platform_description'] ?? 'منصة MediEquip هي منصة B2B لتوريد المعدات الطبية في ليبيا') }}</textarea>
                        @error('platform_description')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Toggle Switches --}}
                    <div class="bg-white rounded-xl p-6 border-2 border-medical-gray-200 space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-medical-red-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">وضع الصيانة</p>
                                    <p class="text-xs text-medical-gray-500">إيقاف المنصة مؤقتاً للصيانة</p>
                                </div>
                            </div>
                            <input type="checkbox" name="maintenance_mode" value="1"
                                {{ old('maintenance_mode', $settings['general']['maintenance_mode'] ?? false) ? 'checked' : '' }}
                                class="w-6 h-6 text-medical-red-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-red-100">
                        </div>

                        <div class="flex items-center justify-between pt-4 border-t-2 border-medical-gray-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-medical-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">السماح بالتسجيل</p>
                                    <p class="text-xs text-medical-gray-500">السماح للمستخدمين الجدد بالتسجيل</p>
                                </div>
                            </div>
                            <input type="checkbox" name="user_registration_enabled" value="1"
                                {{ old('user_registration_enabled', $settings['general']['user_registration_enabled'] ?? true) ? 'checked' : '' }}
                                class="w-6 h-6 text-medical-green-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-green-100">
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-medical-blue-500 to-medical-blue-600 text-white rounded-xl hover:from-medical-blue-600 hover:to-medical-blue-700 transition-all font-bold shadow-lg hover:shadow-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Email Settings Tab --}}
        <div x-show="activeTab === 'email'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-6" style="display: none;">
            <div
                class="bg-gradient-to-br from-white to-medical-purple-50 rounded-2xl p-8 border-2 border-medical-purple-200">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-medical-gray-900">إعدادات البريد الإلكتروني</h2>
                        <p class="text-sm text-medical-gray-600">تكوين خادم SMTP لإرسال الإشعارات</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update.email') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-purple-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                                </svg>
                                خادم SMTP
                            </label>
                            <input type="text" name="smtp_host"
                                value="{{ old('smtp_host', $settings['email']['smtp_host'] ?? 'smtp.gmail.com') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-purple-100 focus:border-medical-purple-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('smtp_host') border-medical-red-500 @enderror">
                            @error('smtp_host')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-blue-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                منفذ SMTP
                            </label>
                            <input type="number" name="smtp_port"
                                value="{{ old('smtp_port', $settings['email']['smtp_port'] ?? '587') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-purple-100 focus:border-medical-purple-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('smtp_port') border-medical-red-500 @enderror">
                            @error('smtp_port')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                اسم المستخدم
                            </label>
                            <input type="text" name="smtp_username"
                                value="{{ old('smtp_username', $settings['email']['smtp_username'] ?? 'noreply@mediequip.ly') }}"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-purple-100 focus:border-medical-purple-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('smtp_username') border-medical-red-500 @enderror">
                            @error('smtp_username')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                كلمة المرور
                            </label>
                            <input type="password" name="smtp_password" placeholder="اتركه فارغاً إذا لم ترد التغيير"
                                class="w-full px-5 py-3.5 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-purple-100 focus:border-medical-purple-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('smtp_password') border-medical-red-500 @enderror">
                            @error('smtp_password')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Email Notifications Toggle --}}
                    <div class="bg-white rounded-xl p-6 border-2 border-medical-purple-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-medical-purple-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-purple-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">تفعيل الإشعارات</p>
                                    <p class="text-xs text-medical-gray-500">إرسال إشعارات البريد الإلكتروني للمستخدمين
                                    </p>
                                </div>
                            </div>
                            <input type="checkbox" name="email_notifications_enabled" value="1"
                                {{ old('email_notifications_enabled', $settings['email']['email_notifications_enabled'] ?? true) ? 'checked' : '' }}
                                class="w-6 h-6 text-medical-purple-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-purple-100">
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-4">
                        <form action="{{ route('admin.settings.email.test') }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="px-6 py-3.5 bg-white border-2 border-medical-purple-300 text-medical-purple-700 rounded-xl hover:bg-medical-purple-50 transition-all font-bold shadow-sm flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                اختبار الاتصال
                            </button>
                        </form>

                        <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-medical-purple-500 to-medical-purple-600 text-white rounded-xl hover:from-medical-purple-600 hover:to-medical-purple-700 transition-all font-bold shadow-lg hover:shadow-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Payment Settings Tab --}}
        <div x-show="activeTab === 'payment'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-6" style="display: none;">
            <div
                class="bg-gradient-to-br from-white to-medical-green-50 rounded-2xl p-8 border-2 border-medical-green-200">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-medical-gray-900">إعدادات الدفع والعمولات</h2>
                        <p class="text-sm text-medical-gray-600">إدارة نسب العمولة والحدود المالية</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update.payment') }}" method="POST" class="space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-green-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                نسبة العمولة (%)
                            </label>
                            <div class="relative">
                                <input type="number" name="commission_percentage" step="0.1"
                                    value="{{ old('commission_percentage', $settings['payment']['commission_percentage'] ?? '5') }}"
                                    class="w-full px-5 py-3.5 pr-12 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-green-100 focus:border-medical-green-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('commission_percentage') border-medical-red-500 @enderror">
                                <div
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-medical-green-600 font-black">
                                    %</div>
                            </div>
                            @error('commission_percentage')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-medical-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-yellow-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                الحد الأدنى للطلب (د.ل)
                            </label>
                            <div class="relative">
                                <input type="number" name="minimum_order_amount"
                                    value="{{ old('minimum_order_amount', $settings['payment']['minimum_order_amount'] ?? '100') }}"
                                    class="w-full px-5 py-3.5 pr-16 border-2 border-medical-gray-200 rounded-xl focus:ring-4 focus:ring-medical-green-100 focus:border-medical-green-500 transition-all duration-200 text-medical-gray-900 font-semibold @error('minimum_order_amount') border-medical-red-500 @enderror">
                                <div
                                    class="absolute left-4 top-1/2 transform -translate-y-1/2 text-medical-green-600 font-black">
                                    د.ل</div>
                            </div>
                            @error('minimum_order_amount')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Payment Info Card --}}
                    <div
                        class="bg-gradient-to-br from-medical-green-50 to-white rounded-xl p-6 border-2 border-medical-green-200">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-10 h-10 bg-medical-green-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-medical-gray-900 mb-1">معلومات العمولة</p>
                                <p class="text-sm text-medical-gray-600">يتم احتساب نسبة العمولة على كل عملية بيع تتم
                                    عبر المنصة. الحد الأدنى للطلب يحدد أقل قيمة يمكن للمشترين إتمامها.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-medical-green-500 to-medical-green-600 text-white rounded-xl hover:from-medical-green-600 hover:to-medical-green-700 transition-all font-bold shadow-lg hover:shadow-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Security Settings Tab --}}
        <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-6" style="display: none;">
            <div
                class="bg-gradient-to-br from-white to-medical-red-50 rounded-2xl p-8 border-2 border-medical-red-200">
                <div class="flex items-center gap-3 mb-6">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-red-500 to-medical-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-black text-medical-gray-900">إعدادات الأمان</h2>
                        <p class="text-sm text-medical-gray-600">تأمين المنصة وحماية البيانات</p>
                    </div>
                </div>

                <form action="{{ route('admin.settings.update.security') }}" method="POST" class="space-y-6">
                    @csrf
                    {{-- Security Options --}}
                    <div class="bg-white rounded-xl p-6 border-2 border-medical-red-200 space-y-4">
                        <div class="flex items-center justify-between pb-4 border-b-2 border-medical-gray-200">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-medical-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">المصادقة الثنائية (2FA)</p>
                                    <p class="text-xs text-medical-gray-500">طبقة أمان إضافية لتسجيل الدخول</p>
                                </div>
                            </div>
                            <input type="checkbox" name="two_factor_enabled" value="1"
                                {{ old('two_factor_enabled', $settings['security']['two_factor_enabled'] ?? false) ? 'checked' : '' }}
                                class="w-6 h-6 text-medical-blue-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-blue-100">
                        </div>

                        <div class="flex items-center justify-between pb-4 border-b-2 border-medical-gray-200">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-medical-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">تشفير SSL</p>
                                    <p class="text-xs text-medical-gray-500">تشفير الاتصال بين المستخدم والخادم</p>
                                </div>
                            </div>
                            <input type="checkbox" checked disabled
                                class="w-6 h-6 text-medical-green-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-green-100">
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 bg-medical-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-medical-gray-900">تسجيل المحاولات الفاشلة</p>
                                    <p class="text-xs text-medical-gray-500">تتبع محاولات تسجيل الدخول غير الناجحة</p>
                                </div>
                            </div>
                            <input type="checkbox" name="log_failed_attempts" value="1"
                                {{ old('log_failed_attempts', $settings['security']['log_failed_attempts'] ?? true) ? 'checked' : '' }}
                                class="w-6 h-6 text-medical-yellow-500 border-2 border-medical-gray-300 rounded-lg focus:ring-4 focus:ring-medical-yellow-100">
                        </div>
                    </div>

                    {{-- Security Alert --}}
                    <div
                        class="bg-gradient-to-br from-medical-red-50 to-white rounded-xl p-6 border-2 border-medical-red-200">
                        <div class="flex items-start gap-3">
                            <div
                                class="w-10 h-10 bg-medical-red-500 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-medical-gray-900 mb-1">تحذير أمني</p>
                                <p class="text-sm text-medical-gray-600">تأكد من تفعيل جميع إعدادات الأمان لحماية
                                    المنصة من التهديدات المحتملة. يُنصح بشدة بتفعيل المصادقة الثنائية لجميع حسابات
                                    المدراء.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="px-8 py-4 bg-gradient-to-r from-medical-red-500 to-medical-red-600 text-white rounded-xl hover:from-medical-red-600 hover:to-medical-red-700 transition-all font-bold shadow-lg hover:shadow-xl flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            حفظ الإعدادات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-dashboard.layout>
