<x-dashboard.layout title="مقارنة عروض الأسعار" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('buyer.quotations.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">مقارنة عروض الأسعار</h1>
            </div>
            @if($rfq)
                <p class="text-sm text-gray-500">للطلب: {{ $rfq->reference_code }} - {{ $rfq->title }}</p>
            @endif
        </div>
    </div>

    @if($quotations->count() > 0)
        {{-- Comparison Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-4 text-right text-sm font-medium text-gray-500 sticky right-0 bg-gray-50 z-10 min-w-[200px]">
                                معايير المقارنة
                            </th>
                            @foreach($quotations as $quotation)
                            <th class="px-6 py-4 text-center min-w-[250px]">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold mb-2">
                                        {{ substr($quotation->supplier?->user?->name ?? 'م', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-gray-900">{{ $quotation->supplier?->user?->name ?? 'مورد' }}</span>
                                    <span class="text-xs text-gray-500">{{ $quotation->supplier?->company_name ?? '' }}</span>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        {{-- Price --}}
                        <tr class="bg-blue-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-blue-50 z-10">
                                💰 السعر الإجمالي
                            </td>
                            @php
                                $minPrice = $quotations->min('total_price');
                            @endphp
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center">
                                <span class="text-lg font-bold {{ $quotation->total_price == $minPrice ? 'text-green-600' : 'text-gray-900' }}">
                                    {{ number_format($quotation->total_price, 2) }} د.ل
                                </span>
                                @if($quotation->total_price == $minPrice)
                                    <span class="block text-xs text-green-600 font-medium">✓ أقل سعر</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Lead Time --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">
                                ⏱️ مدة التوريد
                            </td>
                            @php
                                $minLeadTime = $quotations->whereNotNull('lead_time')->min('lead_time');
                            @endphp
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center">
                                <span class="{{ $quotation->lead_time == $minLeadTime ? 'text-green-600 font-medium' : 'text-gray-900' }}">
                                    {{ $quotation->lead_time ?? '-' }} يوم
                                </span>
                                @if($quotation->lead_time == $minLeadTime && $minLeadTime)
                                    <span class="block text-xs text-green-600">✓ أسرع توريد</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>

                        {{-- Validity --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-50 z-10">
                                📅 صلاحية العرض
                            </td>
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ $quotation->validity_days ?? '-' }} يوم
                            </td>
                            @endforeach
                        </tr>

                        {{-- Warranty --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">
                                🛡️ الضمان
                            </td>
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center text-gray-900">
                                {{ $quotation->warranty ?? '-' }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Status --}}
                        <tr class="bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-50 z-10">
                                📊 الحالة
                            </td>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد المراجعة',
                                    'accepted' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                ];
                            @endphp
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                                </span>
                            </td>
                            @endforeach
                        </tr>

                        {{-- Submission Date --}}
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-white z-10">
                                📆 تاريخ التقديم
                            </td>
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center text-gray-500 text-sm">
                                {{ $quotation->created_at->format('Y-m-d H:i') }}
                            </td>
                            @endforeach
                        </tr>

                        {{-- Item Details Header --}}
                        @if($rfq && $rfq->items->count() > 0)
                        <tr class="bg-gray-100">
                            <td colspan="{{ $quotations->count() + 1 }}" class="px-6 py-3 text-sm font-bold text-gray-700">
                                📦 تفاصيل البنود
                            </td>
                        </tr>

                        {{-- Each RFQ Item --}}
                        @foreach($rfq->items as $rfqItem)
                        <tr class="{{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="px-6 py-4 text-sm sticky right-0 {{ $loop->even ? 'bg-gray-50' : 'bg-white' }} z-10">
                                <div class="font-medium text-gray-900">{{ $rfqItem->item_name }}</div>
                                <div class="text-xs text-gray-500">الكمية: {{ $rfqItem->quantity }} {{ $rfqItem->unit }}</div>
                            </td>
                            @foreach($quotations as $quotation)
                            @php
                                $quotationItem = $quotation->items->first(function($item) use ($rfqItem) {
                                    return $item->rfq_item_id == $rfqItem->id;
                                });
                            @endphp
                            <td class="px-6 py-4 text-center">
                                @if($quotationItem)
                                    <div class="font-medium text-gray-900">{{ number_format($quotationItem->unit_price, 2) }} د.ل</div>
                                    <div class="text-xs text-gray-500">
                                        الإجمالي: {{ number_format($quotationItem->unit_price * $rfqItem->quantity, 2) }} د.ل
                                    </div>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                        @endif

                        {{-- Actions Row --}}
                        <tr class="bg-gray-100">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 sticky right-0 bg-gray-100 z-10">
                                الإجراءات
                            </td>
                            @foreach($quotations as $quotation)
                            <td class="px-6 py-4 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <a href="{{ route('buyer.quotations.show', $quotation) }}" 
                                       class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                        عرض التفاصيل
                                    </a>
                                    @if($quotation->status === 'pending')
                                        <form action="{{ route('buyer.quotations.accept', $quotation) }}" method="POST" id="accept-form-compare-{{ $quotation->id }}">
                                            @csrf
                                            <button type="submit" 
                                                    onclick="event.preventDefault(); if(confirm('هل أنت متأكد من قبول هذا العرض؟ سيتم رفض باقي العروض تلقائياً.')) { document.getElementById('accept-form-compare-{{ $quotation->id }}').submit(); }"
                                                    class="px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition-colors font-medium">
                                                قبول العرض
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary --}}
        <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
            <h3 class="font-semibold text-blue-900 mb-4">📊 ملخص المقارنة</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-blue-700 font-medium">أقل سعر:</span>
                    <span class="text-blue-900">{{ number_format($quotations->min('total_price'), 2) }} د.ل</span>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">أعلى سعر:</span>
                    <span class="text-blue-900">{{ number_format($quotations->max('total_price'), 2) }} د.ل</span>
                </div>
                <div>
                    <span class="text-blue-700 font-medium">فارق السعر:</span>
                    <span class="text-blue-900">{{ number_format($quotations->max('total_price') - $quotations->min('total_price'), 2) }} د.ل</span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عروض للمقارنة</h3>
            <p class="text-gray-500">يجب أن يكون لديك عرضين على الأقل لإجراء المقارنة</p>
        </div>
    @endif
</div>
</x-dashboard.layout>
