@extends('layouts.app')

@section('content')
    {{--
    ADMIN USER EDIT PAGE - Form Page Pattern Template
    Controller Integration: UserController@update
    Validation: UserRequest
    Form Action: route('admin.users.update', $user->id)
--}}

    <div class="max-w-4xl mx-auto px-6 py-8" x-data="{ showPassword: false, showPasswordConfirm: false }">

        {{-- Page Header --}}
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="{{ route('admin.users') }}"
                    class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg active:scale-95 transition-all duration-200 cursor-pointer">
                    <i class="fas fa-arrow-right text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">تعديل بيانات المستخدم</h1>
                    <p class="text-gray-600 mt-1">تحديث معلومات المستخدم في النظام</p>
                </div>
                <a href="{{ route('admin.users') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
        </div>

        {{-- Edit User Form --}}
        <div class="bg-white rounded-2xl shadow-medical p-8">
            <form action="#" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Full Name --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">الاسم الكامل *</label>
                        <input type="text" name="name" value="أحمد محمد" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="email" value="ahmed@example.com" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">رقم الهاتف *</label>
                        <input type="tel" name="phone" value="+218 91 234 5678" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    {{-- User Type --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">نوع المستخدم *</label>
                        <select name="user_type_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="1" selected>مدير</option>
                            <option value="2">مورد</option>
                            <option value="3">مشتري</option>
                        </select>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة *</label>
                        <select name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                            <option value="active" selected>نشط</option>
                            <option value="inactive">غير نشط</option>
                            <option value="pending">قيد المراجعة</option>
                        </select>
                    </div>

                    {{-- Email Verified --}}
                    <div class="flex items-center">
                        <input type="checkbox" name="email_verified" id="email_verified" checked
                            class="w-5 h-5 text-medical-blue-500 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                        <label for="email_verified" class="mr-3 text-sm font-medium text-medical-gray-700">
                            البريد الإلكتروني موثق
                        </label>
                    </div>
                </div>

                {{-- Password Change Section --}}
                <div class="mt-8 pt-8 border-t border-medical-gray-200">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">تغيير كلمة المرور (اختياري)</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- New Password --}}
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">كلمة المرور الجديدة</label>
                            <input type="password" name="password"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="اتركه فارغاً إذا لم ترغب في التغيير">
                        </div>

                        {{-- Confirm New Password --}}
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200"
                                placeholder="تأكيد كلمة المرور الجديدة">
                        </div>
                    </div>
                </div>

                {{-- Additional Information Section --}}
                <div class="mt-8 pt-8 border-t border-medical-gray-200">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات إضافية</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Address --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">العنوان</label>
                            <textarea name="address" rows="3"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">شارع الجمهورية، طرابلس</textarea>
                        </div>

                        {{-- City --}}
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">المدينة</label>
                            <input type="text" name="city" value="طرابلس"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        {{-- Country --}}
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">الدولة</label>
                            <input type="text" name="country" value="ليبيا"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        </div>

                        {{-- Notes --}}
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">ملاحظات</label>
                            <textarea name="notes" rows="3"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">مستخدم موثوق</textarea>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="mt-8 flex items-center justify-between">
                    <button type="button"
                        class="px-6 py-3 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 transition-colors duration-200 font-medium">
                        حذف المستخدم
                    </button>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <a href="{{ route('admin.users') }}"
                            class="px-6 py-3 border border-medical-gray-300 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 transition-colors duration-200 font-medium">
                            إلغاء
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl hover:shadow-medical-lg transition-all duration-200 font-medium">
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </form>
        </div>

        </x-dashboard.layout>
