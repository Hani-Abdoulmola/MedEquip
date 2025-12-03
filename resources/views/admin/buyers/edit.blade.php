{{-- Admin Buyers Management - Edit Buyer --}}
<x-dashboard.layout title="تعديل بيانات المشتري" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900">تعديل بيانات المشتري</h1>
                <p class="mt-2 text-medical-gray-600">تحديث معلومات المشتري: {{ $buyer->organization_name }}</p>
            </div>

            <a href="{{ route('admin.buyers.show', $buyer) }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200">
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

            {{-- BASIC INFORMATION --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">المعلومات الأساسية</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Organization Name --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">اسم المؤسسة *</label>
                        <input type="text" name="organization_name"
                            value="{{ old('organization_name', $buyer->organization_name) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('organization_name') border-red-500 @enderror">
                        @error('organization_name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Organization Type --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">نوع المؤسسة *</label>
                        <select name="organization_type" required
                            class="w-full px-4 py-3 border rounded-xl @error('organization_type') border-red-500 @enderror">
                            <option value="hospital" {{ $buyer->organization_type == 'hospital' ? 'selected' : '' }}>
                                مستشفى</option>
                            <option value="clinic" {{ $buyer->organization_type == 'clinic' ? 'selected' : '' }}>عيادة
                            </option>
                            <option value="pharmacy" {{ $buyer->organization_type == 'pharmacy' ? 'selected' : '' }}>
                                صيدلية</option>
                        </select>
                        @error('organization_type')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- License Number --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">رقم الترخيص</label>
                        <input type="text" name="license_number"
                            value="{{ old('license_number', $buyer->license_number) }}"
                            class="w-full px-4 py-3 border rounded-xl">
                    </div>

                    {{-- Contact Email --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">البريد الإلكتروني *</label>
                        <input type="email" name="contact_email"
                            value="{{ old('contact_email', $buyer->contact_email) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('contact_email') border-red-500 @enderror">
                        @error('contact_email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Phone --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">رقم الهاتف *</label>
                        <input type="tel" name="contact_phone"
                            value="{{ old('contact_phone', $buyer->contact_phone) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('contact_phone') border-red-500 @enderror">
                        @error('contact_phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">العنوان *</label>
                        <textarea name="address" rows="3" required
                            class="w-full px-4 py-3 border rounded-xl @error('address') border-red-500 @enderror">{{ old('address', $buyer->address) }}</textarea>
                        @error('address')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">المدينة *</label>
                        <input type="text" name="city" value="{{ old('city', $buyer->city) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('city') border-red-500 @enderror">
                        @error('city')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">الدولة *</label>
                        <input type="text" name="country" value="{{ old('country', $buyer->country) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('country') border-red-500 @enderror">
                        @error('country')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Verification Documents --}}
                    <div class="md:col-span-2">
                        <label for="verification_documents"
                            class="block text-sm font-medium text-medical-gray-700 mb-2">
                            مستندات التوثيق <span class="text-red-500">*</span>
                        </label>
                        <input type="file" name="verification_documents[]" id="verification_documents" multiple
                            accept="image/*,application/pdf"
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
                        @if (!empty($buyer->verification_documents) && is_array($buyer->verification_documents))
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-medical-gray-700 mb-1">المستندات
                                    الحالية:</label>
                                <ul class="list-disc pl-5">
                                    @foreach ($buyer->verification_documents as $document)
                                        <li class="mb-1">
                                            <a href="{{ asset('storage/' . $document) }}" target="_blank"
                                                class="text-medical-blue-600 underline">
                                                {{ basename($document) }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="text-xs text-medical-gray-400 mt-1">
                                    رفع ملفات جديدة سيضيف للمرفقات الحالية أو يستبدل حسب منطق السيرفر.
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- ACCOUNT SECTION (USER) --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">بيانات الحساب</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- User Name --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">اسم المستخدم *</label>
                        <input type="text" name="name" value="{{ old('name', $buyer->user->name) }}" required
                            class="w-full px-4 py-3 border rounded-xl @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">البريد الإلكتروني للحساب
                            *</label>
                        <input type="email" name="email" value="{{ old('email', $buyer->user->email) }}"
                            required
                            class="w-full px-4 py-3 border rounded-xl @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            {{-- STATUS --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">الحالة والتوثيق</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1"
                                {{ $buyer->is_active ? 'checked' : '' }}>
                            <span>حساب نشط</span>
                        </label>
                    </div>

                    <div>
                        <input type="hidden" name="is_verified" value="0">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_verified" value="1"
                                {{ old('is_verified', $buyer->is_verified) ? 'checked' : '' }}>
                            <span>موثق</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- SUBMIT --}}
            <div class="flex justify-end space-x-4 space-x-reverse pt-6 border-t">
                <a href="{{ route('admin.buyers') }}" class="px-6 py-3 bg-medical-gray-100 rounded-xl">إلغاء</a>

                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">
                    تحديث البيانات
                </button>
            </div>

        </form>
    </div>

</x-dashboard.layout>
