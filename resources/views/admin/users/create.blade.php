{{-- Admin Users Management - Create New User --}}
<x-dashboard.layout title="إضافة مستخدم جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- 
    ADMIN USERS CREATE PAGE - Form Page Pattern Template
    Controller: UserController@create, UserController@store
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة مستخدم جديد</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء حساب مستخدم جديد في النظام</p>
            </div>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Create User Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8" x-data="{ showPassword: false }">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">المعلومات الأساسية</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Full Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الاسم الكامل <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('phone') border-red-500 @enderror"
                            placeholder="+218 91 234 5678">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- User Type --}}
                    <div>
                        <label for="user_type_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            نوع المستخدم <span class="text-red-500">*</span>
                        </label>
                        <select id="user_type_id" name="user_type_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('user_type_id') border-red-500 @enderror">
                            <option value="">اختر نوع المستخدم</option>
                            <option value="1" {{ old('user_type_id') == '1' ? 'selected' : '' }}>مدير النظام</option>
                            <option value="2" {{ old('user_type_id') == '2' ? 'selected' : '' }}>مورد</option>
                            <option value="3" {{ old('user_type_id') == '3' ? 'selected' : '' }}>مشتري</option>
                        </select>
                        @error('user_type_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Password Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">كلمة المرور</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input :type="showPassword ? 'text' : 'password'" id="password" name="password" required
                                class="w-full px-4 py-3 pl-12 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('password') border-red-500 @enderror">
                            <button type="button" @click="showPassword = !showPassword"
                                class="absolute left-4 top-1/2 transform -translate-y-1/2 text-medical-gray-400 hover:text-medical-gray-600 transition-colors duration-200">
                                <svg x-show="!showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-medical-gray-500">يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل</p>
                    </div>

                    {{-- Password Confirmation --}}
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            تأكيد كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <input :type="showPassword ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                    </div>
                </div>
            </div>

            {{-- Account Settings Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">إعدادات الحساب</h2>
                
                <div class="space-y-4">
                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            حالة الحساب <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-red-500 @enderror">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>موقوف</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email Verification --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="email_verified" name="email_verified" value="1" {{ old('email_verified') ? 'checked' : '' }}
                            class="w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                        <label for="email_verified" class="mr-3 text-sm font-medium text-medical-gray-700">
                            تفعيل البريد الإلكتروني تلقائياً
                        </label>
                    </div>

                    {{-- Send Welcome Email --}}
                    <div class="flex items-center">
                        <input type="checkbox" id="send_welcome_email" name="send_welcome_email" value="1" {{ old('send_welcome_email', '1') ? 'checked' : '' }}
                            class="w-4 h-4 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                        <label for="send_welcome_email" class="mr-3 text-sm font-medium text-medical-gray-700">
                            إرسال بريد إلكتروني ترحيبي للمستخدم
                        </label>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.users') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>إنشاء المستخدم</span>
                </button>
            </div>
        </form>
    </div>

</x-dashboard.layout>
