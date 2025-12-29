{{-- Supplier Quotation - Show Details --}}
<x-dashboard.layout title="تفاصيل عرض السعر" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('supplier.quotations.index') }}"
                    class="p-2 bg-medical-gray-100 hover:bg-medical-gray-200 rounded-xl transition-colors">
                    <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">عرض السعر</h1>
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

                    <div class="p-4 bg-medical-gray-50 rounded-xl">
                        <p class="text-sm text-medical-gray-600 mb-1">تاريخ التقديم</p>
                        <p class="text-lg font-bold text-medical-gray-700">{{ $quotation->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-sm text-medical-gray-500">{{ $quotation->created_at->diffForHumans() }}</p>
                    </div>

                    @if($quotation->valid_until)
                        <div class="p-4 bg-medical-blue-50 rounded-xl">
                            <p class="text-sm text-medical-blue-600 mb-1">صلاحية العرض حتى</p>
                            <p class="text-lg font-bold text-medical-blue-700">{{ $quotation->valid_until->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif

                    @if($quotation->updated_at && $quotation->updated_at != $quotation->created_at)
                        <div class="p-4 bg-medical-purple-50 rounded-xl">
                            <p class="text-sm text-medical-purple-600 mb-1">آخر تحديث</p>
                            <p class="text-lg font-bold text-medical-purple-700">{{ $quotation->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if($quotation->terms)
                    <div class="mt-6 p-4 bg-medical-gray-50 rounded-xl">
                        <p class="text-sm text-medical-gray-700 font-medium mb-2">الشروط والتفاصيل</p>
                        <p class="text-medical-gray-800 whitespace-pre-line">{{ $quotation->terms }}</p>
                    </div>
                @endif

                @if($quotation->status === 'rejected' && $quotation->rejection_reason)
                    <div class="mt-6 p-4 bg-medical-red-50 rounded-xl border border-medical-red-200">
                        <p class="text-sm text-medical-red-700 font-medium mb-2">سبب الرفض</p>
                        <p class="text-medical-red-800">{{ $quotation->rejection_reason }}</p>
                    </div>
                @endif
            </div>

            {{-- Quotation Items --}}
            @if($quotation->items && $quotation->items->count() > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        تفاصيل التسعير ({{ $quotation->items->count() }} عنصر)
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">#</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">سعر الوحدة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الإجمالي</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">مدة التوصيل</th>
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
                                            <p class="font-medium text-medical-gray-900">{{ $item->item_name }}</p>
                                            @if($item->specifications)
                                                <p class="text-xs text-medical-gray-500 mt-1">{{ Str::limit($item->specifications, 50) }}</p>
                                            @endif
                                            @if($item->notes)
                                                <p class="text-xs text-medical-blue-600 mt-1">📝 {{ Str::limit($item->notes, 50) }}</p>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 font-medium text-medical-gray-900">{{ number_format($item->unit_price, 2) }} د.ل</td>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->quantity }} {{ $item->unit ?? 'وحدة' }}</td>
                                        <td class="px-4 py-3 font-bold text-medical-green-600">{{ number_format($itemTotal, 2) }} د.ل</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->lead_time ?? '-' }}</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->warranty ?? '-' }}</td>
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
                </div>
            @endif

            {{-- Related RFQ --}}
            @if($quotation->rfq)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                        <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        طلب عرض السعر المرتبط
                    </h2>

                    <div class="p-4 border border-medical-gray-200 rounded-xl">
                        <div class="flex items-start justify-between">
                            <div>
                                <p class="font-mono text-sm text-medical-blue-600">{{ $quotation->rfq->reference_code }}</p>
                                <p class="font-bold text-medical-gray-900 mt-1">{{ $quotation->rfq->title }}</p>
                                @if($quotation->rfq->buyer)
                                    <p class="text-sm text-medical-gray-600 mt-2">
                                        المشتري: {{ $quotation->rfq->buyer->organization_name }}
                                    </p>
                                @endif
                                @if($quotation->rfq->deadline)
                                    <p class="text-sm text-medical-gray-600 mt-1">
                                        الموعد النهائي: {{ $quotation->rfq->deadline->format('Y-m-d H:i') }}
                                    </p>
                                @endif
                            </div>
                            <a href="{{ route('supplier.rfqs.show', $quotation->rfq) }}"
                                class="px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                عرض الطلب ←
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actions --}}
            @if($quotation->status === 'pending' && $quotation->rfq && $quotation->rfq->status === 'open')
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">الإجراءات</h3>

                    <div class="space-y-3">
                        <a href="{{ route('supplier.quotations.edit', $quotation) }}"
                            class="w-full inline-flex items-center justify-center px-4 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors font-medium">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            تعديل العرض
                        </a>

                        <form action="{{ route('supplier.quotations.destroy', $quotation) }}" method="POST"
                            onsubmit="return confirm('هل أنت متأكد من حذف هذا العرض؟')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center px-4 py-3 bg-medical-red-50 text-medical-red-700 rounded-xl hover:bg-medical-red-100 transition-colors font-medium">
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                حذف العرض
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            {{-- Status Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات الحالة</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-500">الحالة الحالية</p>
                        <p class="font-bold text-medical-gray-900 mt-1">{{ $statusLabels[$quotation->status] ?? $quotation->status }}</p>
                    </div>
                    @if($quotation->status === 'accepted')
                        <div class="p-3 bg-medical-green-50 rounded-lg">
                            <p class="text-sm text-medical-green-700 font-medium">✅ تم قبول عرضك!</p>
                            <p class="text-xs text-medical-green-600 mt-1">سيتم التواصل معك قريباً</p>
                        </div>
                    @endif
                    @if($quotation->status === 'rejected')
                        <div class="p-3 bg-medical-red-50 rounded-lg">
                            <p class="text-sm text-medical-red-700 font-medium">❌ لم يتم قبول العرض</p>
                            @if($quotation->rejection_reason)
                                <p class="text-xs text-medical-red-600 mt-1">{{ $quotation->rejection_reason }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Attachments --}}
            @if($quotation->getMedia('quotation_documents')->count() > 0)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-4">المرفقات</h3>
                    <div class="space-y-2">
                        @foreach($quotation->getMedia('quotation_documents') as $media)
                            <a href="{{ $media->getUrl() }}" target="_blank"
                                class="flex items-center gap-2 p-2 bg-medical-gray-50 rounded-lg hover:bg-medical-gray-100 transition-colors">
                                <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <span class="text-sm text-medical-gray-700">{{ $media->name }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

