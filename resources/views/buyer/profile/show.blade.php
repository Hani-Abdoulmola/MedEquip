<x-dashboard.layout title="الملف الشخصي" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">الملف الشخصي</h1>
            <p class="mt-1 text-sm text-gray-500">معلومات المنظمة والحساب</p>
        </div>
        <a href="{{ route('buyer.profile.edit') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            تعديل الملف
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Info --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Organization Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات المنظمة</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">اسم المنظمة</dt>
                        <dd class="text-gray-900 font-medium mt-1">{{ $buyer->organization_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">نوع المنظمة</dt>
                        <dd class="text-gray-900 font-medium mt-1">{{ $buyer->organization_type_label }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">رقم الترخيص</dt>
                        <dd class="text-gray-900 font-medium font-mono mt-1">{{ $buyer->license_number }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">حالة التحقق</dt>
                        <dd class="mt-1">
                            @if($buyer->is_verified)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    موثق
                                </span>
                            @elseif($buyer->rejection_reason)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    مرفوض
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    قيد المراجعة
                                </span>
                            @endif
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Location Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">الموقع</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">الدولة</dt>
                        <dd class="text-gray-900 font-medium mt-1">{{ $buyer->country }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">المدينة</dt>
                        <dd class="text-gray-900 font-medium mt-1">{{ $buyer->city }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-gray-500">العنوان</dt>
                        <dd class="text-gray-900 font-medium mt-1">{{ $buyer->address }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Contact Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات الاتصال</h2>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-gray-500">البريد الإلكتروني للتواصل</dt>
                        <dd class="text-gray-900 font-medium mt-1">
                            <a href="mailto:{{ $buyer->contact_email }}" class="text-medical-blue-600 hover:text-medical-blue-800">
                                {{ $buyer->contact_email }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">رقم الهاتف</dt>
                        <dd class="text-gray-900 font-medium mt-1">
                            <a href="tel:{{ $buyer->contact_phone }}" class="text-medical-blue-600 hover:text-medical-blue-800">
                                {{ $buyer->contact_phone }}
                            </a>
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- License Documents --}}
            @if($licenseDocuments->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">وثائق الترخيص</h2>
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
                        <a href="{{ $document->getUrl() }}" 
                           target="_blank"
                           class="text-medical-blue-600 hover:text-medical-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- User Account --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">حساب المستخدم</h3>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-medical-blue-100 rounded-full flex items-center justify-center text-medical-blue-600 text-2xl font-bold">
                        {{ substr($buyer->user?->name ?? 'م', 0, 1) }}
                    </div>
                    <div>
                        <div class="font-medium text-gray-900">{{ $buyer->user?->name }}</div>
                        <div class="text-sm text-gray-500">{{ $buyer->user?->email }}</div>
                    </div>
                </div>
                <dl class="text-sm space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">تاريخ الانضمام</dt>
                        <dd class="text-gray-900">{{ $buyer->created_at->format('Y-m-d') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">آخر تحديث</dt>
                        <dd class="text-gray-900">{{ $buyer->updated_at->format('Y-m-d') }}</dd>
                    </div>
                    @if($buyer->verified_at)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">تاريخ التحقق</dt>
                        <dd class="text-gray-900">{{ $buyer->verified_at->format('Y-m-d') }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">إحصائيات سريعة</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">طلبات عروض الأسعار</dt>
                        <dd class="text-gray-900 font-medium">{{ $buyer->getTotalRfqsCount() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">الطلبات</dt>
                        <dd class="text-gray-900 font-medium">{{ $buyer->getTotalOrdersCount() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">المفضلة</dt>
                        <dd class="text-gray-900 font-medium">{{ $buyer->getFavoritesCount() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">إجمالي الإنفاق</dt>
                        <dd class="text-gray-900 font-medium">{{ number_format($buyer->getTotalSpending(), 2) }} د.ل</dd>
                    </div>
                </dl>
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">إجراءات سريعة</h3>
                <div class="space-y-2">
                    <a href="{{ route('buyer.profile.edit') }}" 
                       class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        <span class="text-sm text-gray-700">تعديل معلومات المنظمة</span>
                    </a>
                    <a href="{{ route('buyer.profile.edit') }}#password" 
                       class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        <span class="text-sm text-gray-700">تغيير كلمة المرور</span>
                    </a>
                    <a href="{{ route('buyer.profile.edit') }}#documents" 
                       class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span class="text-sm text-gray-700">رفع وثائق جديدة</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</x-dashboard.layout>

