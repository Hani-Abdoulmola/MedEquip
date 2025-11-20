{{-- Admin Buyers Management - View Buyer Details --}}
<x-dashboard.layout title="تفاصيل المشتري" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل المشتري</h1>
                <p class="mt-2 text-medical-gray-600">معلومات كاملة عن المشتري ونشاطه</p>
            </div>
            <a href="{{ route('admin.buyers') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Buyer Info Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <div class="text-center mb-6">
                    <div class="w-24 h-24 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-2xl flex items-center justify-center text-white font-bold text-3xl mx-auto mb-4">
                        م
                    </div>
                    <h2 class="text-xl font-bold text-medical-gray-900">مستشفى طرابلس المركزي</h2>
                    <p class="text-medical-gray-600 mt-1">فاطمة حسن</p>
                    <span class="inline-flex px-4 py-2 text-sm font-medium rounded-full bg-medical-green-100 text-medical-green-700 mt-3">
                        نشط
                    </span>
                </div>

                <div class="space-y-4 border-t border-medical-gray-200 pt-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">البريد الإلكتروني</p>
                        <p class="font-medium text-medical-gray-900 mt-1">fatima@hospital.ly</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الهاتف</p>
                        <p class="font-medium text-medical-gray-900 mt-1">+218 91 876 5432</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">نوع المؤسسة</p>
                        <p class="font-medium text-medical-gray-900 mt-1">مستشفى حكومي</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">المدينة</p>
                        <p class="font-medium text-medical-gray-900 mt-1">طرابلس، ليبيا</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ التسجيل</p>
                        <p class="font-medium text-medical-gray-900 mt-1">2024-02-20</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-medical-gray-200 space-y-3">
                    <button class="w-full px-4 py-2.5 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors duration-200 font-medium">
                        تعديل البيانات
                    </button>
                    <button class="w-full px-4 py-2.5 bg-medical-yellow-50 text-medical-yellow-700 rounded-xl hover:bg-medical-yellow-100 transition-colors duration-200 font-medium">
                        إيقاف مؤقت
                    </button>
                    <button class="w-full px-4 py-2.5 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 transition-colors duration-200 font-medium">
                        حذف المشتري
                    </button>
                </div>
            </div>
        </div>

        {{-- Stats and Activity --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-6 shadow-medical">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">الطلبات</p>
                            <p class="text-3xl font-bold text-medical-gray-900 mt-2">23</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-medical">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-medical-gray-600">طلبات مكتملة</p>
                            <p class="text-3xl font-bold text-medical-green-600 mt-2">18</p>
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
                            <p class="text-sm text-medical-gray-600">إجمالي المشتريات</p>
                            <p class="text-2xl font-bold text-medical-gray-900 mt-2">89,500 د.ل</p>
                        </div>
                        <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Orders --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">الطلبات الأخيرة</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-12 h-12 bg-medical-green-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-medical-gray-900">طلب #1234</p>
                                <p class="text-sm text-medical-gray-600">تم التسليم: 2024-03-15</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-medical-blue-600 font-semibold">12,500 د.ل</p>
                            <span class="text-xs text-medical-green-600">مكتمل</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                        <div class="flex items-center space-x-3 space-x-reverse">
                            <div class="w-12 h-12 bg-medical-yellow-100 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-medical-gray-900">طلب #1235</p>
                                <p class="text-sm text-medical-gray-600">قيد المعالجة</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <p class="text-medical-blue-600 font-semibold">8,200 د.ل</p>
                            <span class="text-xs text-medical-yellow-600">جاري التنفيذ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

