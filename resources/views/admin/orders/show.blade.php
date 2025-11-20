{{-- Admin Orders Management - View Order Details --}}
<x-dashboard.layout title="تفاصيل الطلب" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الطلب #ORD-1234</h1>
                <p class="mt-2 text-medical-gray-600">معلومات كاملة عن الطلب</p>
            </div>
            <a href="{{ route('admin.orders') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Order Summary --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">حالة الطلب</h3>
                <div class="text-center mb-6">
                    <div class="w-20 h-20 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl flex items-center justify-center text-white mx-auto mb-4">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-medical-green-100 text-medical-green-700">
                        مكتمل
                    </span>
                </div>

                <div class="space-y-3 border-t border-medical-gray-200 pt-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-medical-gray-600">رقم الطلب</span>
                        <span class="font-medium text-medical-gray-900">#ORD-1234</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-medical-gray-600">تاريخ الطلب</span>
                        <span class="font-medium text-medical-gray-900">2024-03-10</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-medical-gray-600">تاريخ التسليم</span>
                        <span class="font-medium text-medical-gray-900">2024-03-15</span>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-medical-gray-200">
                        <span class="text-sm text-medical-gray-600">المبلغ الإجمالي</span>
                        <span class="text-xl font-bold text-medical-blue-600">12,500 د.ل</span>
                    </div>
                </div>
            </div>

            {{-- Buyer Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات المشتري</h3>
                <div class="flex items-center space-x-3 space-x-reverse mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold">
                        م
                    </div>
                    <div>
                        <p class="font-medium text-medical-gray-900">مستشفى طرابلس المركزي</p>
                        <p class="text-sm text-medical-gray-600">فاطمة حسن</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <p class="text-medical-gray-600">fatima@hospital.ly</p>
                    <p class="text-medical-gray-600">+218 91 876 5432</p>
                    <p class="text-medical-gray-600">طرابلس، ليبيا</p>
                </div>
            </div>

            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات المورد</h3>
                <div class="flex items-center space-x-3 space-x-reverse mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold">
                        م
                    </div>
                    <div>
                        <p class="font-medium text-medical-gray-900">شركة المعدات الطبية</p>
                        <p class="text-sm text-medical-gray-600">محمد علي</p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <p class="text-medical-gray-600">mohamed@medequip.ly</p>
                    <p class="text-medical-gray-600">+218 91 234 5678</p>
                    <p class="text-medical-gray-600">طرابلس، ليبيا</p>
                </div>
            </div>
        </div>

        {{-- Order Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">المنتجات المطلوبة</h3>
                <div class="space-y-4">
                    {{-- Item 1 --}}
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center space-x-4 space-x-reverse flex-1">
                            <div class="w-16 h-16 bg-medical-blue-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-medical-gray-900">جهاز قياس ضغط الدم</p>
                                <p class="text-sm text-medical-gray-600 mt-1">رقم المنتج: PRD-001</p>
                            </div>
                        </div>
                        <div class="text-left mr-4">
                            <p class="text-sm text-medical-gray-600">الكمية: 5</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">6,000 د.ل</p>
                        </div>
                    </div>

                    {{-- Item 2 --}}
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center space-x-4 space-x-reverse flex-1">
                            <div class="w-16 h-16 bg-medical-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-medium text-medical-gray-900">ميزان حرارة رقمي</p>
                                <p class="text-sm text-medical-gray-600 mt-1">رقم المنتج: PRD-002</p>
                            </div>
                        </div>
                        <div class="text-left mr-4">
                            <p class="text-sm text-medical-gray-600">الكمية: 10</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">3,500 د.ل</p>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="mt-6 pt-6 border-t border-medical-gray-200 space-y-3">
                    <div class="flex justify-between text-medical-gray-600">
                        <span>المجموع الفرعي</span>
                        <span>9,500 د.ل</span>
                    </div>
                    <div class="flex justify-between text-medical-gray-600">
                        <span>الضريبة (10%)</span>
                        <span>950 د.ل</span>
                    </div>
                    <div class="flex justify-between text-medical-gray-600">
                        <span>الشحن</span>
                        <span>2,050 د.ل</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-medical-gray-900 pt-3 border-t border-medical-gray-200">
                        <span>المجموع الإجمالي</span>
                        <span class="text-medical-blue-600">12,500 د.ل</span>
                    </div>
                </div>
            </div>

            {{-- Shipping Information --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات الشحن</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-medical-gray-600">عنوان التسليم</p>
                        <p class="font-medium text-medical-gray-900 mt-1">شارع الجمهورية، طرابلس</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">طريقة الشحن</p>
                        <p class="font-medium text-medical-gray-900 mt-1">توصيل سريع</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم التتبع</p>
                        <p class="font-medium text-medical-blue-600 mt-1">TRK-789456123</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">شركة الشحن</p>
                        <p class="font-medium text-medical-gray-900 mt-1">ليبيا للشحن السريع</p>
                    </div>
                </div>
            </div>

            {{-- Order Timeline --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">سجل الطلب</h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-3 h-3 bg-medical-green-500 rounded-full mt-1.5"></div>
                        <div class="flex-1">
                            <p class="font-medium text-medical-gray-900">تم التسليم</p>
                            <p class="text-sm text-medical-gray-600 mt-1">2024-03-15 02:30 م</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-3 h-3 bg-medical-blue-500 rounded-full mt-1.5"></div>
                        <div class="flex-1">
                            <p class="font-medium text-medical-gray-900">قيد التوصيل</p>
                            <p class="text-sm text-medical-gray-600 mt-1">2024-03-14 09:00 ص</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-3 h-3 bg-medical-yellow-500 rounded-full mt-1.5"></div>
                        <div class="flex-1">
                            <p class="font-medium text-medical-gray-900">قيد المعالجة</p>
                            <p class="text-sm text-medical-gray-600 mt-1">2024-03-11 11:15 ص</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3 space-x-reverse">
                        <div class="w-3 h-3 bg-medical-gray-400 rounded-full mt-1.5"></div>
                        <div class="flex-1">
                            <p class="font-medium text-medical-gray-900">تم إنشاء الطلب</p>
                            <p class="text-sm text-medical-gray-600 mt-1">2024-03-10 03:45 م</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

