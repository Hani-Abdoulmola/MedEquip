{{-- Admin Settings - System Configuration --}}
<x-dashboard.layout title="إعدادات النظام" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إعدادات النظام</h1>
            <p class="mt-2 text-medical-gray-600">إدارة إعدادات وتكوينات المنصة</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Settings Navigation --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-medical p-4">
                <nav class="space-y-2">
                    <a href="#general" class="flex items-center space-x-3 space-x-reverse px-4 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span class="font-medium">إعدادات عامة</span>
                    </a>
                    <a href="#email" class="flex items-center space-x-3 space-x-reverse px-4 py-3 text-medical-gray-700 hover:bg-medical-gray-50 rounded-xl transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span class="font-medium">البريد الإلكتروني</span>
                    </a>
                    <a href="#payment" class="flex items-center space-x-3 space-x-reverse px-4 py-3 text-medical-gray-700 hover:bg-medical-gray-50 rounded-xl transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                        <span class="font-medium">الدفع</span>
                    </a>
                    <a href="#notifications" class="flex items-center space-x-3 space-x-reverse px-4 py-3 text-medical-gray-700 hover:bg-medical-gray-50 rounded-xl transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <span class="font-medium">الإشعارات</span>
                    </a>
                    <a href="#security" class="flex items-center space-x-3 space-x-reverse px-4 py-3 text-medical-gray-700 hover:bg-medical-gray-50 rounded-xl transition-colors duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span class="font-medium">الأمان</span>
                    </a>
                </nav>
            </div>
        </div>

        {{-- Settings Content --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- General Settings --}}
            <div id="general" class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6">الإعدادات العامة</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">اسم المنصة</label>
                            <input type="text" value="MediEquip"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">البريد الإلكتروني للدعم</label>
                            <input type="email" value="support@mediequip.ly"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">رقم الهاتف</label>
                            <input type="tel" value="+218 21 123 4567"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">العملة الافتراضية</label>
                            <select class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="LYD" selected>دينار ليبي (LYD)</option>
                                <option value="USD">دولار أمريكي (USD)</option>
                                <option value="EUR">يورو (EUR)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">اللغة الافتراضية</label>
                            <select class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="ar" selected>العربية</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">المنطقة الزمنية</label>
                            <select class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="Africa/Tripoli" selected>طرابلس (GMT+2)</option>
                                <option value="UTC">UTC</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">وصف المنصة</label>
                        <textarea rows="4"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">منصة MediEquip هي منصة B2B لتوريد المعدات الطبية في ليبيا، تربط الموردين بالمشترين من المستشفيات والعيادات والصيدليات.</textarea>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse">
                        <input type="checkbox" id="maintenance_mode" class="w-5 h-5 text-medical-blue-500 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                        <label for="maintenance_mode" class="text-sm font-medium text-medical-gray-700">
                            وضع الصيانة (إيقاف المنصة مؤقتاً)
                        </label>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse">
                        <input type="checkbox" id="user_registration" checked class="w-5 h-5 text-medical-blue-500 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                        <label for="user_registration" class="text-sm font-medium text-medical-gray-700">
                            السماح بتسجيل مستخدمين جدد
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl hover:shadow-medical-lg transition-all duration-200 font-medium">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            {{-- Email Settings --}}
            <div id="email" class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6">إعدادات البريد الإلكتروني</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">خادم SMTP</label>
                            <input type="text" value="smtp.gmail.com"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">منفذ SMTP</label>
                            <input type="number" value="587"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">اسم المستخدم</label>
                            <input type="text" value="noreply@mediequip.ly"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">كلمة المرور</label>
                            <input type="password" value="••••••••"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <div class="flex items-center space-x-3 space-x-reverse">
                        <input type="checkbox" id="email_notifications" checked class="w-5 h-5 text-medical-blue-500 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                        <label for="email_notifications" class="text-sm font-medium text-medical-gray-700">
                            تفعيل إشعارات البريد الإلكتروني
                        </label>
                    </div>

                    <div class="flex justify-end space-x-3 space-x-reverse">
                        <button type="button"
                            class="px-6 py-3 border border-medical-gray-300 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 transition-colors duration-200 font-medium">
                            اختبار الاتصال
                        </button>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl hover:shadow-medical-lg transition-all duration-200 font-medium">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>

            {{-- Payment Settings --}}
            <div id="payment" class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6">إعدادات الدفع</h3>
                <form class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">نسبة العمولة (%)</label>
                            <input type="number" value="5" step="0.1"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحد الأدنى للطلب (د.ل)</label>
                            <input type="number" value="100"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl hover:shadow-medical-lg transition-all duration-200 font-medium">
                            حفظ التغييرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-dashboard.layout>

