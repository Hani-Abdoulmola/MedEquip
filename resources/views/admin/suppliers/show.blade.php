{{-- Admin Suppliers Management - View Supplier Details --}}
<x-dashboard.layout title="تفاصيل المورد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- 
    ADMIN SUPPLIERS SHOW PAGE - Detail Page Pattern Template with Tabs
    Controller: SupplierController@show
    Data: $supplier = Supplier::with(['user', 'products', 'quotations'])->findOrFail($id);
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل المورد</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراجعة معلومات المورد والمنتجات</p>
            </div>
            <a href="{{ route('admin.suppliers') }}" class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Supplier Header Card --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-medical-gray-900">شركة الأجهزة الطبية المتقدمة</h2>
                    <p class="text-medical-gray-600 mt-1">معرف المورد: #SUP-001</p>
                    <div class="flex items-center space-x-3 space-x-reverse mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-green-100 text-medical-green-700">
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            موثق
                        </span>
                        <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-blue-100 text-medical-blue-700">
                            نشط
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                <button class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>توثيق المورد</span>
                </button>
                <button class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                    </svg>
                    <span>إيقاف المورد</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">24</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">عروض الأسعار</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">18</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الطلبات المكتملة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">12</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">التقييم</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">4.8</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabs Section --}}
    <div class="bg-white rounded-2xl shadow-medical" x-data="{ activeTab: 'info' }">
        {{-- Tab Headers --}}
        <div class="border-b border-medical-gray-200">
            <nav class="flex space-x-4 space-x-reverse px-6" aria-label="Tabs">
                <button @click="activeTab = 'info'" :class="activeTab === 'info' ? 'border-medical-blue-600 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    معلومات المورد
                </button>
                <button @click="activeTab = 'products'" :class="activeTab === 'products' ? 'border-medical-blue-600 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    المنتجات
                </button>
                <button @click="activeTab = 'quotations'" :class="activeTab === 'quotations' ? 'border-medical-blue-600 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    عروض الأسعار
                </button>
                <button @click="activeTab = 'documents'" :class="activeTab === 'documents' ? 'border-medical-blue-600 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    المستندات
                </button>
                <button @click="activeTab = 'activity'" :class="activeTab === 'activity' ? 'border-medical-blue-600 text-medical-blue-600' : 'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'" class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    سجل النشاط
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            {{-- Info Tab --}}
            <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات الشركة</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">اسم الشركة</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">شركة الأجهزة الطبية المتقدمة</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">السجل التجاري</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">CR-123456789</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">الرقم الضريبي</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">TAX-987654321</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">العنوان</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">طرابلس، ليبيا</dd>
                            </div>
                        </dl>
                    </div>

                    <div>
                        <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات الاتصال</h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">البريد الإلكتروني</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">info@medicaldevices.ly</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">رقم الهاتف</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">+218 91 234 5678</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">الموقع الإلكتروني</dt>
                                <dd class="mt-1 text-sm text-medical-blue-600">www.medicaldevices.ly</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-medical-gray-600">تاريخ التسجيل</dt>
                                <dd class="mt-1 text-sm text-medical-gray-900">2024-01-15</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            {{-- Products Tab --}}
            <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">اسم المنتج</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">الفئة</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">الحالة</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">تاريخ الإضافة</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-medical-gray-700">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-medical-gray-200">
                            <tr class="hover:bg-medical-gray-50 transition-all duration-200">
                                <td class="px-6 py-4 text-sm text-medical-gray-900">جهاز أشعة رقمي</td>
                                <td class="px-6 py-4 text-sm text-medical-gray-600">أجهزة التصوير</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-green-100 text-medical-green-700">نشط</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-medical-gray-600">2024-02-10</td>
                                <td class="px-6 py-4 text-center">
                                    <button class="text-medical-blue-600 hover:text-medical-blue-700 font-medium text-sm">عرض التفاصيل</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quotations Tab --}}
            <div x-show="activeTab === 'quotations'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <p class="text-medical-gray-600">عروض الأسعار المقدمة من المورد</p>
            </div>

            {{-- Documents Tab --}}
            <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <p class="text-medical-gray-600">المستندات والشهادات</p>
            </div>

            {{-- Activity Tab --}}
            <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <p class="text-medical-gray-600">سجل نشاط المورد</p>
            </div>
        </div>
    </div>

</x-dashboard.layout>
