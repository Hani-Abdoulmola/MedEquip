{{-- Supplier Profile - Show --}}
<x-dashboard.layout title="الملف الشخصي" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الملف الشخصي</h1>
                <p class="mt-2 text-medical-gray-600">عرض وإدارة معلومات شركتك</p>
            </div>
            <a href="{{ route('supplier.profile.edit') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>تعديل الملف الشخصي</span>
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
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
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">اسم الشركة</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">السجل التجاري</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->commercial_register ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">الرقم الضريبي</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->tax_number ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">حالة التحقق</p>
                        @if($supplier->is_verified)
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-green-100 text-medical-green-700">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                موثق
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                </svg>
                                قيد المراجعة
                            </span>
                        @endif
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
                        <p class="text-sm text-medical-gray-500 mb-1">البريد الإلكتروني</p>
                        <a href="mailto:{{ $supplier->contact_email }}" class="font-semibold text-medical-blue-600 hover:underline">
                            {{ $supplier->contact_email }}
                        </a>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">رقم الهاتف</p>
                        @if($supplier->contact_phone)
                            <a href="tel:{{ $supplier->contact_phone }}" class="font-semibold text-medical-blue-600 hover:underline">
                                {{ $supplier->contact_phone }}
                            </a>
                        @else
                            <p class="font-semibold text-medical-gray-400">غير محدد</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">الدولة</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->country ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">المدينة</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->city ?? 'غير محدد' }}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-sm text-medical-gray-500 mb-1">العنوان</p>
                        <p class="font-semibold text-medical-gray-900">{{ $supplier->address ?? 'غير محدد' }}</p>
                    </div>
                </div>
            </div>

            {{-- Verification Documents --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-medical-gray-900">مستندات التحقق</h2>
                            <p class="text-sm text-medical-gray-500">الوثائق الرسمية للشركة</p>
                        </div>
                    </div>
                </div>

                {{-- Upload Form --}}
                <form action="{{ route('supplier.profile.upload-document') }}" method="POST" enctype="multipart/form-data" class="mb-6">
                    @csrf
                    <div class="flex items-center gap-4">
                        <div class="flex-1">
                            <input type="file" name="document" id="document" 
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500"
                                accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                        <button type="submit"
                            class="px-6 py-3 bg-medical-purple-600 text-white rounded-xl hover:bg-medical-purple-700 transition-all duration-200 font-medium">
                            رفع مستند
                        </button>
                    </div>
                    <p class="text-xs text-medical-gray-500 mt-2">يُقبل PDF والصور فقط. الحد الأقصى: 5 ميجابايت</p>
                </form>

                {{-- Documents List --}}
                @if($verificationDocuments->count() > 0)
                    <div class="space-y-3">
                        @foreach($verificationDocuments as $document)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-medical-red-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-medical-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="font-medium text-medical-gray-900">{{ $document->file_name }}</p>
                                        <p class="text-xs text-medical-gray-500">{{ $document->human_readable_size }} • {{ $document->created_at->format('Y-m-d') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ $document->getUrl() }}" target="_blank"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('supplier.profile.delete-document', $document->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المستند؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-medical-gray-500">
                        <svg class="mx-auto h-12 w-12 text-medical-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p>لم يتم رفع أي مستندات بعد</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Profile Card --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <div class="text-center">
                    {{-- Logo/Avatar --}}
                    <div class="w-24 h-24 mx-auto mb-4 rounded-2xl bg-medical-gray-100 flex items-center justify-center overflow-hidden">
                        @if($supplierImages->count() > 0)
                            <img src="{{ $supplierImages->first()->getUrl('thumb') }}" alt="{{ $supplier->company_name }}" class="w-full h-full object-cover">
                        @else
                            <svg class="w-12 h-12 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        @endif
                    </div>
                    <h3 class="text-xl font-bold text-medical-gray-900">{{ $supplier->company_name }}</h3>
                    <p class="text-medical-gray-500 mt-1">{{ $supplier->city ?? '' }}{{ $supplier->city && $supplier->country ? '، ' : '' }}{{ $supplier->country ?? '' }}</p>
                    
                    @if($supplier->is_verified)
                        <div class="mt-4 inline-flex items-center px-4 py-2 bg-medical-green-50 text-medical-green-700 rounded-full text-sm font-medium">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            مورد موثق
                        </div>
                    @endif
                </div>
            </div>

            {{-- Account Information --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات الحساب</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">اسم المستخدم</p>
                        <p class="font-semibold text-medical-gray-900">{{ $user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">البريد الإلكتروني</p>
                        <p class="font-semibold text-medical-gray-900">{{ $user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">تاريخ التسجيل</p>
                        <p class="font-semibold text-medical-gray-900">{{ $user->created_at->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>

            {{-- Change Password --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">تغيير كلمة المرور</h3>
                <form action="{{ route('supplier.profile.update-password') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')
                    
                    <div>
                        <label for="current_password" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            كلمة المرور الحالية
                        </label>
                        <input type="password" name="current_password" id="current_password" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            كلمة المرور الجديدة
                        </label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    </div>
                    
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            تأكيد كلمة المرور
                        </label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    </div>
                    
                    <button type="submit"
                        class="w-full px-6 py-3 bg-medical-gray-800 text-white rounded-xl hover:bg-medical-gray-900 transition-all duration-200 font-medium">
                        تغيير كلمة المرور
                    </button>
                </form>
            </div>
        </div>
    </div>

</x-dashboard.layout>

