{{-- Supplier Orders - Show --}}
<x-dashboard.layout title="تفاصيل الطلب #{{ $order->order_number ?? $order->id }}" userRole="supplier" :userName="auth()->user()->name"
    userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('supplier.orders.index') }}"
                    class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-medical-gray-100 hover:bg-medical-gray-200 transition-colors">
                    <svg class="w-5 h-5 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">
                        طلب #{{ $order->order_number ?? $order->id }}
                    </h1>
                    <p class="mt-1 text-medical-gray-600">
                        {{ $order->order_date?->format('Y-m-d H:i') ?? 'تاريخ غير محدد' }}
                    </p>
                </div>
            </div>

            {{-- Status Badge --}}
            @php
                $statusColors = [
                    'pending' => 'bg-medical-yellow-100 text-medical-yellow-700 border-medical-yellow-300',
                    'processing' => 'bg-medical-blue-100 text-medical-blue-700 border-medical-blue-300',
                    'shipped' => 'bg-medical-purple-100 text-medical-purple-700 border-medical-purple-300',
                    'delivered' => 'bg-medical-green-100 text-medical-green-700 border-medical-green-300',
                    'cancelled' => 'bg-medical-red-100 text-medical-red-700 border-medical-red-300',
                ];
                $statusLabels = [
                    'pending' => 'قيد الانتظار',
                    'processing' => 'قيد المعالجة',
                    'shipped' => 'تم الشحن',
                    'delivered' => 'تم التسليم',
                    'cancelled' => 'ملغى',
                ];
            @endphp
            <span
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold border {{ $statusColors[$order->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                {{ $statusLabels[$order->status] ?? $order->status }}
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div
            class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div
            class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Order Items --}}
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                <div class="px-6 py-4 border-b border-medical-gray-200">
                    <h2 class="text-lg font-bold text-medical-gray-900">عناصر الطلب</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-medical-gray-200">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    المنتج</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    سعر الوحدة</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">
                                    الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-medical-gray-200">
                            @forelse ($order->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div>
                                            <p class="font-medium text-medical-gray-900">{{ $item->display_name }}</p>
                                            @if ($item->specifications)
                                                <p class="text-xs text-medical-gray-500 mt-1">
                                                    {{ Str::limit($item->specifications, 50) }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-medical-gray-900">{{ $item->quantity }}</span>
                                        <span class="text-medical-gray-500 text-sm">{{ $item->unit ?? 'وحدة' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-medical-gray-900">
                                        {{ number_format($item->unit_price, 2) }} {{ $order->currency ?? 'LYD' }}
                                    </td>
                                    <td class="px-6 py-4 font-semibold text-medical-gray-900">
                                        {{ number_format($item->total_price, 2) }} {{ $order->currency ?? 'LYD' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-medical-gray-500">
                                        لا توجد عناصر في هذا الطلب
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="bg-medical-gray-50">
                            <tr>
                                <td colspan="3" class="px-6 py-4 text-left font-bold text-medical-gray-900">
                                    الإجمالي الكلي
                                </td>
                                <td class="px-6 py-4 font-bold text-medical-green-600 text-lg">
                                    {{ number_format($order->total_amount, 2) }} {{ $order->currency ?? 'LYD' }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            @if ($order->notes)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-3">ملاحظات</h3>
                    <p class="text-medical-gray-700 whitespace-pre-line">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Create Delivery Link --}}
            @if ($order->status === 'shipped' && !$order->deliveries->isNotEmpty())
                <div class="bg-medical-blue-50 border border-medical-blue-200 rounded-2xl p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-medical-blue-900 mb-2">إنشاء سجل تسليم</h3>
                            <p class="text-sm text-medical-blue-700">يمكنك الآن إنشاء سجل تسليم لهذا الطلب</p>
                        </div>
                        <a href="{{ route('supplier.deliveries.create', $order) }}"
                            class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            إنشاء تسليم
                        </a>
                    </div>
                </div>
            @endif

            {{-- Invoices --}}
            @if ($order->invoices->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">الفواتير</h3>
                    <div class="space-y-3">
                        @foreach ($order->invoices as $invoice)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-medical-gray-500">
                                        {{ $invoice->invoice_date?->format('Y-m-d') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $invoice->payment_status === 'paid' ? 'bg-medical-green-100 text-medical-green-700' : ($invoice->payment_status === 'partial' ? 'bg-medical-yellow-100 text-medical-yellow-700' : 'bg-medical-red-100 text-medical-red-700') }}">
                                        {{ $invoice->payment_status === 'paid' ? 'مدفوعة' : ($invoice->payment_status === 'partial' ? 'جزئية' : 'غير مدفوعة') }}
                                    </span>
                                    <a href="{{ route('supplier.invoices.show', $invoice) }}"
                                        class="inline-flex items-center px-3 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                        عرض
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Deliveries --}}
            @if ($order->deliveries->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">عمليات التسليم</h3>
                    <div class="space-y-3">
                        @foreach ($order->deliveries as $delivery)
                            <div class="flex items-center justify-between p-4 bg-medical-gray-50 rounded-xl">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $delivery->delivery_number }}</p>
                                    <p class="text-sm text-medical-gray-500">
                                        {{ $delivery->delivery_date?->format('Y-m-d') }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $delivery->status === 'delivered' ? 'bg-medical-green-100 text-medical-green-700' : ($delivery->status === 'in_transit' ? 'bg-medical-purple-100 text-medical-purple-700' : 'bg-medical-yellow-100 text-medical-yellow-700') }}">
                                        {{ $delivery->status === 'delivered' ? 'تم التسليم' : ($delivery->status === 'in_transit' ? 'قيد النقل' : 'قيد الانتظار') }}
                                    </span>
                                    <a href="{{ route('supplier.deliveries.show', $delivery) }}"
                                        class="inline-flex items-center px-3 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                        عرض
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Update Status Form --}}
            @if (!in_array($order->status, ['delivered', 'cancelled']))
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">تحديث حالة الطلب</h3>

                    <form action="{{ route('supplier.orders.update-status', $order) }}" method="POST"
                        class="space-y-4">
                        @csrf
                        @method('PATCH')

                        {{-- Status Timeline --}}
                        <div class="flex items-center justify-between mb-6 relative">
                            @php
                                $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                                $currentIndex = array_search($order->status, $statuses);
                            @endphp

                            {{-- Progress Line --}}
                            <div class="absolute top-4 right-8 left-8 h-1 bg-medical-gray-200 -z-10"></div>
                            <div class="absolute top-4 right-8 h-1 bg-medical-green-500 -z-10"
                                style="width: {{ $currentIndex !== false ? ($currentIndex / 3) * 100 : 0 }}%"></div>

                            @foreach ($statuses as $index => $status)
                                <div class="flex flex-col items-center">
                                    <div
                                        class="w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold
                                        {{ $currentIndex !== false && $index <= $currentIndex ? 'bg-medical-green-500 text-white' : 'bg-medical-gray-200 text-medical-gray-500' }}">
                                        @if ($currentIndex !== false && $index < $currentIndex)
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </div>
                                    <span
                                        class="mt-2 text-xs font-medium {{ $order->status === $status ? 'text-medical-green-600' : 'text-medical-gray-500' }}">
                                        {{ $statusLabels[$status] }}
                                    </span>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                    الحالة الجديدة
                                </label>
                                <select name="status" id="status" required
                                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                                    @if ($order->status === 'pending')
                                        <option value="processing">قيد المعالجة - بدء التجهيز</option>
                                        <option value="cancelled">إلغاء الطلب</option>
                                    @elseif($order->status === 'processing')
                                        <option value="shipped">تم الشحن - في الطريق</option>
                                        <option value="cancelled">إلغاء الطلب</option>
                                    @elseif($order->status === 'shipped')
                                        <option value="delivered">تم التسليم - مكتمل</option>
                                    @endif
                                </select>
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-medical-gray-700 mb-2">
                                    ملاحظات (اختياري)
                                </label>
                                <input type="text" name="notes" id="notes"
                                    placeholder="أضف ملاحظة عن التحديث..."
                                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                            </div>
                        </div>

                        @error('status')
                            <p class="text-red-500 text-sm">{{ $message }}</p>
                        @enderror

                        <div class="flex justify-end">
                            <button type="submit"
                                class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                                تحديث الحالة
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Buyer Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات المشتري</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-medical-gray-500">اسم المنشأة</p>
                        <p class="font-medium text-medical-gray-900">
                            {{ $order->buyer->organization_name ?? 'غير محدد' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-medical-gray-500">نوع المنشأة</p>
                        <p class="font-medium text-medical-gray-900">
                            {{ $order->buyer->organization_type ?? 'غير محدد' }}</p>
                    </div>
                    @if ($order->buyer->contact_email)
                        <div>
                            <p class="text-xs text-medical-gray-500">البريد الإلكتروني</p>
                            <a href="mailto:{{ $order->buyer->contact_email }}"
                                class="font-medium text-medical-blue-600 hover:underline">
                                {{ $order->buyer->contact_email }}
                            </a>
                        </div>
                    @endif
                    @if ($order->buyer->contact_phone)
                        <div>
                            <p class="text-xs text-medical-gray-500">رقم الهاتف</p>
                            <a href="tel:{{ $order->buyer->contact_phone }}"
                                class="font-medium text-medical-blue-600 hover:underline">
                                {{ $order->buyer->contact_phone }}
                            </a>
                        </div>
                    @endif
                    @if ($order->buyer->address)
                        <div>
                            <p class="text-xs text-medical-gray-500">العنوان</p>
                            <p class="font-medium text-medical-gray-900">
                                {{ $order->buyer->address }}
                                @if ($order->buyer->city)
                                    , {{ $order->buyer->city }}
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Order Summary --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">ملخص الطلب</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-medical-gray-600">عدد العناصر</span>
                        <span class="font-medium text-medical-gray-900">{{ $order->items->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-medical-gray-600">إجمالي الكمية</span>
                        <span class="font-medium text-medical-gray-900">{{ $order->items->sum('quantity') }}</span>
                    </div>
                    <hr class="border-medical-gray-200">
                    <div class="flex justify-between">
                        <span class="text-medical-gray-600">المجموع الفرعي</span>
                        <span
                            class="font-medium text-medical-gray-900">{{ number_format($order->items->sum('subtotal'), 2) }}
                            {{ $order->currency ?? 'LYD' }}</span>
                    </div>
                    @if ($order->items->sum('tax_amount') > 0)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">الضريبة</span>
                            <span
                                class="font-medium text-medical-gray-900">{{ number_format($order->items->sum('tax_amount'), 2) }}
                                {{ $order->currency ?? 'LYD' }}</span>
                        </div>
                    @endif
                    @if ($order->items->sum('discount_amount') > 0)
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">الخصم</span>
                            <span
                                class="font-medium text-medical-green-600">-{{ number_format($order->items->sum('discount_amount'), 2) }}
                                {{ $order->currency ?? 'LYD' }}</span>
                        </div>
                    @endif
                    <hr class="border-medical-gray-200">
                    <div class="flex justify-between text-lg">
                        <span class="font-bold text-medical-gray-900">الإجمالي</span>
                        <span class="font-bold text-medical-green-600">{{ number_format($order->total_amount, 2) }}
                            {{ $order->currency ?? 'LYD' }}</span>
                    </div>
                </div>
            </div>

            {{-- Related Quotation --}}
            @if ($order->quotation)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">عرض السعر المرتبط</h3>
                    <div class="space-y-3">
                        <div>
                            <p class="text-xs text-medical-gray-500">رقم العرض</p>
                            <p class="font-medium text-medical-gray-900">{{ $order->quotation->reference_code }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-medical-gray-500">قيمة العرض</p>
                            <p class="font-medium text-medical-gray-900">
                                {{ number_format($order->quotation->total_price, 2) }} {{ $order->currency ?? 'LYD' }}
                            </p>
                        </div>
                        <a href="{{ route('supplier.quotations.edit', $order->quotation) }}"
                            class="inline-flex items-center text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            عرض تفاصيل العرض
                        </a>
                    </div>
                </div>
            @endif

            {{-- Timestamps --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">التواريخ</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-xs text-medical-gray-500">تاريخ الطلب</p>
                        <p class="font-medium text-medical-gray-900">
                            {{ $order->order_date?->format('Y-m-d H:i') ?? $order->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-medical-gray-500">آخر تحديث</p>
                        <p class="font-medium text-medical-gray-900">{{ $order->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>
