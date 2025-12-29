{{-- Supplier Deliveries - Create --}}
<x-dashboard.layout title="إنشاء سجل تسليم" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إنشاء سجل تسليم</h1>
                <p class="mt-2 text-medical-gray-600">للطلب: {{ $order->order_number }}</p>
            </div>
            <a href="{{ route('supplier.orders.show', $order) }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للطلب</span>
            </a>
        </div>
    </div>

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <ul class="list-disc pr-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-medical p-8">
                <form method="POST" action="{{ route('supplier.deliveries.store', $order) }}">
                    @csrf

                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                        معلومات التسليم
                    </h2>

                    <div class="space-y-6">
                        {{-- Delivery Date --}}
                        <div>
                            <label for="delivery_date" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                تاريخ التسليم المتوقع <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="delivery_date" name="delivery_date" 
                                value="{{ old('delivery_date', date('Y-m-d')) }}"
                                min="{{ date('Y-m-d') }}" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                        </div>

                        {{-- Delivery Location --}}
                        <div>
                            <label for="delivery_location" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                موقع التسليم <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="delivery_location" name="delivery_location" 
                                value="{{ old('delivery_location', $order->buyer?->address) }}"
                                placeholder="العنوان الكامل للتسليم" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                        </div>

                        {{-- Receiver Name --}}
                        <div>
                            <label for="receiver_name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                اسم المستلم <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="receiver_name" name="receiver_name" 
                                value="{{ old('receiver_name', $order->buyer?->contact_person) }}"
                                placeholder="اسم الشخص الذي سيستلم" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                        </div>

                        {{-- Receiver Phone --}}
                        <div>
                            <label for="receiver_phone" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                هاتف المستلم <span class="text-red-500">*</span>
                            </label>
                            <input type="tel" id="receiver_phone" name="receiver_phone" 
                                value="{{ old('receiver_phone', $order->buyer?->contact_phone) }}"
                                placeholder="رقم الهاتف للتواصل" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                        </div>

                        {{-- Notes --}}
                        <div>
                            <label for="notes" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                ملاحظات (اختياري)
                            </label>
                            <textarea id="notes" name="notes" rows="4"
                                placeholder="أي ملاحظات إضافية عن التسليم..."
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">{{ old('notes') }}</textarea>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="flex items-center justify-end gap-4 pt-6 mt-6 border-t border-medical-gray-200">
                        <a href="{{ route('supplier.orders.show', $order) }}"
                            class="px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                            إلغاء
                        </a>
                        <button type="submit"
                            class="px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold shadow-sm">
                            إنشاء سجل التسليم
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Order Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات الطلب
                </h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                        <p class="font-mono font-bold text-medical-gray-900 mt-1">{{ $order->order_number }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">المشتري</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $order->buyer?->organization_name ?? 'غير محدد' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي الطلب</p>
                        <p class="font-bold text-medical-green-600 mt-1">{{ number_format($order->total_amount, 2) }} د.ل</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                        <p class="text-medical-gray-900 mt-1">{{ $order->order_date?->format('Y-m-d') }}</p>
                    </div>
                </div>
            </div>

            {{-- Order Items --}}
            @if($order->items->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        عناصر الطلب
                    </h3>

                    <ul class="space-y-3">
                        @foreach($order->items as $item)
                            <li class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-medium text-medical-gray-900">{{ $item->product?->name ?? 'منتج' }}</p>
                                    <p class="text-sm text-medical-gray-500">الكمية: {{ $item->quantity }}</p>
                                </div>
                                <p class="font-semibold text-medical-blue-600">{{ number_format($item->total_price, 2) }} د.ل</p>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Tips --}}
            <div class="bg-medical-blue-50 rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-medical-blue-900 mb-3">معلومات</h3>
                <ul class="space-y-2 text-sm text-medical-blue-700">
                    <li class="flex items-start">
                        <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>سيتم إنشاء رقم تسليم تلقائياً</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>يمكنك رفع إثبات التسليم لاحقاً</span>
                    </li>
                    <li class="flex items-start">
                        <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                        <span>المشتري سيتحقق من التسليم</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>

</x-dashboard.layout>

