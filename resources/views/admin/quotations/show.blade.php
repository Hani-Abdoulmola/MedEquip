{{-- Admin Quotation - Show Details --}}
<x-dashboard.layout title="تفاصيل عرض السعر" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.quotations.index') }}"
                    class="p-2 bg-medical-gray-100 hover:bg-medical-gray-200 rounded-xl transition-colors">
                    <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">عرض سعر</h1>
                    <p class="mt-1 text-medical-gray-600 font-mono">{{ $quotation->reference_code }}</p>
                </div>
            </div>

            {{-- Status Badge --}}
            @php
                $statusClasses = [
                    'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                    'accepted' => 'bg-medical-green-100 text-medical-green-700',
                    'rejected' => 'bg-medical-red-100 text-medical-red-700',
                ];
                $statusLabels = [
                    'pending' => 'قيد المراجعة',
                    'accepted' => 'مقبول',
                    'rejected' => 'مرفوض',
                ];
            @endphp
            <span class="px-6 py-2 rounded-full text-lg font-semibold {{ $statusClasses[$quotation->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                {{ $statusLabels[$quotation->status] ?? $quotation->status }}
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Quotation Overview --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    ملخص العرض
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 bg-medical-green-50 rounded-xl">
                        <p class="text-sm text-medical-green-600 mb-1">إجمالي السعر</p>
                        <p class="text-3xl font-bold text-medical-green-700">{{ number_format($quotation->total_price, 2) }} د.ل</p>
                    </div>

                    <div class="p-4 bg-medical-blue-50 rounded-xl">
                        <p class="text-sm text-medical-blue-600 mb-1">مدة التوصيل</p>
                        <p class="text-2xl font-bold text-medical-blue-700">{{ $quotation->lead_time ?? 'غير محدد' }} يوم</p>
                    </div>

                    <div class="p-4 bg-medical-purple-50 rounded-xl">
                        <p class="text-sm text-medical-purple-600 mb-1">فترة الضمان</p>
                        <p class="text-2xl font-bold text-medical-purple-700">{{ $quotation->warranty_period ?? 'غير محدد' }} شهر</p>
                    </div>

                    <div class="p-4 bg-medical-gray-50 rounded-xl">
                        <p class="text-sm text-medical-gray-600 mb-1">تاريخ التقديم</p>
                        <p class="text-lg font-bold text-medical-gray-700">{{ $quotation->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-sm text-medical-gray-500">{{ $quotation->created_at->diffForHumans() }}</p>
                    </div>
                </div>

                @if($quotation->notes)
                    <div class="mt-6 p-4 bg-medical-yellow-50 rounded-xl">
                        <p class="text-sm text-medical-yellow-700 font-medium mb-1">ملاحظات المورد</p>
                        <p class="text-medical-yellow-800">{{ $quotation->notes }}</p>
                    </div>
                @endif

                @if($quotation->status === 'rejected' && $quotation->rejection_reason)
                    <div class="mt-6 p-4 bg-medical-red-50 rounded-xl">
                        <p class="text-sm text-medical-red-700 font-medium mb-1">سبب الرفض</p>
                        <p class="text-medical-red-800">{{ $quotation->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Quotation Items --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    تفاصيل التسعير ({{ $quotation->items->count() }} عنصر)
                </h2>

                @if($quotation->items && $quotation->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">#</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">سعر الوحدة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الإجمالي</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">التوصيل</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الضمان</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-medical-gray-200">
                                @php $totalPrice = 0; @endphp
                                @foreach($quotation->items as $index => $item)
                                    @php $itemTotal = $item->unit_price * $item->quantity; $totalPrice += $itemTotal; @endphp
                                    <tr class="hover:bg-medical-gray-50">
                                        <td class="px-4 py-3 text-medical-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-medical-gray-900">{{ $item->rfqItem->product_name ?? 'منتج' }}</p>
                                            @if($item->notes)
                                                <p class="text-xs text-medical-gray-500 mt-1">{{ $item->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-medical-gray-900">{{ number_format($item->unit_price, 2) }} د.ل</td>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 font-bold text-medical-green-600">{{ number_format($itemTotal, 2) }} د.ل</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->lead_time ?? '-' }} يوم</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->warranty ?? '-' }} شهر</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-medical-gray-50">
                                <tr>
                                    <td colspan="4" class="px-4 py-3 text-left font-bold text-medical-gray-900">الإجمالي</td>
                                    <td class="px-4 py-3 font-bold text-medical-green-700 text-lg">{{ number_format($totalPrice, 2) }} د.ل</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    {{-- Legacy: Single price without items --}}
                    <div class="text-center py-8 bg-medical-gray-50 rounded-xl">
                        <p class="text-medical-gray-600">عرض سعر إجمالي بدون تفاصيل</p>
                        <p class="text-2xl font-bold text-medical-green-600 mt-2">{{ number_format($quotation->total_price, 2) }} د.ل</p>
                    </div>
                @endif
            </div>

            {{-- Related RFQ --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    طلب عرض السعر المرتبط
                </h2>

                @if($quotation->rfq)
                    <div class="p-4 border border-medical-gray-200 rounded-xl">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-mono text-sm text-medical-blue-600">{{ $quotation->rfq->reference_code }}</p>
                                <p class="font-bold text-medical-gray-900 mt-1">{{ $quotation->rfq->title }}</p>
                                <p class="text-sm text-medical-gray-600 mt-2">
                                    المشتري: {{ $quotation->rfq->buyer->organization_name ?? 'غير محدد' }}
                                </p>
                            </div>
                            <a href="{{ route('admin.rfqs.show', $quotation->rfq) }}"
                                class="px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                عرض التفاصيل ←
                            </a>
                        </div>

                        {{-- RFQ Items --}}
                        @if($quotation->rfq->items && $quotation->rfq->items->count() > 0)
                            <div class="mt-4 pt-4 border-t border-medical-gray-200">
                                <p class="text-sm font-medium text-medical-gray-700 mb-3">العناصر المطلوبة:</p>
                                <div class="space-y-2">
                                    @foreach($quotation->rfq->items as $rfqItem)
                                        <div class="flex items-center justify-between p-2 bg-medical-gray-50 rounded-lg text-sm">
                                            <span class="text-medical-gray-700">{{ $rfqItem->product_name }}</span>
                                            <span class="font-medium text-medical-gray-900">{{ $rfqItem->quantity }} {{ $rfqItem->unit ?? 'وحدة' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <p class="text-medical-gray-500">لا يوجد طلب مرتبط</p>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    معلومات المورد
                </h3>

                @if($quotation->supplier)
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-medical-gray-500">اسم الشركة</p>
                            <p class="font-bold text-medical-gray-900">{{ $quotation->supplier->company_name }}</p>
                        </div>

                        @if($quotation->supplier->user)
                            <div>
                                <p class="text-sm text-medical-gray-500">البريد الإلكتروني</p>
                                <p class="text-medical-gray-900">{{ $quotation->supplier->user->email }}</p>
                            </div>
                        @endif

                        @if($quotation->supplier->phone)
                            <div>
                                <p class="text-sm text-medical-gray-500">الهاتف</p>
                                <p class="text-medical-gray-900">{{ $quotation->supplier->phone }}</p>
                            </div>
                        @endif

                        <div class="pt-4 border-t border-medical-gray-200">
                            <a href="{{ route('admin.suppliers.show', $quotation->supplier) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                عرض ملف المورد
                            </a>
                        </div>
                    </div>
                @else
                    <p class="text-medical-gray-500">لا توجد معلومات</p>
                @endif
            </div>

            {{-- Actions --}}
            @if($quotation->status === 'pending')
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">الإجراءات</h3>

                    {{-- Accept --}}
                    <form action="{{ route('admin.quotations.accept', $quotation) }}" method="POST" class="mb-4">
                        @csrf
                        @method('POST')
                        <div class="mb-3">
                            <label class="flex items-center gap-2 text-sm text-medical-gray-700">
                                <input type="checkbox" name="award_rfq" value="1"
                                    class="w-4 h-4 text-medical-green-600 border-medical-gray-300 rounded focus:ring-medical-green-500">
                                ترسية الطلب وإغلاقه
                            </label>
                            <p class="text-xs text-medical-gray-500 mt-1 mr-6">سيتم رفض العروض الأخرى تلقائياً</p>
                        </div>
                        <button type="submit"
                            class="w-full px-4 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-colors font-medium flex items-center justify-center gap-2"
                            onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            قبول العرض
                        </button>
                    </form>

                    {{-- Reject --}}
                    <form action="{{ route('admin.quotations.reject', $quotation) }}" method="POST" x-data="{ showReason: false }">
                        @csrf
                        @method('POST')
                        
                        <div x-show="showReason" x-transition class="mb-3">
                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">سبب الرفض (اختياري)</label>
                            <textarea name="rejection_reason" rows="3"
                                class="w-full px-4 py-2 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-red-500"
                                placeholder="اكتب سبب الرفض..."></textarea>
                        </div>

                        <button type="button" @click="showReason = true" x-show="!showReason"
                            class="w-full px-4 py-3 bg-medical-red-50 text-medical-red-700 rounded-xl hover:bg-medical-red-100 transition-colors font-medium flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            رفض العرض
                        </button>

                        <button type="submit" x-show="showReason"
                            class="w-full px-4 py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 transition-colors font-medium flex items-center justify-center gap-2"
                            onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            تأكيد الرفض
                        </button>
                    </form>
                </div>
            @endif

            {{-- Compare Quotations --}}
            @if($quotation->rfq && $quotation->rfq->quotations->count() > 1)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">مقارنة العروض</h3>
                    <p class="text-sm text-medical-gray-600 mb-4">
                        يوجد {{ $quotation->rfq->quotations->count() }} عروض لهذا الطلب
                    </p>
                    <a href="{{ route('admin.quotations.compare', ['rfq_id' => $quotation->rfq_id]) }}"
                        class="w-full inline-flex items-center justify-center px-4 py-3 bg-medical-purple-50 text-medical-purple-700 rounded-xl hover:bg-medical-purple-100 transition-colors font-medium">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        مقارنة العروض
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

