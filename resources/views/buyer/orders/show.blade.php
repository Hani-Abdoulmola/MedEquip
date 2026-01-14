{{-- Buyer Orders - Show --}}
<x-dashboard.layout title="تفاصيل الطلب" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('buyer.orders.index') }}" class="text-medical-gray-500 hover:text-medical-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل الطلب</h1>
                </div>
                <p class="text-medical-gray-600">رقم الطلب: <span class="font-semibold">{{ $order->order_number }}</span></p>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                        'processing' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'shipped' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                        'delivered' => 'bg-green-100 text-green-800 border-green-300',
                        'cancelled' => 'bg-red-100 text-red-800 border-red-300',
                    ];
                    $statusLabels = [
                        'pending' => 'قيد الانتظار',
                        'processing' => 'قيد المعالجة',
                        'shipped' => 'تم الشحن',
                        'delivered' => 'تم التسليم',
                        'cancelled' => 'ملغى',
                    ];
                @endphp
                <span class="px-4 py-2 rounded-xl text-sm font-semibold border {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                    {{ $statusLabels[$order->status] ?? $order->status }}
                </span>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
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
        {{-- Order Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات الطلب</h2>
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $order->order_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $order->order_date?->format('Y/m/d H:i') ?? $order->created_at->format('Y/m/d H:i') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي المبلغ</p>
                        <p class="font-bold text-medical-green-600 text-xl mt-1">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-600">العملة</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $order->currency }}</p>
                    </div>
                    @if($order->quotation)
                        <div>
                            <p class="text-sm text-medical-gray-600">عرض السعر</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $order->quotation->reference_code }}</p>
                        </div>
                        @if($order->quotation->rfq)
                            <div>
                                <p class="text-sm text-medical-gray-600">طلب عرض السعر</p>
                                <a href="{{ route('buyer.rfqs.show', $order->quotation->rfq) }}" class="font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                                    {{ $order->quotation->rfq->reference_code }}
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
                @if($order->notes)
                    <div class="mt-6 pt-6 border-t border-medical-gray-100">
                        <p class="text-sm text-medical-gray-600 mb-2">ملاحظات</p>
                        <p class="text-medical-gray-900">{{ $order->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                <div class="p-6 border-b border-medical-gray-100">
                    <h2 class="text-lg font-semibold text-medical-gray-900">بنود الطلب</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">السعر</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-medical-gray-100">
                            @foreach($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-medical-gray-900">{{ $item->item_name ?? $item->product?->name ?? 'منتج' }}</p>
                                        @if($item->specifications)
                                            <p class="text-sm text-medical-gray-600 mt-1">{{ Str::limit($item->specifications, 50) }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-medical-gray-900">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="px-6 py-4 text-medical-gray-900">{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-6 py-4 font-semibold text-medical-gray-900">{{ number_format($item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-medical-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-left font-bold text-medical-gray-900">الإجمالي</td>
                                <td class="px-6 py-4 font-bold text-medical-green-600 text-lg">{{ number_format($order->total_amount, 2) }} {{ $order->currency }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات المورد</h2>
                @if($order->supplier)
                    <div class="flex items-center mb-4">
                        <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center ml-4">
                            <span class="text-lg font-bold text-medical-blue-600">
                                {{ mb_substr($order->supplier->company_name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-semibold text-medical-gray-900">{{ $order->supplier->company_name }}</p>
                            @if($order->supplier->user)
                                <p class="text-sm text-medical-gray-600">{{ $order->supplier->user->email }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-3 text-sm">
                        @if($order->supplier->contact_phone)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                                <span>{{ $order->supplier->contact_phone }}</span>
                            </div>
                        @endif
                        @if($order->supplier->contact_email)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $order->supplier->contact_email }}</span>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-medical-gray-600">لا تتوفر معلومات المورد</p>
                @endif
            </div>

            {{-- Quick Actions --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold text-medical-gray-900 mb-4">إجراءات سريعة</h2>
                <div class="space-y-3">
                    @if($order->invoices->isNotEmpty())
                        <a href="{{ route('buyer.invoices.show', $order->invoices->first()) }}" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-blue-50 text-medical-blue-600 rounded-xl hover:bg-medical-blue-100 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            عرض الفاتورة
                        </a>
                    @endif
                    @if($order->deliveries->isNotEmpty())
                        <a href="{{ route('buyer.deliveries.show', $order->deliveries->first()) }}" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-colors font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                            </svg>
                            تتبع التوصيل
                        </a>
                    @endif
                    <a href="{{ route('buyer.orders.index') }}" 
                       class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        العودة للطلبات
                    </a>
                </div>
            </div>

            {{-- Order Progress Timeline --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-lg font-semibold text-medical-gray-900 mb-4">تتبع الطلب</h2>
                @php
                    $statusOrder = ['pending', 'processing', 'shipped', 'delivered'];
                    $currentIndex = array_search($order->status, $statusOrder);
                    if ($order->status === 'cancelled') {
                        $currentIndex = -1; // Cancelled
                    }
                    
                    $timelineSteps = [
                        ['key' => 'pending', 'label' => 'قيد الانتظار', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'yellow'],
                        ['key' => 'processing', 'label' => 'قيد المعالجة', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z', 'color' => 'blue'],
                        ['key' => 'shipped', 'label' => 'تم الشحن', 'icon' => 'M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4', 'color' => 'indigo'],
                        ['key' => 'delivered', 'label' => 'تم التسليم', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'green'],
                    ];
                @endphp
                
                @if($order->status === 'cancelled')
                    <div class="flex items-center gap-3 p-4 bg-red-50 rounded-xl">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-red-800">تم إلغاء الطلب</p>
                            <p class="text-sm text-red-600">{{ $order->updated_at->format('Y/m/d H:i') }}</p>
                        </div>
                    </div>
                @else
                    <div class="relative">
                        @foreach($timelineSteps as $index => $step)
                            @php
                                $isCompleted = $currentIndex !== false && $index <= $currentIndex;
                                $isCurrent = $index === $currentIndex;
                                $colorClasses = [
                                    'yellow' => $isCompleted ? 'bg-yellow-500 text-white' : 'bg-gray-100 text-gray-400',
                                    'blue' => $isCompleted ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400',
                                    'indigo' => $isCompleted ? 'bg-indigo-500 text-white' : 'bg-gray-100 text-gray-400',
                                    'green' => $isCompleted ? 'bg-green-500 text-white' : 'bg-gray-100 text-gray-400',
                                ];
                                $lineColor = $isCompleted && $index < count($timelineSteps) - 1 ? 'bg-green-400' : 'bg-gray-200';
                            @endphp
                            <div class="flex items-start gap-3 {{ $index < count($timelineSteps) - 1 ? 'pb-6' : '' }} relative">
                                {{-- Connector Line --}}
                                @if($index < count($timelineSteps) - 1)
                                    <div class="absolute right-4 top-10 w-0.5 h-full {{ $isCompleted && $index + 1 <= $currentIndex ? 'bg-green-400' : 'bg-gray-200' }}"></div>
                                @endif
                                
                                {{-- Icon --}}
                                <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 z-10 {{ $colorClasses[$step['color']] }} {{ $isCurrent ? 'ring-2 ring-offset-2 ring-' . $step['color'] . '-400' : '' }}">
                                    @if($isCompleted)
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"></path>
                                        </svg>
                                    @endif
                                </div>
                                
                                {{-- Content --}}
                                <div class="flex-1">
                                    <p class="font-medium {{ $isCompleted ? 'text-gray-900' : 'text-gray-400' }}">{{ $step['label'] }}</p>
                                    @if($isCurrent)
                                        <p class="text-xs text-{{ $step['color'] }}-600 mt-0.5">الحالة الحالية</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Activity Timeline --}}
            @if($order->deliveries->isNotEmpty() || $order->invoices->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-lg font-semibold text-medical-gray-900 mb-4">سجل الأنشطة</h2>
                    <div class="space-y-4">
                        {{-- Order Created --}}
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-medical-gray-900">تم إنشاء الطلب</p>
                                <p class="text-sm text-medical-gray-600">{{ $order->created_at->format('Y/m/d H:i') }}</p>
                            </div>
                        </div>
                        
                        @foreach($order->invoices->sortBy('created_at') as $invoice)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-medical-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-medical-gray-900">تم إصدار فاتورة</p>
                                    <p class="text-sm text-medical-gray-600">رقم: {{ $invoice->invoice_number }}</p>
                                    <p class="text-xs text-medical-gray-400">{{ $invoice->created_at->format('Y/m/d H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                        
                        @foreach($order->deliveries->sortBy('created_at') as $delivery)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium text-medical-gray-900">تحديث التوصيل</p>
                                    @if($delivery->tracking_number)
                                        <p class="text-sm text-medical-gray-600">رقم التتبع: {{ $delivery->tracking_number }}</p>
                                    @endif
                                    @if($delivery->status)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800 mt-1">
                                            {{ $delivery->status }}
                                        </span>
                                    @endif
                                    <p class="text-xs text-medical-gray-400 mt-1">{{ $delivery->created_at->format('Y/m/d H:i') }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

