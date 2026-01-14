<x-dashboard.layout title="تفاصيل طلب عرض السعر" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('buyer.rfqs.index') }}" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                </a>
                <h1 class="text-2xl font-bold text-gray-900">{{ $rfq->title }}</h1>
            </div>
            <div class="flex items-center gap-4 text-sm text-gray-500">
                <span class="font-mono">{{ $rfq->reference_code }}</span>
                <span>•</span>
                <span>{{ $rfq->created_at->format('Y-m-d H:i') }}</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if(in_array($rfq->status, ['draft', 'open']) && $rfq->quotations->count() === 0)
                <a href="{{ route('buyer.rfqs.edit', $rfq) }}" 
                   class="inline-flex items-center px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    تعديل
                </a>
            @endif
            @php
                $statusColors = [
                    'draft' => 'bg-gray-100 text-gray-800',
                    'open' => 'bg-blue-100 text-blue-800',
                    'under_review' => 'bg-yellow-100 text-yellow-800',
                    'closed' => 'bg-gray-100 text-gray-800',
                    'awarded' => 'bg-green-100 text-green-800',
                    'cancelled' => 'bg-red-100 text-red-800',
                ];
                $statusLabels = [
                    'draft' => 'مسودة',
                    'open' => 'مفتوح',
                    'under_review' => 'قيد المراجعة',
                    'closed' => 'مغلق',
                    'awarded' => 'تم الترسية',
                    'cancelled' => 'ملغي',
                ];
            @endphp
            <span class="px-4 py-2 text-sm font-medium rounded-full {{ $statusColors[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                {{ $statusLabels[$rfq->status] ?? $rfq->status }}
            </span>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Left Column - RFQ Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Description --}}
            @if($rfq->description)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-3">الوصف</h2>
                <p class="text-gray-600 whitespace-pre-wrap">{{ $rfq->description }}</p>
            </div>
            @endif

            {{-- Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">بنود الطلب ({{ $rfq->items->count() }} بند)</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">#</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">البند</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">الكمية</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">المواصفات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($rfq->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $item->item_name }}</div>
                                    @if($item->product)
                                        <div class="text-xs text-gray-500">منتج: {{ $item->product->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $item->quantity }} {{ $item->unit }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $item->specifications ?? '-' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Quotations --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">عروض الأسعار المستلمة ({{ $rfq->quotations->count() }})</h2>
                        @if($rfq->quotations->count() > 1)
                            <a href="{{ route('buyer.quotations.compare', ['rfq_id' => $rfq->id]) }}" 
                               class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                مقارنة العروض ←
                            </a>
                        @endif
                    </div>
                </div>
                
                @if($rfq->quotations->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($rfq->quotations as $quotation)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold">
                                        {{ substr($quotation->supplier?->user?->name ?? 'م', 0, 1) }}
                                    </div>
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $quotation->supplier?->user?->name ?? 'مورد' }}</h3>
                                        <p class="text-sm text-gray-500">{{ $quotation->supplier?->company_name ?? '' }}</p>
                                        <p class="text-xs text-gray-400 mt-1">{{ $quotation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                                <div class="text-left">
                                    <div class="text-lg font-bold text-gray-900">
                                        {{ number_format($quotation->total_price, 2) }} د.ل
                                    </div>
                                    @php
                                        $qStatusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'accepted' => 'bg-green-100 text-green-800',
                                            'rejected' => 'bg-red-100 text-red-800',
                                        ];
                                        $qStatusLabels = [
                                            'pending' => 'قيد المراجعة',
                                            'accepted' => 'مقبول',
                                            'rejected' => 'مرفوض',
                                        ];
                                    @endphp
                                    <span class="mt-1 inline-block px-2 py-1 text-xs font-medium rounded-full {{ $qStatusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $qStatusLabels[$quotation->status] ?? $quotation->status }}
                                    </span>
                                </div>
                            </div>
                            
                            {{-- Quotation Details --}}
                            <div class="mt-4 grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500">مدة التوريد:</span>
                                    <span class="text-gray-900 font-medium">{{ $quotation->lead_time ?? '-' }} يوم</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">صلاحية العرض:</span>
                                    <span class="text-gray-900 font-medium">{{ $quotation->validity_days ?? '-' }} يوم</span>
                                </div>
                                <div>
                                    <span class="text-gray-500">الضمان:</span>
                                    <span class="text-gray-900 font-medium">{{ $quotation->warranty ?? '-' }}</span>
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="mt-4 flex items-center gap-3">
                                <a href="{{ route('buyer.quotations.show', $quotation) }}" 
                                   class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                    عرض التفاصيل
                                </a>
                                @if($quotation->status === 'pending' && in_array($rfq->status, ['open', 'under_review']))
                                    <form action="{{ route('buyer.quotations.accept', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟ سيتم رفض باقي العروض تلقائياً.')"
                                                class="text-sm text-green-600 hover:text-green-800 font-medium">
                                            قبول العرض
                                        </button>
                                    </form>
                                    <form action="{{ route('buyer.quotations.reject', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')"
                                                class="text-sm text-red-600 hover:text-red-800 font-medium">
                                            رفض
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عروض أسعار بعد</h3>
                        <p class="text-gray-500">انتظر الموردين لتقديم عروضهم</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column - Info Cards --}}
        <div class="space-y-6">
            {{-- Status & Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">معلومات الطلب</h3>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">الحالة</dt>
                        <dd>
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                            </span>
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">النوع</dt>
                        <dd class="text-gray-900">{{ $rfq->is_public ? 'عام' : 'خاص' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">الموعد النهائي</dt>
                        <dd class="text-gray-900">
                            @if($rfq->deadline)
                                {{ $rfq->deadline->format('Y-m-d') }}
                                @if($rfq->deadline->isPast())
                                    <span class="text-red-500">(انتهى)</span>
                                @endif
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">تاريخ الإنشاء</dt>
                        <dd class="text-gray-900">{{ $rfq->created_at->format('Y-m-d') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">عدد البنود</dt>
                        <dd class="text-gray-900">{{ $rfq->items->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">عروض الأسعار</dt>
                        <dd class="text-gray-900">{{ $rfq->quotations->count() }}</dd>
                    </div>
                </dl>

                {{-- Status Change Actions --}}
                @if(in_array($rfq->status, ['draft', 'open']))
                <div class="mt-6 pt-6 border-t border-gray-100 space-y-3">
                    @if($rfq->status === 'draft')
                        <form action="{{ route('buyer.rfqs.update-status', $rfq) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="open">
                            <button type="submit" class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm font-medium">
                                نشر الطلب
                            </button>
                        </form>
                    @endif
                    @if($rfq->status === 'open')
                        <form action="{{ route('buyer.rfqs.update-status', $rfq) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="closed">
                            <button type="submit" 
                                    onclick="return confirm('هل أنت متأكد من إغلاق هذا الطلب؟')"
                                    class="w-full px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-medium">
                                إغلاق الطلب
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('buyer.rfqs.update-status', $rfq) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="cancelled">
                        <button type="submit" 
                                onclick="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')"
                                class="w-full px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
                            إلغاء الطلب
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Assigned Suppliers --}}
            @if($rfq->assignedSuppliers->count() > 0)
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-semibold text-gray-900 mb-4">الموردين المعينين</h3>
                <ul class="space-y-3">
                    @foreach($rfq->assignedSuppliers as $supplier)
                    <li class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 text-sm font-medium">
                            {{ substr($supplier->user?->name ?? 'م', 0, 1) }}
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $supplier->user?->name ?? 'مورد' }}</div>
                            <div class="text-xs text-gray-500">{{ $supplier->company_name }}</div>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>
    </div>
</div>
</x-dashboard.layout>

