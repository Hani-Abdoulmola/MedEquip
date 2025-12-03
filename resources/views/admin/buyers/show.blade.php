{{-- Admin Buyers Management - View Buyer Details --}}
<x-dashboard.layout title="تفاصيل المشتري" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل المشتري</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراجعة معلومات المشتري</p>
            </div>
            <a href="{{ route('admin.buyers') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Buyer Header Card --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <div class="flex items-start justify-between">

            <div class="flex items-center space-x-4 space-x-reverse">
                {{-- Avatar --}}
                <div
                    class="w-20 h-20 bg-gradient-to-br from-medical-blue-100 to-medical-green-200 rounded-2xl flex items-center justify-center">
                    <span class="text-3xl font-bold text-medical-blue-600">
                        {{ mb_substr($buyer->organization_name, 0, 1, 'UTF-8') }}
                    </span>
                </div>

                {{-- Header Info --}}
                <div>
                    <h2 class="text-2xl font-bold text-medical-gray-900">{{ $buyer->organization_name }}</h2>
                    <p class="text-medical-gray-600 mt-1">معرّف المشتري: #{{ $buyer->id }}</p>

                    <div class="flex items-center space-x-3 space-x-reverse mt-2">
                        {{-- Verified --}}
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium
                            {{ $buyer->is_verified ? 'bg-medical-green-100 text-medical-green-700' : 'bg-medical-yellow-100 text-medical-yellow-700' }}">
                            <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            {{ $buyer->is_verified ? 'موثق' : 'غير موثق' }}
                        </span>

                        {{-- Status --}}
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium
                            {{ $buyer->is_active ? 'bg-medical-blue-100 text-medical-blue-700' : 'bg-medical-red-100 text-medical-red-700' }}">
                            {{ $buyer->is_active ? 'نشط' : 'موقوف' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Toggle Active --}}
            <div>
                <form method="POST" action="{{ route('admin.buyers.toggle-active', $buyer) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center space-x-2 space-x-reverse px-4 py-2
                        {{ $buyer->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-medical-blue-600 hover:bg-medical-blue-700' }}
                        text-white rounded-xl transition-all duration-200 font-medium shadow-medical">

                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>

                        <span>{{ $buyer->is_active ? 'إيقاف المشتري' : 'تفعيل المشتري' }}</span>
                    </button>
                </form>
            </div>

        </div>
    </div>

    @php
        $ordersCount = $buyer->orders->count();
        $invoicesCount = $buyer->invoices->count();
        $rfqsCount = $buyer->rfqs->count();
        $totalPurchases = $buyer->orders->sum('total_amount');
    @endphp

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">

        {{-- Orders --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الطلبات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $ordersCount }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5h6l2 14H7L9 5z" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- RFQs --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">طلبات عروض الأسعار</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">{{ $rfqsCount }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M3 7h18M3 12h18M3 17h18" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Invoices --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">الفواتير</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $invoicesCount }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M9 12h6m-6 4h6M9 8h3" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
            </div>
        </div>

        {{-- Total Purchases --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المشتريات</p>
                    <p class="text-2xl font-bold text-medical-gray-900 mt-2">
                        {{ number_format($totalPurchases, 2) }} د.ل
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path d="M12 8v8m0-8c1.1 0 2 .9 2 2s-.9 2-2 2m0 4c-1.1 0-2-.9-2-2s.9-2 2-2" stroke-width="2"
                            stroke-linecap="round" />
                    </svg>
                </div>
            </div>
        </div>

    </div>

    {{-- Tabs Section --}}
    <div class="bg-white rounded-2xl shadow-medical" x-data="{ activeTab: 'info' }">

        {{-- Tabs Header --}}
        <div class="border-b border-medical-gray-200">
            <nav class="flex space-x-4 space-x-reverse px-6">

                <button @click="activeTab = 'info'"
                    :class="activeTab === 'info' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">معلومات المشتري</button>

                <button @click="activeTab = 'rfqs'"
                    :class="activeTab === 'rfqs' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">طلبات عروض الأسعار</button>

                <button @click="activeTab = 'orders'"
                    :class="activeTab === 'orders' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">الطلبات</button>

                <button @click="activeTab = 'invoices'"
                    :class="activeTab === 'invoices' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">الفواتير</button>

                <button @click="activeTab = 'documents'"
                    :class="activeTab === 'documents' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">المستندات</button>

                <button @click="activeTab = 'activity'"
                    :class="activeTab === 'activity' ? 'border-medical-blue-600 text-medical-blue-600' :
                        'border-transparent text-medical-gray-500'"
                    class="py-4 px-1 border-b-2 font-medium text-sm">سجل النشاط</button>

            </nav>
        </div>

        {{-- Tabs Content --}}
        <div class="p-6">

            {{-- INFO TAB --}}
            <div x-show="activeTab === 'info'" x-transition>
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات عامة</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                    {{-- Left Column --}}
                    <div>
                        <table class="min-w-full divide-y divide-medical-gray-100 rounded-xl shadow border">
                            <tbody>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">اسم المؤسسة</th>
                                    <td class="py-3 px-4 text-medical-gray-900">{{ $buyer->organization_name }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">نوع المؤسسة</th>
                                    <td class="py-3 px-4 text-medical-gray-900">{{ $buyer->organization_type }}</td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">رقم الترخيص</th>
                                    <td class="py-3 px-4 text-medical-gray-900">{{ $buyer->license_number ?? '—' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">العنوان</th>
                                    <td class="py-3 px-4 text-medical-gray-900">
                                        {{ $buyer->address ?: $buyer->city . '، ' . $buyer->country }}
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">تاريخ التسجيل</th>
                                    <td class="py-3 px-4 text-medical-gray-900">
                                        {{ $buyer->created_at->format('Y/m/d - h:i A') }}
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    {{-- Right Column --}}
                    <div>
                        <table class="min-w-full divide-y divide-medical-gray-100 rounded-xl shadow border">
                            <tbody>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">الشخص المسؤول</th>
                                    <td class="py-3 px-4 text-medical-gray-900">{{ $buyer->contact_person ?? '—' }}
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">البريد الإلكتروني
                                    </th>
                                    <td class="py-3 px-4 text-medical-blue-700">
                                        <a href="mailto:{{ $buyer->contact_email }}">{{ $buyer->contact_email }}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">رقم الهاتف</th>
                                    <td class="py-3 px-4 text-medical-blue-700">
                                        <a href="tel:{{ $buyer->contact_phone }}">{{ $buyer->contact_phone }}</a>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">الحالة</th>
                                    <td class="py-3 px-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center
                                            {{ $buyer->is_active ? 'bg-medical-green-100 text-medical-green-700' : 'bg-medical-gray-100 text-medical-gray-600' }}">
                                            {{ $buyer->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                </tr>

                                <tr>
                                    <th class="bg-medical-gray-50 py-3 px-4 text-xs font-semibold">التوثيق</th>
                                    <td class="py-3 px-4">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-semibold inline-flex items-center
                                            {{ $buyer->is_verified ? 'bg-medical-blue-100 text-medical-blue-700' : 'bg-medical-gray-100 text-medical-gray-600' }}">
                                            {{ $buyer->is_verified ? 'موثق' : 'غير موثق' }}
                                        </span>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
            {{-- RFQS TAB --}}
            <div x-show="activeTab === 'rfqs'" x-transition style="display: none;">

                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">
                    طلبات عروض الأسعار (RFQs)
                </h3>

                @if ($buyer->rfqs->count() > 0)
                    <div class="overflow-x-auto rounded-2xl shadow-medical bg-white mt-2">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500">رقم
                                        الطلب</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500">التاريخ
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500">الحالة
                                    </th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-medical-gray-500">
                                        الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-medical-gray-100">

                                @foreach ($buyer->rfqs as $rfq)
                                    <tr class="hover:bg-medical-gray-50 transition-all duration-200">

                                        <td class="px-6 py-4 text-sm text-medical-gray-900">
                                            #{{ $rfq->id }}
                                        </td>

                                        <td class="px-6 py-4 text-sm text-medical-gray-600">
                                            {{ $rfq->created_at->format('Y-m-d') }}
                                        </td>

                                        <td class="px-6 py-4">
                                            <span
                                                class="px-3 py-1 rounded-lg text-xs font-medium
                                                @if ($rfq->status === 'pending') bg-medical-yellow-100 text-medical-yellow-700
                                                @elseif($rfq->status === 'approved') bg-medical-green-100 text-medical-green-700
                                                @else bg-medical-red-100 text-medical-red-700 @endif">
                                                {{ $rfq->status }}
                                            </span>
                                        </td>

                                        <td class="px-6 py-4 text-center">
                                            <a href="{{ route('admin.rfqs.show', $rfq) }}"
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
                    <p class="text-sm text-medical-gray-500 mt-4">لا توجد طلبات عروض أسعار.</p>
                @endif
            </div>

            {{-- ORDERS TAB --}}
            <div x-show="activeTab === 'orders'" x-transition style="display: none;">

                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">طلبات الشراء</h3>

                @if ($buyer->orders->count() > 0)
                    @foreach ($buyer->orders as $order)
                        <div class="p-4 border rounded-xl mb-3 bg-medical-gray-50">

                            <div class="flex items-center justify-between">
                                <p class="font-medium text-medical-gray-900">طلب #{{ $order->id }}</p>

                                <span
                                    class="px-3 py-1 rounded-lg text-xs font-medium
                                                @if ($order->status === 'completed') bg-medical-green-100 text-medical-green-700
                                                @elseif($order->status === 'pending') bg-medical-yellow-100 text-medical-yellow-700
                                                @else bg-medical-gray-100 text-medical-gray-600 @endif">
                                    {{ $order->status }}
                                </span>
                            </div>

                            <p class="text-medical-blue-600 font-semibold mt-2">
                                {{ number_format($order->total_amount, 2) }} د.ل
                            </p>

                            <a href="{{ route('admin.orders.show', $order) }}"
                                class="text-medical-blue-600 hover:text-medical-blue-700 text-sm mt-2 inline-block">
                                عرض التفاصيل
                            </a>
                        </div>
                    @endforeach
                @else
                    <p class="text-medical-gray-500">لا يوجد طلبات.</p>
                @endif

            </div>

            {{-- INVOICES TAB --}}
            <div x-show="activeTab === 'invoices'" x-transition style="display: none;">

                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">الفواتير</h3>

                @if ($buyer->invoices->count() > 0)
                    @foreach ($buyer->invoices as $invoice)
                        <div class="p-4 border rounded-xl mb-3 bg-white shadow-sm">

                            <p class="font-medium text-medical-gray-900">
                                فاتورة #{{ $invoice->id }}
                            </p>

                            <p class="text-medical-gray-600 text-sm mt-1">
                                تاريخ: {{ $invoice->created_at->format('Y-m-d') }}
                            </p>

                            <p class="text-medical-green-700 font-semibold mt-2">
                                {{ number_format($invoice->amount, 2) }} د.ل
                            </p>

                            <a href="{{ route('admin.invoices.show', $invoice) }}"
                                class="text-medical-blue-600 hover:text-medical-blue-700 text-sm mt-2 inline-block">
                                عرض التفاصيل
                            </a>

                        </div>
                    @endforeach
                @else
                    <p class="text-medical-gray-500">لا توجد فواتير.</p>
                @endif

            </div>

            {{-- DOCUMENTS TAB --}}
            <div x-show="activeTab === 'documents'" x-transition style="display: none;">

                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">مستندات المشتري</h3>

                @php
                    $license = $buyer->getMedia('license_documents')->first();
                @endphp

                @if ($license)
                    <div class="space-y-2">
                        <a href="{{ $license->getUrl() }}" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-xl hover:bg-medical-blue-100 transition">
                            عرض المستند
                        </a>

                        <p class="text-xs text-medical-gray-500">
                            {{ $license->file_name }} — {{ number_format($license->size / 1024, 1) }} KB
                        </p>
                    </div>
                @else
                    <p class="text-medical-gray-500">لا يوجد مستندات.</p>
                @endif

            </div>

            {{-- ACTIVITY TAB --}}
            <div x-show="activeTab === 'activity'" x-transition style="display: none;">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">سجل النشاط</h3>

                @if ($buyer->activities->count() > 0)
                    <ul class="divide-y divide-medical-gray-100">
                        @foreach ($buyer->activities as $activity)
                            <li class="py-4">
                                <p class="font-medium text-medical-gray-900">
                                    {{ $activity->description }}
                                </p>
                                <p class="text-xs text-medical-gray-500 mt-1">
                                    {{ $activity->created_at->translatedFormat('Y/m/d - h:i A') }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-medical-gray-500">لا يوجد نشاط.</p>
                @endif
            </div>

        </div>
    </div>

</x-dashboard.layout>
