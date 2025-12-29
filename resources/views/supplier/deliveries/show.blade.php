{{-- Supplier Deliveries - Show --}}
<x-dashboard.layout title="تفاصيل التسليم" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل التسليم</h1>
                <p class="mt-2 text-medical-gray-600">{{ $delivery->delivery_number }}</p>
            </div>
            <a href="{{ route('supplier.deliveries.index') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-medical-green-50 border border-medical-green-200 rounded-xl text-medical-green-700">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-xl text-red-700">
            <ul class="list-disc pr-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                        <p class="text-medical-gray-900 mt-1 dir-ltr">{{ $delivery->receiver_phone }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            @if($delivery->order && $delivery->order->items->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                        عناصر الطلب
                    </h3>

                    <div class="space-y-4">
                        @foreach($delivery->order->items as $item)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-medical-gray-200 rounded-lg flex items-center justify-center">
                                        @if($item->product && $item->product->hasMedia('product_images'))
                                            <img src="{{ $item->product->getFirstMediaUrl('product_images', 'thumb') }}" 
                                                alt="{{ $item->product->name }}" 
                                                class="w-full h-full object-cover rounded-lg">
                                        @else
                                            <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                            </svg>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $item->product?->name ?? $item->item_name ?? 'منتج' }}</p>
                                        <p class="text-sm text-medical-gray-500">الكمية: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <p class="font-bold text-medical-blue-600">{{ number_format($item->total_price, 2) }} د.ل</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Delivery Proofs --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    إثباتات التسليم
                </h3>

                @if($delivery->hasMedia('delivery_proofs'))
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                        @foreach($delivery->getMedia('delivery_proofs') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank" class="group relative">
                                @if(str_starts_with($media->mime_type, 'image/'))
                                    <img src="{{ $media->getUrl('thumb') }}" alt="إثبات التسليم"
                                        class="w-full h-32 object-cover rounded-xl border border-medical-gray-200 group-hover:opacity-90 transition-opacity">
                                @else
                                    <div class="w-full h-32 bg-medical-gray-100 rounded-xl border border-medical-gray-200 flex items-center justify-center group-hover:bg-medical-gray-200 transition-colors">
                                        <svg class="w-12 h-12 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <span class="absolute bottom-2 right-2 bg-black/50 text-white text-xs px-2 py-1 rounded">
                                    {{ $media->human_readable_size }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-medical-gray-50 rounded-xl mb-6">
                        <svg class="mx-auto h-12 w-12 text-medical-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <p class="text-medical-gray-500 font-medium">لا توجد إثباتات مرفوعة بعد</p>
                        <p class="text-sm text-medical-gray-400 mt-1">يمكنك رفع صور أو ملفات PDF كإثبات للتسليم</p>
                    </div>
                @endif

                {{-- Upload Form --}}
                @if($delivery->status !== 'delivered')
                    <form action="{{ route('supplier.deliveries.upload-proof', $delivery) }}" method="POST" enctype="multipart/form-data"
                        class="pt-4 border-t border-medical-gray-200">
                        @csrf
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">رفع إثبات تسليم</label>
                        <div class="flex gap-3">
                            <input type="file" name="proof" accept="image/*,application/pdf" required
                                class="flex-1 text-sm text-medical-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-medical-blue-50 file:text-medical-blue-700 hover:file:bg-medical-blue-100 cursor-pointer">
                            <button type="submit"
                                class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                                رفع
                            </button>
                        </div>
                        <p class="mt-2 text-xs text-medical-gray-500">صور أو PDF - الحد الأقصى 10MB</p>
                    </form>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Update Status --}}
            @if($delivery->status !== 'delivered')
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        تحديث الحالة
                    </h3>

                    <form action="{{ route('supplier.deliveries.update-status', $delivery) }}" method="POST">
                        @csrf
                        @method('PATCH')

                        <div class="space-y-3">
                            <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-medical-gray-50
                                {{ $delivery->status === 'pending' ? 'border-medical-yellow-500 bg-medical-yellow-50' : 'border-medical-gray-200' }}">
                                <input type="radio" name="status" value="pending" {{ $delivery->status === 'pending' ? 'checked' : '' }}
                                    class="w-4 h-4 text-medical-yellow-600 focus:ring-medical-yellow-500">
                                <span class="mr-3 font-medium text-medical-gray-700">قيد الانتظار</span>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-medical-gray-50
                                {{ $delivery->status === 'in_transit' ? 'border-medical-purple-500 bg-medical-purple-50' : 'border-medical-gray-200' }}">
                                <input type="radio" name="status" value="in_transit" {{ $delivery->status === 'in_transit' ? 'checked' : '' }}
                                    class="w-4 h-4 text-medical-purple-600 focus:ring-medical-purple-500">
                                <span class="mr-3 font-medium text-medical-gray-700">قيد التوصيل</span>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-medical-gray-50
                                {{ $delivery->status === 'delivered' ? 'border-medical-green-500 bg-medical-green-50' : 'border-medical-gray-200' }}">
                                <input type="radio" name="status" value="delivered" {{ $delivery->status === 'delivered' ? 'checked' : '' }}
                                    class="w-4 h-4 text-medical-green-600 focus:ring-medical-green-500">
                                <span class="mr-3 font-medium text-medical-gray-700">تم التسليم</span>
                            </label>

                            <label class="flex items-center p-3 rounded-xl border-2 cursor-pointer transition-all hover:bg-medical-gray-50
                                {{ $delivery->status === 'failed' ? 'border-medical-red-500 bg-medical-red-50' : 'border-medical-gray-200' }}">
                                <input type="radio" name="status" value="failed" {{ $delivery->status === 'failed' ? 'checked' : '' }}
                                    class="w-4 h-4 text-medical-red-600 focus:ring-medical-red-500">
                                <span class="mr-3 font-medium text-medical-gray-700">فشل التسليم</span>
                            </label>
                        </div>

                        <button type="submit"
                            class="w-full mt-4 px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold">
                            تحديث الحالة
                        </button>
                    </form>
                </div>
            @endif

            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات الطلب
                </h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                        <a href="{{ route('supplier.orders.show', $delivery->order) }}"
                            class="font-mono font-semibold text-medical-blue-600 hover:text-medical-blue-700 mt-1 block">
                            {{ $delivery->order->order_number ?? 'N/A' }}
                        </a>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">المشتري</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $delivery->buyer?->organization_name ?? 'غير محدد' }}</p>
                        @if($delivery->buyer)
                            @if($delivery->buyer->contact_email)
                                <p class="text-sm text-medical-gray-500 mt-1">{{ $delivery->buyer->contact_email }}</p>
                            @endif
                            @if($delivery->buyer->contact_phone)
                                <p class="text-sm text-medical-gray-500">{{ $delivery->buyer->contact_phone }}</p>
                            @endif
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">حالة الطلب</p>
                        @php
                            $orderStatusClasses = [
                                'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                'processing' => 'bg-medical-blue-100 text-medical-blue-700',
                                'shipped' => 'bg-medical-purple-100 text-medical-purple-700',
                                'delivered' => 'bg-medical-green-100 text-medical-green-700',
                                'cancelled' => 'bg-medical-red-100 text-medical-red-700',
                            ];
                            $orderStatusLabels = [
                                'pending' => 'قيد الانتظار',
                                'processing' => 'قيد المعالجة',
                                'shipped' => 'تم الشحن',
                                'delivered' => 'تم التسليم',
                                'cancelled' => 'ملغى',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium mt-1 {{ $orderStatusClasses[$delivery->order->status ?? ''] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                            {{ $orderStatusLabels[$delivery->order->status ?? ''] ?? $delivery->order->status ?? 'غير محدد' }}
                        </span>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي الطلب</p>
                        <p class="font-bold text-medical-green-600 mt-1">{{ number_format($delivery->order->total_amount ?? 0, 2) }} د.ل</p>
                    </div>
                </div>
            </div>

            {{-- Verification Status --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    حالة التحقق
                </h3>

                @if($delivery->is_verified)
                    <div class="flex items-center gap-3 p-4 bg-medical-green-50 rounded-xl">
                        <svg class="w-8 h-8 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-medical-green-700">تم التحقق</p>
                            <p class="text-sm text-medical-green-600">{{ $delivery->verified_at?->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-3 p-4 bg-medical-yellow-50 rounded-xl">
                        <svg class="w-8 h-8 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-medical-yellow-700">في انتظار التحقق</p>
                            <p class="text-sm text-medical-yellow-600">سيتم التحقق من قبل المشتري</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

</x-dashboard.layout>

