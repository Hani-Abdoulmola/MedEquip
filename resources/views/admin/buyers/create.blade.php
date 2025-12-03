{{-- Admin Buyers Management - Create New Buyer --}}
<x-dashboard.layout title="إضافة مشتري جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{--
    ADMIN BUYERS CREATE PAGE - Form Page Pattern
    Controller: BuyerController@create, BuyerController@store
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة مشتري جديد</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء حساب مشتري جديد في النظام</p>
            </div>
            <a href="{{ route('admin.buyers') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Create Buyer Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.buyers.store') }}">
            @csrf

            {{-- ================================
                User Account Section (NEW)
            ================================= --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    معلومات الحساب المرتبط بالمشتري
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- User Name --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            اسم المستخدم <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('name') border-red-500 @enderror">

                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            البريد الإلكتروني للمستخدم <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('email') border-red-500 @enderror">

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            رقم الهاتف للمستخدم <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('phone') border-red-500 @enderror">

                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            كلمة المرور <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('password') border-red-500 @enderror">

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>


            {{-- ================================
                Basic Information Section
            ================================= --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    المعلومات الأساسية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Organization Name --}}
                    <div>
                        <label for="organization_name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            اسم المؤسسة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="organization_name" name="organization_name"
                            value="{{ old('organization_name') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('organization_name') border-red-500 @enderror">
                        @error('organization_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Organization Type --}}
                    <div>
                        <label for="organization_type" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            نوع المؤسسة <span class="text-red-500">*</span>
                        </label>
                        <select id="organization_type" name="organization_type" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('organization_type') border-red-500 @enderror">

                            <option value="">اختر النوع</option>
                            <option value="hospital" {{ old('organization_type') == 'hospital' ? 'selected' : '' }}>
                                مستشفى</option>
                            <option value="clinic" {{ old('organization_type') == 'clinic' ? 'selected' : '' }}>عيادة
                            </option>
                            <option value="pharmacy" {{ old('organization_type') == 'pharmacy' ? 'selected' : '' }}>
                                صيدلية</option>

                        </select>

                        @error('organization_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- License Number --}}
                    <div>
                        <label for="license_number" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            رقم الترخيص (اختياري)
                        </label>
                        <input type="text" id="license_number" name="license_number"
                            value="{{ old('license_number') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('license_number') border-red-500 @enderror">
                        @error('license_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Email --}}
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="contact_email" name="contact_email"
                            value="{{ old('contact_email') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('contact_email') border-red-500 @enderror">
                        @error('contact_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Phone --}}
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            رقم الهاتف <span class="text-red-500">*</span>
                        </label>
                        <input type="tel" id="contact_phone" name="contact_phone"
                            value="{{ old('contact_phone') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('contact_phone') border-red-500 @enderror">
                        @error('contact_phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            العنوان <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('address') border-red-500 @enderror">{{ old('address') }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المدينة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('city') border-red-500 @enderror">

                        @error('city')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الدولة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="country" name="country" value="{{ old('country', 'ليبيا') }}"
                            required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('country') border-red-500 @enderror">

                        @error('country')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Verification Documents --}}
                    <div class="md:col-span-2">
                        <label for="verification_documents"
                            class="block text-sm font-medium text-medical-gray-700 mb-2">
                            مستندات التوثيق <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="verification_documents[]" id="verification_documents" multiple
                            accept="image/*,application/pdf" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2
                                   focus:ring-medical-blue-500 focus:border-medical-blue-500
                                   @error('verification_documents') border-red-500 @enderror">
                        <div class="text-xs text-medical-gray-500 mt-1">
                            يرجى رفع مستندات رسمية مثل رخصة العمل، شهادة تسجيل المؤسسة، مستندات التوثيق ذات الصلة (يسمح
                            بصيغ: PDF، JPG، PNG)
                        </div>
                        @error('verification_documents')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @if ($errors->has('verification_documents.*'))
                            <ul class="mt-2 text-sm text-red-600">
                                @foreach ($errors->get('verification_documents.*') as $messages)
                                    @foreach ($messages as $message)
                                        <li>{{ $message }}</li>
                                    @endforeach
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>


            {{-- ================================
                Status & Verification
            ================================= --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    الحالة والتوثيق
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Is Active --}}
                    <div>
                        <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', true) ? 'checked' : '' }}
                                class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded
                                focus:ring-2 focus:ring-medical-blue-500">

                            <span class="text-sm font-medium text-medical-gray-700">حساب نشط</span>
                        </label>
                    </div>

                    {{-- Is Verified --}}
                    <div>
                        <input type="hidden" name="is_verified" value="0">
                        <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                            <input type="checkbox" name="is_verified" value="1"
                                {{ old('is_verified', true) ? 'checked' : '' }}
                                class="w-5 h-5 text-medical-green-600 border-medical-gray-300 rounded
                                focus:ring-2 focus:ring-medical-green-500">
                            <span class="text-sm font-medium text-medical-gray-700">موثق</span>
                        </label>
                    </div>

                </div>
            </div>


            {{-- ================================
                Form Actions
            ================================= --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.buyers') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl
                    hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إلغاء
                </a>

                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl
                    hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">

                    <span class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span>حفظ المشتري</span>
                    </span>
                </button>
            </div>

        </form>
    </div>

</x-dashboard.layout>
