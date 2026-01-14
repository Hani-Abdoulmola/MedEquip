{{-- Buyer Deliveries - Show --}}
<x-dashboard.layout title="تفاصيل التوصيل" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('buyer.deliveries.index') }}" class="text-medical-gray-500 hover:text-medical-gray-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h1 class="text-3xl font-bold text-medical-gray-900">تتبع الشحنة</h1>
        </div>
        @if(in_array($delivery->status, ['pending', 'in_transit']))
            <form action="{{ route('buyer.deliveries.confirm', $delivery) }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    تأكيد الاستلام
                </button>
            </form>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Delivery Details --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold mb-4">معلومات الشحنة</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم التسليم</p>
                        <p class="font-semibold mt-1">{{ $delivery->delivery_number ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">موقع التسليم</p>
                        <p class="font-semibold mt-1">{{ $delivery->delivery_location ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ التوصيل</p>
                        <p class="font-semibold mt-1">{{ $delivery->delivery_date?->format('Y/m/d') ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">الحالة</p>
                        @php
                            $colors = ['pending' => 'text-yellow-600', 'in_transit' => 'text-blue-600', 'delivered' => 'text-green-600', 'failed' => 'text-red-600'];
                            $labels = ['pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم', 'failed' => 'فشل التسليم'];
                        @endphp
                        <p class="font-semibold mt-1 {{ $colors[$delivery->status] ?? '' }}">{{ $labels[$delivery->status] ?? $delivery->status }}</p>
                    </div>
                    @if($delivery->is_verified)
                        <div>
                            <p class="text-sm text-medical-gray-600">تاريخ التحقق</p>
                            <p class="font-semibold mt-1">{{ $delivery->verified_at?->format('Y/m/d H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-medical-gray-600">استلمه</p>
                            <p class="font-semibold mt-1">{{ $delivery->receiver_name ?? 'غير محدد' }}</p>
                        </div>
                    @endif
                </div>
                @if($delivery->notes)
                    <div class="mt-6 pt-6 border-t border-medical-gray-100">
                        <p class="text-sm text-medical-gray-600 mb-2">ملاحظات</p>
                        <p class="text-medical-gray-900">{{ $delivery->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Order Items --}}
            @if($delivery->order && $delivery->order->items->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                    <div class="p-6 border-b"><h2 class="text-lg font-semibold">بنود الشحنة</h2></div>
                    <table class="w-full">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600">الكمية</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-medical-gray-100">
                            @foreach($delivery->order->items as $item)
                                <tr>
                                    <td class="px-6 py-4 font-semibold">{{ $item->item_name ?? $item->product?->name }}</td>
                                    <td class="px-6 py-4">{{ $item->quantity }} {{ $item->unit }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Delivery Proof Images --}}
            @if($delivery->getMedia('delivery_proof')->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-lg font-semibold mb-4">صور إثبات التسليم</h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($delivery->getMedia('delivery_proof') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank" class="block">
                                <img src="{{ $media->getUrl('thumb') }}" alt="Delivery Proof" class="rounded-xl w-full h-32 object-cover">
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold mb-4">المورد</h2>
                @if($delivery->order?->supplier)
                    <p class="font-semibold">{{ $delivery->order->supplier->company_name }}</p>
                    <p class="text-sm text-medical-gray-600 mt-1">{{ $delivery->order->supplier->contact_phone }}</p>
                @endif
            </div>

            {{-- Order Link --}}
            @if($delivery->order)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-lg font-semibold mb-4">الطلب المرتبط</h2>
                    <a href="{{ route('buyer.orders.show', $delivery->order) }}" class="text-medical-blue-600 hover:text-medical-blue-700 font-semibold">
                        {{ $delivery->order->order_number }}
                    </a>
                </div>
            @endif

            {{-- Status Timeline --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold mb-4">حالة الشحنة</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $delivery->status !== 'failed' ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $delivery->status !== 'failed' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="font-medium">تم إنشاء الشحنة</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ in_array($delivery->status, ['in_transit', 'delivered']) ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ in_array($delivery->status, ['in_transit', 'delivered']) ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="font-medium">في الطريق</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $delivery->status === 'delivered' ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $delivery->status === 'delivered' ? 'text-green-600' : 'text-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span class="font-medium">تم التسليم</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

