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
            <a href="{{ route('admin.suppliers') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Supplier Header Card --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4 space-x-reverse">
                <div
                    class="w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-medical-gray-900">{{ $supplier->company_name }}</h2>
                    <p class="text-medical-gray-600 mt-1">
                        معرف المورد: #{{ $supplier->id }}
                    </p>
                    <div class="flex items-center space-x-3 space-x-reverse mt-2">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $supplier->is_verified ? 'bg-medical-green-100 text-medical-green-700' : 'bg-medical-yellow-100 text-medical-yellow-700' }}">
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            {{ $supplier->is_verified ? 'موثق' : 'قيد المراجعة' }}
                        </span>
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $supplier->is_active ? 'bg-medical-blue-100 text-medical-blue-700' : 'bg-medical-red-100 text-medical-red-700' }}">
                            {{ $supplier->is_active ? 'نشط' : 'موقوف' }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex items-center space-x-2 space-x-reverse">
                @if (!$supplier->is_verified)
                    <form method="POST" action="{{ route('admin.suppliers.verify', $supplier) }}">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-medium shadow-medical">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>توثيق المورد</span>
                        </button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.suppliers.toggle-active', $supplier) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2 {{ $supplier->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-medical-blue-600 hover:bg-medical-blue-700' }} text-white rounded-xl transition-all duration-200 font-medium shadow-medical">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                        <span>{{ $supplier->is_active ? 'إيقاف المورد' : 'تفعيل المورد' }}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @php
        $productsCount = $supplier->products->count();
        $quotationsCount = $supplier->quotations()->count();
        $completedDeliveries = $supplier->deliveries()->count();
    @endphp

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">المنتجات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $productsCount }}</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">عروض الأسعار</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $quotationsCount }}</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">عمليات التسليم</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $completedDeliveries }}</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">التقييم</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">—</p>
                </div>
                <div
                    class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
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
                <button @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    معلومات المورد
                </button>
                <button @click="activeTab = 'products'"
                    :class="activeTab === 'products' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    المنتجات
                </button>
                <button @click="activeTab = 'quotations'"
                    :class="activeTab === 'quotations' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    عروض الأسعار
                </button>
                <button @click="activeTab = 'documents'"
                    :class="activeTab === 'documents' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    المستندات
                </button>
                <button @click="activeTab = 'activity'"
                    :class="activeTab === 'activity' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300'"
                    class="py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                    سجل النشاط
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            {{-- Info Tab --}}
            <div x-show="activeTab === 'info'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Company Info Table & Status --}}
                    <div class="flex flex-col h-full">
                        {{-- Company Info Table --}}
                        <h3 class="text-lg font-bold text-medical-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 21V7a2 2 0 012-2h2a2 2 0 012 2v14M21 21v-8a2 2 0 00-2-2h-4a2 2 0 00-2 2v8" />
                            </svg>
                            معلومات الشركة
                        </h3>
                        <div class="rounded-xl shadow overflow-hidden border border-medical-gray-100 bg-white mb-8">
                            <table class="min-w-full divide-y divide-medical-gray-100">
                                <tbody>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 w-40 text-right">
                                            اسم الشركة</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm font-medium">
                                            {{ $supplier->company_name ?? '—' }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            السجل التجاري</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            @if ($supplier->commercial_register)
                                                <span
                                                    class="inline-block px-2 py-0.5 rounded bg-medical-yellow-100 text-medical-yellow-800">
                                                    {{ $supplier->commercial_register }}
                                                </span>
                                            @else
                                                <span class="text-medical-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            الرقم الضريبي</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            @if ($supplier->tax_number)
                                                <span
                                                    class="inline-block px-2 py-0.5 rounded bg-medical-green-100 text-medical-green-900">
                                                    {{ $supplier->tax_number }}
                                                </span>
                                            @else
                                                <span class="text-medical-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            العنوان</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            @php
                                                $location = trim(
                                                    ($supplier->city ?? '') . '، ' . ($supplier->country ?? ''),
                                                );
                                            @endphp
                                            <span class="{{ $supplier->address ? '' : 'text-medical-gray-400' }}">
                                                {{ $supplier->address ?: ($location ?: '—') }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        {{-- Company Status Table --}}
                        <div>
                            <h3 class="text-lg font-bold text-medical-gray-800 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 20 20">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m2-4H7a2 2 0 00-2 2v6a2 2 0 002 2h6a2 2 0 002-2v-6a2 2 0 00-2-2z" />
                                </svg>
                                حالة الشركة
                            </h3>
                            <div class="rounded-xl shadow overflow-hidden border border-medical-gray-100 bg-white">
                                <table class="min-w-full divide-y divide-medical-gray-100">
                                    <tbody>
                                        <tr>
                                            <th
                                                class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-2 px-4 text-right w-32">
                                                الحالة
                                            </th>
                                            <td class="py-2 px-4 text-medical-gray-900 text-sm">
                                                <span
                                                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold
                                                    {{ $supplier->is_active ? 'bg-medical-green-100 text-medical-green-700' : 'bg-medical-gray-100 text-medical-gray-600' }}">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="{{ $supplier->is_active ? 'M5 13l4 4L19 7' : 'M6 18L18 6M6 6l12 12' }}" />
                                                    </svg>
                                                    {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th
                                                class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-2 px-4 text-right w-32">
                                                التوثيق
                                            </th>
                                            <td class="py-2 px-4 text-medical-gray-900 text-sm">
                                                @if ($supplier->is_verified)
                                                    <span
                                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-medical-blue-100 text-medical-blue-700 text-xs font-semibold">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M9 12l2 2 4-4" />
                                                        </svg>
                                                        موثق
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-medical-gray-100 text-medical-gray-500 text-xs font-semibold">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 20 20">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        غير موثق
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- Contact Info Table --}}
                    <div>
                        <h3 class="text-lg font-bold text-medical-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-medical-blue-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 13a4 4 0 01-8 0m8 0a4 4 0 00-8 0m8 0V5a3 3 0 00-6 0v8a4 4 0 01-8 0" />
                            </svg>
                            معلومات الاتصال
                        </h3>
                        <div class="rounded-xl shadow overflow-hidden border border-medical-gray-100 bg-white">
                            <table class="min-w-full divide-y divide-medical-gray-100">
                                <tbody>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 w-40 text-right">
                                            المسؤول</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            <span
                                                class="{{ optional($supplier->user)->name ? '' : 'text-medical-gray-400' }}">
                                                {{ optional($supplier->user)->name ?? '—' }}
                                            </span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            البريد الإلكتروني</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            <a href="mailto:{{ $supplier->contact_email ?? optional($supplier->user)->email }}"
                                                class="hover:underline {{ $supplier->contact_email ?? optional($supplier->user)->email ? 'text-medical-blue-700' : 'text-medical-gray-400 pointer-events-none' }}">
                                                {{ $supplier->contact_email ?? (optional($supplier->user)->email ?? '—') }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            رقم الهاتف</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            @php
                                                $phone = $supplier->contact_phone ?? optional($supplier->user)->phone;
                                            @endphp
                                            @if ($phone)
                                                <a href="tel:{{ $phone }}"
                                                    class="text-medical-blue-700 hover:underline">
                                                    {{ $phone }}
                                                </a>
                                            @else
                                                <span class="text-medical-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th
                                            class="bg-medical-gray-50 text-xs font-semibold text-medical-gray-600 py-3 px-4 text-right">
                                            تاريخ التسجيل</th>
                                        <td class="py-3 px-4 text-medical-gray-900 text-sm">
                                            @if ($supplier->created_at)
                                                <span
                                                    class="inline-block px-2 py-0.5 rounded bg-medical-blue-50 text-medical-blue-800">
                                                    {{ $supplier->created_at->translatedFormat('Y/m/d - h:i A') }}
                                                </span>
                                            @else
                                                <span class="text-medical-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Products Tab --}}
            <div x-show="activeTab === 'products'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">اسم المنتج
                                </th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">الفئة</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">الحالة</th>
                                <th class="px-6 py-3 text-right text-sm font-medium text-medical-gray-700">تاريخ
                                    الإضافة</th>
                                <th class="px-6 py-3 text-center text-sm font-medium text-medical-gray-700">الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-medical-gray-200">
                            @forelse($supplier->products ?? [] as $product)
                                <tr class="hover:bg-medical-gray-50 transition-all duration-200">
                                    <td class="px-6 py-4 text-sm text-medical-gray-900">
                                        {{ $product->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-medical-gray-600">
                                        {{ optional($product->category)->name ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if (isset($product->is_active) && $product->is_active)
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-green-100 text-medical-green-700">
                                                نشط
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-gray-100 text-medical-gray-600">
                                                غير نشط
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-medical-gray-600">
                                        {{ optional($product->created_at)->format('Y-m-d') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <a href="{{ route('admin.products.show', $product->id) }}"
                                            class="text-medical-blue-600 hover:text-medical-blue-700 font-medium text-sm">
                                            عرض التفاصيل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-medical-gray-500 py-8">
                                        لا يوجد منتجات لهذا المورد حالياً.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quotations Tab --}}
            <div x-show="activeTab === 'quotations'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">عروض الأسعار المقدمة من المورد</h3>
                @php
                    // Assuming there is a relationship $supplier->quotations for the quotations
                    $quotations = $supplier->quotations ?? [];
                @endphp

                @if ($quotations && count($quotations) > 0)
                    <div class="overflow-x-auto rounded-2xl shadow-medical bg-white mt-2">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                        رقم العرض</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                        التاريخ</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                        الحالة</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                        إجمالي المبلغ</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                        الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-medical-gray-100">
                                @foreach ($quotations as $quotation)
                                    <tr class="hover:bg-medical-gray-50 transition-all duration-200">
                                        <td class="px-6 py-4 text-sm text-medical-gray-900">
                                            #{{ $quotation->id ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-medical-gray-600">
                                            {{ optional($quotation->created_at)->format('Y-m-d') ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($quotation->status === 'approved')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-green-100 text-medical-green-700">مقبول</span>
                                            @elseif($quotation->status === 'pending')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-yellow-100 text-medical-yellow-700">قيد
                                                    الانتظار</span>
                                            @elseif($quotation->status === 'rejected')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-red-100 text-medical-red-700">مرفوض</span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium bg-medical-gray-100 text-medical-gray-600">غير
                                                    محدد</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-medical-gray-900">
                                            {{ number_format($quotation->total_amount ?? 0, 2) }} ريال
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('admin.quotations.show', $quotation->id) }}"
                                                class="text-medical-blue-600 hover:text-medical-blue-700 font-medium text-sm">
                                                عرض التفاصيل
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-sm text-medical-gray-500 mt-4">
                        لا توجد عروض أسعار مقدمة من هذا المورد حالياً.
                    </p>
                @endif
            </div>

            {{-- Documents Tab --}}
            <div x-show="activeTab === 'documents'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">مستندات التوثيق</h3>
                @php
                    $verificationMedia = $supplier->getFirstMedia('verification_documents');
                @endphp
                @if ($verificationMedia)
                    <div class="space-y-2">
                        <p class="text-sm text-medical-gray-700">
                            تم رفع مستند توثيق واحد لهذا المورد. يمكن للإدارة مراجعته من خلال الرابط التالي:
                        </p>
                        <a href="{{ $verificationMedia->getUrl() }}" target="_blank"
                            class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-xl hover:bg-medical-blue-100 transition-colors duration-200 text-sm font-medium">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 3h4a2 2 0 012 2v4M10 14l9-9M5 21h14a2 2 0 002-2v-7" />
                            </svg>
                            <span>عرض / تنزيل مستند التوثيق</span>
                        </a>
                        <p class="text-xs text-medical-gray-500">
                            اسم الملف: {{ $verificationMedia->file_name }} · الحجم:
                            {{ number_format($verificationMedia->size / 1024, 1) }} كيلوبايت
                        </p>
                    </div>
                @else
                    <p class="text-sm text-medical-gray-500">
                        لا يوجد مستند توثيق مرفوع لهذا المورد حتى الآن.
                    </p>
                @endif
            </div>

            {{-- Activity Tab --}}
            <div x-show="activeTab === 'activity'" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display: none;">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">سجل نشاط المورد</h3>
                @if (isset($activities) && count($activities) > 0)
                    <ul class="divide-y divide-medical-gray-100">
                        @foreach ($activities as $activity)
                            <li class="py-4 flex items-start space-x-3 space-x-reverse">
                                <span class="flex-1 min-w-0">
                                    <p class="text-medical-gray-800 text-sm font-medium mb-1">
                                        {{ $activity->description ?? '-' }}
                                    </p>
                                    <span class="text-xs text-medical-gray-500">
                                        {{ \Carbon\Carbon::parse($activity->created_at)->translatedFormat('Y/m/d - h:i A') }}
                                    </span>
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-sm text-medical-gray-500 mt-4">لا توجد أنشطة متاحة لهذا المورد حتى الآن.</p>
                @endif
            </div>
        </div>
    </div>

</x-dashboard.layout>
