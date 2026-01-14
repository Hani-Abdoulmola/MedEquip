<x-dashboard.layout title="تعديل الملف الشخصي" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تعديل الملف الشخصي</h1>
            <p class="mt-1 text-sm text-gray-500">تحديث معلومات المنظمة والحساب</p>
        </div>
        <a href="{{ route('buyer.profile.show') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-red-800">يرجى تصحيح الأخطاء التالية:</h4>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Organization Info Form --}}
    <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Organization Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات المنظمة</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Organization Name --}}
                <div>
                    <label for="organization_name" class="block text-sm font-medium text-gray-700 mb-1">
                        اسم المنظمة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="organization_name" 
                           name="organization_name" 
                           value="{{ old('organization_name', $buyer->organization_name) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('organization_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Organization Type --}}
                <div>
                    <label for="organization_type" class="block text-sm font-medium text-gray-700 mb-1">
                        نوع المنظمة <span class="text-red-500">*</span>
                    </label>
                    <select id="organization_type" 
                            name="organization_type" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        @foreach($organizationTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('organization_type', $buyer->organization_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('organization_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- License Number --}}
                <div>
                    <label for="license_number" class="block text-sm font-medium text-gray-700 mb-1">
                        رقم الترخيص <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="license_number" 
                           name="license_number" 
                           value="{{ old('license_number', $buyer->license_number) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('license_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Location Details --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">الموقع</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Country --}}
                <div>
                    <label for="country" class="block text-sm font-medium text-gray-700 mb-1">
                        الدولة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="country" 
                           name="country" 
                           value="{{ old('country', $buyer->country) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('country')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- City --}}
                <div>
                    <label for="city" class="block text-sm font-medium text-gray-700 mb-1">
                        المدينة <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="city" 
                           name="city" 
                           value="{{ old('city', $buyer->city) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('city')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-1">
                        العنوان التفصيلي <span class="text-red-500">*</span>
                    </label>
                    <textarea id="address" 
                              name="address" 
                              rows="2"
                              required
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">{{ old('address', $buyer->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Contact Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Contact Email --}}
                <div>
                    <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-1">
                        البريد الإلكتروني للتواصل <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="contact_email" 
                           name="contact_email" 
                           value="{{ old('contact_email', $buyer->contact_email) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('contact_email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Contact Phone --}}
                <div>
                    <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-1">
                        رقم الهاتف <span class="text-red-500">*</span>
                    </label>
                    <input type="tel" 
                           id="contact_phone" 
                           name="contact_phone" 
                           value="{{ old('contact_phone', $buyer->contact_phone) }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('contact_phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- License Documents --}}
        <div id="documents" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">وثائق الترخيص</h2>
            
            {{-- Existing Documents --}}
            @if($licenseDocuments->count() > 0)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">الوثائق الحالية</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($licenseDocuments as $document)
                    <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-gray-200 rounded flex items-center justify-center text-gray-500">
                            @if(str_contains($document->mime_type, 'pdf'))
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                            @else
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-gray-900 truncate">{{ $document->file_name }}</div>
                            <div class="text-xs text-gray-500">{{ number_format($document->size / 1024, 2) }} KB</div>
                        </div>
                        <form action="{{ route('buyer.profile.delete-document', $document->id) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من حذف هذه الوثيقة؟')"
                                    class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Upload New Documents --}}
            <div>
                <label for="license_documents" class="block text-sm font-medium text-gray-700 mb-1">
                    رفع وثائق جديدة
                </label>
                <input type="file" 
                       id="license_documents" 
                       name="license_documents[]" 
                       multiple
                       accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                <p class="mt-1 text-xs text-gray-500">PDF, JPG, PNG - الحد الأقصى 5 ميجابايت لكل ملف</p>
                @error('license_documents')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Submit --}}
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('buyer.profile.show') }}" 
               class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                إلغاء
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                حفظ التغييرات
            </button>
        </div>
    </form>

    {{-- Password Change --}}
    <form id="password" action="{{ route('buyer.profile.update-password') }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        @csrf
        @method('PUT')
        
        <h2 class="text-lg font-semibold text-gray-900 mb-4">تغيير كلمة المرور</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Current Password --}}
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                    كلمة المرور الحالية <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="current_password" 
                       name="current_password" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- New Password --}}
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                    كلمة المرور الجديدة <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirm Password --}}
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    تأكيد كلمة المرور <span class="text-red-500">*</span>
                </label>
                <input type="password" 
                       id="password_confirmation" 
                       name="password_confirmation" 
                       required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
            </div>
        </div>

        <div class="mt-4 flex justify-end">
            <button type="submit" 
                    class="px-6 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-900 transition-colors font-medium">
                تغيير كلمة المرور
            </button>
        </div>
    </form>
</div>
</x-dashboard.layout>

