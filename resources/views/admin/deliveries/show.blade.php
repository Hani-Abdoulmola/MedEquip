{{-- Admin Deliveries Management - View Delivery Details --}}
<x-dashboard.layout title="تفاصيل التسليم" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل التسليم</h1>
                <p class="mt-2 text-medical-gray-600">{{ $delivery->delivery_number }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.deliveries.edit', $delivery->id) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-yellow-600 text-white rounded-xl hover:bg-medical-yellow-700 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('admin.deliveries.index') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Delivery Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    معلومات التسليم
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم التسليم</p>
                        <p class="font-mono font-bold text-medical-gray-900 mt-1">{{ $delivery->delivery_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ التسليم</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->delivery_date?->format('Y-m-d') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">موقع التسليم</p>
                        <p class="text-medical-gray-900 mt-1">{{ $delivery->delivery_location }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">الحالة</p>
                        <div class="mt-1">
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                    'in_transit' => 'bg-medical-purple-100 text-medical-purple-700',
                                    'delivered' => 'bg-medical-green-100 text-medical-green-700',
                                    'failed' => 'bg-medical-red-100 text-medical-red-700',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد الانتظار',
                                    'in_transit' => 'قيد التوصيل',
                                    'delivered' => 'تم التسليم',
                                    'failed' => 'فشل التسليم',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium {{ $statusClasses[$delivery->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                {{ $statusLabels[$delivery->status] ?? $delivery->status }}
                            </span>
                        </div>
                    </div>
                </div>

                @if($delivery->notes)
                    <div class="mt-6 pt-6 border-t border-medical-gray-200">
                        <p class="text-sm text-medical-gray-600 mb-2">ملاحظات</p>
                        <p class="text-medical-gray-900">{{ $delivery->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Receiver Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    معلومات المستلم
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">اسم المستلم</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->receiver_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">هاتف المستلم</p>
                        <p class="text-medical-gray-900 mt-1">{{ $delivery->receiver_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    معلومات الطلب
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                        <a href="{{ route('admin.orders.show', $delivery->order_id) }}"
                            class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                            {{ $delivery->order->order_number ?? 'N/A' }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->order->order_date?->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    حالة التسليم
                </h3>
                @php
                    $statusConfig = [
                        'pending' => ['label' => 'قيد الانتظار', 'color' => 'yellow'],
                        'in_transit' => ['label' => 'قيد التوصيل', 'color' => 'purple'],
                        'delivered' => ['label' => 'تم التسليم', 'color' => 'green'],
                        'failed' => ['label' => 'فاشلة', 'color' => 'red'],
                    ];
                    $config = $statusConfig[$delivery->status] ?? ['label' => $delivery->status, 'color' => 'gray'];
                @endphp
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-medical-{{ $config['color'] }}-100 text-medical-{{ $config['color'] }}-700">
                    {{ $config['label'] }}
                </span>
            </div>

            {{-- Buyer & Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات الأطراف
                </h3>
                <div class="space-y-3">
                    @if($delivery->buyer)
                        <div>
                            <p class="text-sm text-medical-gray-600">المشتري</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->buyer->organization_name }}</p>
                        </div>
                    @endif
                    @if($delivery->supplier)
                        <div>
                            <p class="text-sm text-medical-gray-600">المورد</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->supplier->company_name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

