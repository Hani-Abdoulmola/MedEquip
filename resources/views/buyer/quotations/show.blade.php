<x-dashboard.layout title="تفاصيل عرض السعر" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('buyer.quotations.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">عرض سعر من {{ $quotation->supplier?->user?->name ?? 'مورد' }}</h1>
            </div>
            <p class="text-sm text-gray-500">للطلب: {{ $quotation->rfq?->reference_code }} - {{ $quotation->rfq?->title }}</p>
        </div>
        <div class="flex items-center gap-3">
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
            <span class="px-4 py-2 text-sm font-medium rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$quotation->status] ?? $quotation->status }}
            </span>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - Quotation Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">معلومات المورد</h2>
                <div class="flex items-start gap-4">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-2xl font-bold">
                        {{ substr($quotation->supplier?->user?->name ?? 'م', 0, 1) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">{{ $quotation->supplier?->user?->name ?? 'مورد' }}</h3>
                        <p class="text-gray-500">{{ $quotation->supplier?->company_name ?? '' }}</p>
                        @if($quotation->supplier?->contact_email)
                            <p class="text-sm text-gray-500 mt-2">
                                <span class="font-medium">البريد:</span> {{ $quotation->supplier->contact_email }}
                            </p>
                        @endif
                        @if($quotation->supplier?->contact_phone)
                            <p class="text-sm text-gray-500">
                                <span class="font-medium">الهاتف:</span> {{ $quotation->supplier->contact_phone }}
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Quotation Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">تفاصيل الأسعار</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البند</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">سعر الوحدة</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($quotation->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->rfqItem?->item_name ?? 'بند' }}</div>
                                    @if($item->notes)
                                        <div class="text-xs text-gray-500 mt-1">{{ $item->notes }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity ?? $item->rfqItem?->quantity }} {{ $item->rfqItem?->unit ?? '' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($item->unit_price, 2) }} د.ل
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($item->total_price ?? ($item->unit_price * ($item->quantity ?? $item->rfqItem?->quantity ?? 1)), 2) }} د.ل
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-left text-sm font-bold text-gray-900">
                                    الإجمالي الكلي
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-lg font-bold text-medical-blue-600">
                                    {{ number_format($quotation->total_price, 2) }} د.ل
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Notes --}}
            @if($quotation->notes)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">ملاحظات المورد</h2>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $quotation->notes }}</p>
            </div>
            @endif

            {{-- Terms --}}
            @if($quotation->terms)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">الشروط والأحكام</h2>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $quotation->terms }}</p>
            </div>
            @endif
        </div>

        {{-- Right Column - Summary & Actions --}}
        <div class="space-y-6">
            {{-- Summary Card --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">ملخص العرض</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">السعر الإجمالي</dt>
                        <dd class="text-lg font-bold text-gray-900">{{ number_format($quotation->total_price, 2) }} د.ل</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">مدة التوريد</dt>
                        <dd class="text-gray-900">{{ $quotation->lead_time ?? '-' }} يوم</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">صلاحية العرض</dt>
                        <dd class="text-gray-900">{{ $quotation->validity_days ?? '-' }} يوم</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">الضمان</dt>
                        <dd class="text-gray-900">{{ $quotation->warranty ?? '-' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">تاريخ التقديم</dt>
                        <dd class="text-gray-900">{{ $quotation->created_at->format('Y-m-d H:i') }}</dd>
                    </div>
                </dl>

                {{-- Actions --}}
                @if($quotation->status === 'pending')
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                    <form action="{{ route('buyer.quotations.accept', $quotation) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟ سيتم رفض باقي العروض تلقائياً.')"
                                class="w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                            قبول العرض
                        </button>
                    </form>
                    <form action="{{ route('buyer.quotations.reject', $quotation) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')"
                                class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                            رفض العرض
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- RFQ Info --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">طلب عرض السعر</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-gray-500">الرمز المرجعي</dt>
                        <dd class="text-gray-900 font-mono">{{ $quotation->rfq?->reference_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">العنوان</dt>
                        <dd class="text-gray-900">{{ $quotation->rfq?->title }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">عدد البنود</dt>
                        <dd class="text-gray-900">{{ $quotation->rfq?->items->count() ?? 0 }} بند</dd>
                    </div>
                </dl>
                <div class="mt-4">
                    <a href="{{ route('buyer.rfqs.show', $quotation->rfq) }}" 
                       class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                        عرض تفاصيل الطلب ←
                    </a>
                </div>
            </div>

            {{-- Compare with others --}}
            @if($quotation->rfq && $quotation->rfq->quotations->count() > 1)
            <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                <h3 class="font-semibold text-blue-900 mb-2">قارن مع العروض الأخرى</h3>
                <p class="text-sm text-blue-700 mb-4">
                    يوجد {{ $quotation->rfq->quotations->count() - 1 }} عروض أخرى لهذا الطلب
                </p>
                <a href="{{ route('buyer.quotations.compare', ['rfq_id' => $quotation->rfq_id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    مقارنة العروض
                </a>
            </div>
            @endif
        </div>
    </div>
</div>
</x-dashboard.layout>

