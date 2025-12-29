{{-- Supplier Profile - Edit --}}
<x-dashboard.layout title="تعديل الملف الشخصي" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('supplier.profile.show') }}"
                class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-medical-gray-100 hover:bg-medical-gray-200 transition-colors">
                <svg class="w-5 h-5 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل الملف الشخصي</h1>
                <p class="mt-1 text-medical-gray-600">تحديث معلومات شركتك وبيانات التواصل</p>
            </div>
        </div>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="mb-6 p-4 bg-medical-red-50 border border-medical-red-200 rounded-xl text-medical-red-700">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('supplier.profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Company Information --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-medical-gray-900">معلومات الشركة</h2>
                            <p class="text-sm text-medical-gray-500">البيانات الأساسية للشركة</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="company_name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                اسم الشركة <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="text" name="company_name" id="company_name" required
                                value="{{ old('company_name', $supplier->company_name) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="commercial_register" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                رقم السجل التجاري
                            </label>
                            <input type="text" name="commercial_register" id="commercial_register"
                                value="{{ old('commercial_register', $supplier->commercial_register) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="tax_number" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                الرقم الضريبي
                            </label>
                            <input type="text" name="tax_number" id="tax_number"
                                value="{{ old('tax_number', $supplier->tax_number) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-medical-gray-900">معلومات التواصل</h2>
                            <p class="text-sm text-medical-gray-500">بيانات الاتصال والموقع</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                البريد الإلكتروني للتواصل <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="email" name="contact_email" id="contact_email" required
                                value="{{ old('contact_email', $supplier->contact_email) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                رقم الهاتف
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone"
                                value="{{ old('contact_phone', $supplier->contact_phone) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="country" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                الدولة
                            </label>
                            <input type="text" name="country" id="country"
                                value="{{ old('country', $supplier->country) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="city" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                المدينة
                            </label>
                            <input type="text" name="city" id="city"
                                value="{{ old('city', $supplier->city) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                العنوان
                            </label>
                            <textarea name="address" id="address" rows="3"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">{{ old('address', $supplier->address) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Account Information --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-medical-gray-900">معلومات الحساب</h2>
                            <p class="text-sm text-medical-gray-500">بيانات تسجيل الدخول</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                اسم المستخدم <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                value="{{ old('name', $user->name) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                البريد الإلكتروني <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                value="{{ old('email', $user->email) }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Logo Upload --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">شعار الشركة</h3>
                    
                    <div class="text-center">
                        {{-- Current Logo Preview --}}
                        <div class="w-32 h-32 mx-auto mb-4 rounded-2xl bg-medical-gray-100 flex items-center justify-center overflow-hidden" id="logo-preview">
                            @php
                                $currentLogo = $supplier->getFirstMediaUrl('supplier_images', 'thumb');
                            @endphp
                            @if($currentLogo)
                                <img src="{{ $currentLogo }}" alt="شعار الشركة" class="w-full h-full object-cover" id="logo-image">
                            @else
                                <svg class="w-16 h-16 text-medical-gray-400" id="logo-placeholder" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            @endif
                        </div>

                        <input type="file" name="logo" id="logo" class="hidden" accept="image/jpeg,image/png,image/jpg">
                        <label for="logo"
                            class="inline-flex items-center px-4 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-colors cursor-pointer font-medium">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            اختيار صورة
                        </label>
                        <p class="text-xs text-medical-gray-500 mt-2">JPEG, PNG. الحد الأقصى: 2 ميجابايت</p>
                    </div>
                </div>

                {{-- Verification Status --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">حالة الحساب</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-xl">
                            <span class="text-medical-gray-600">حالة التحقق</span>
                            @if($supplier->is_verified)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-green-100 text-medical-green-700">
                                    موثق
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                    قيد المراجعة
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-xl">
                            <span class="text-medical-gray-600">حالة النشاط</span>
                            @if($supplier->is_active)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-green-100 text-medical-green-700">
                                    نشط
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-red-100 text-medical-red-700">
                                    غير نشط
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <button type="submit"
                        class="w-full px-6 py-4 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold text-lg">
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('supplier.profile.show') }}"
                        class="mt-3 w-full inline-flex justify-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                        إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        // Logo preview
        document.getElementById('logo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('logo-preview');
                    const placeholder = document.getElementById('logo-placeholder');
                    let image = document.getElementById('logo-image');
                    
                    if (placeholder) {
                        placeholder.style.display = 'none';
                    }
                    
                    if (!image) {
                        image = document.createElement('img');
                        image.id = 'logo-image';
                        image.className = 'w-full h-full object-cover';
                        preview.appendChild(image);
                    }
                    
                    image.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
    @endpush

</x-dashboard.layout>

