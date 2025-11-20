{{-- Admin Buyers Management - Edit Buyer --}}
<x-dashboard.layout title="تعديل بيانات المشتري" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{--
    ADMIN BUYERS EDIT PAGE - Form Page Pattern
    Controller: BuyerController@edit, BuyerController@update
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل بيانات المشتري</h1>
                <p class="mt-2 text-medical-gray-600">تحديث معلومات المشتري: {{ $buyer->organization_name }}</p>
            </div>
            <a href="{{ route('admin.buyers.show', $buyer) }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للتفاصيل</span>
            </a>
        </div>
    </div>

    {{-- Edit Buyer Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.buyers.update', $buyer) }}">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">المعلومات
                    الأساسية</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Organization Name --}}
                    <div>
                        <label for="organization_name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            اسم المؤسسة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="organization_name" name="organization_name"
                            value="{{ old('organization_name', $buyer->organization_name) }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('organization_name') border-red-500 @enderror">
                        @error('organization_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Person --}}
                    <div>
                        <label for="contact_person" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الشخص المسؤول <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="contact_person" name="contact_person"
                            value="{{ old('contact_person', $buyer->contact_person) }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('contact_person') border-red-500 @enderror">
                        @error('contact_person')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $buyer->email) }}"
                            required
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
                        <input type="tel" id="phone" name="phone" value="{{ old('phone', $buyer->phone) }}"
                            required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            العنوان <span class="text-red-500">*</span>
                        </label>
                        <textarea id="address" name="address" rows="3" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('address') border-red-500 @enderror">{{ old('address', $buyer->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label for="city" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المدينة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="city" name="city" value="{{ old('city', $buyer->city) }}"
                            required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label for="country" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الدولة <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="country" name="country"
                            value="{{ old('country', $buyer->country ?? 'ليبيا') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('country') border-red-500 @enderror">
                        @error('country')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Status Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">الحالة
                    والتوثيق</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Is Active --}}
                    <div>
                        <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                {{ old('is_active', $buyer->is_active) ? 'checked' : '' }}
                                class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                            <span class="text-sm font-medium text-medical-gray-700">حساب نشط</span>
                        </label>
                    </div>

                    {{-- Is Verified --}}
                    <div>
                        <label class="flex items-center space-x-3 space-x-reverse cursor-pointer">
                            <input type="checkbox" name="is_verified" value="1"
                                {{ old('is_verified', $buyer->is_verified) ? 'checked' : '' }}
                                class="w-5 h-5 text-medical-green-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-green-500">
                            <span class="text-sm font-medium text-medical-gray-700">موثق</span>
                        </label>
                    </div>
                </div>
            </div>




            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.buyers') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <span class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span>تحديث البيانات</span>
                    </span>
                </button>
            </div>
        </form>
    </div>

</x-dashboard.layout>
