{{-- Admin Reports - Analytics and Reports Dashboard --}}
<x-dashboard.layout title="التقارير والإحصائيات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- Page Header --}}
    <div class="mb-6">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">التقارير والإحصائيات</h1>
            <p class="mt-2 text-medical-gray-600">تحليلات شاملة لأداء المنصة</p>
        </div>
    </div>

    {{-- Date Range Filter --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" value="2024-01-01"
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" value="2024-03-31"
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">نوع التقرير</label>
                <select class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    <option value="all">جميع التقارير</option>
                    <option value="sales">المبيعات</option>
                    <option value="users">المستخدمين</option>
                    <option value="products">المنتجات</option>
                </select>
            </div>
            <div class="flex items-end">
                <button class="w-full px-6 py-2.5 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors duration-200 font-medium">
                    تطبيق
                </button>
            </div>
        </div>
    </div>

    {{-- Key Metrics --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المبيعات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">1.2M د.ل</p>
                    <p class="text-sm text-medical-green-600 mt-2">+12.5% من الشهر الماضي</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">عدد الطلبات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">456</p>
                    <p class="text-sm text-medical-green-600 mt-2">+8.3% من الشهر الماضي</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مستخدمين جدد</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">89</p>
                    <p class="text-sm text-medical-green-600 mt-2">+15.2% من الشهر الماضي</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">معدل التحويل</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">68.4%</p>
                    <p class="text-sm text-medical-green-600 mt-2">+3.1% من الشهر الماضي</p>
                </div>
                <div class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Section --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Sales Chart --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">المبيعات الشهرية</h3>
            <div id="salesChart" class="h-80"></div>
        </div>

        {{-- Orders Chart --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">توزيع الطلبات</h3>
            <div id="ordersChart" class="h-80"></div>
        </div>
    </div>

    {{-- Top Products and Suppliers --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Top Products --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">أكثر المنتجات مبيعاً</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-blue-100 rounded-lg flex items-center justify-center text-medical-blue-600 font-bold">
                            1
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">جهاز قياس ضغط الدم</p>
                            <p class="text-sm text-medical-gray-600">128 مبيعة</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">153,600 د.ل</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-green-100 rounded-lg flex items-center justify-center text-medical-green-600 font-bold">
                            2
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">ميزان حرارة رقمي</p>
                            <p class="text-sm text-medical-gray-600">95 مبيعة</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">33,250 د.ل</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-yellow-100 rounded-lg flex items-center justify-center text-medical-yellow-600 font-bold">
                            3
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">جهاز قياس السكر</p>
                            <p class="text-sm text-medical-gray-600">78 مبيعة</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">62,400 د.ل</span>
                </div>
            </div>
        </div>

        {{-- Top Suppliers --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">أفضل الموردين</h3>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-blue-100 rounded-lg flex items-center justify-center text-medical-blue-600 font-bold">
                            1
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">شركة المعدات الطبية</p>
                            <p class="text-sm text-medical-gray-600">45 منتج</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">245,000 د.ل</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-green-100 rounded-lg flex items-center justify-center text-medical-green-600 font-bold">
                            2
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">مؤسسة الصحة الليبية</p>
                            <p class="text-sm text-medical-gray-600">38 منتج</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">198,500 د.ل</span>
                </div>

                <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                    <div class="flex items-center space-x-3 space-x-reverse">
                        <div class="w-10 h-10 bg-medical-yellow-100 rounded-lg flex items-center justify-center text-medical-yellow-600 font-bold">
                            3
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">الشركة الوطنية للأجهزة</p>
                            <p class="text-sm text-medical-gray-600">32 منتج</p>
                        </div>
                    </div>
                    <span class="text-medical-blue-600 font-semibold">156,800 د.ل</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Export Options --}}
    <div class="bg-white rounded-2xl shadow-medical p-6">
        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">تصدير التقارير</h3>
        <div class="flex items-center space-x-4 space-x-reverse">
            <button class="px-6 py-3 bg-medical-green-500 text-white rounded-xl hover:bg-medical-green-600 transition-colors duration-200 font-medium">
                تصدير Excel
            </button>
            <button class="px-6 py-3 bg-medical-red-500 text-white rounded-xl hover:bg-medical-red-600 transition-colors duration-200 font-medium">
                تصدير PDF
            </button>
            <button class="px-6 py-3 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors duration-200 font-medium">
                طباعة
            </button>
        </div>
    </div>

</x-dashboard.layout>

