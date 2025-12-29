{{-- Admin Quotations - Compare --}}
<x-dashboard.layout title="مقارنة عروض الأسعار" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.rfqs.show', $rfq) }}"
                class="p-2 bg-medical-gray-100 hover:bg-medical-gray-200 rounded-xl transition-colors">
                <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">مقارنة عروض الأسعار</h1>
                <p class="mt-1 text-medical-gray-600">{{ $rfq->title }} - {{ $rfq->reference_code }}</p>
            </div>
        </div>
    </div>

    {{-- RFQ Summary & Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-6">
                <div>
                    <p class="text-sm text-medical-gray-500">المشتري</p>
                    <p class="font-bold text-medical-gray-900">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-500">عدد العناصر</p>
                    <p class="font-bold text-medical-gray-900">{{ $rfq->items->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-500">عدد العروض</p>
                    <p class="font-bold text-medical-blue-600">{{ $rfq->quotations->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-500">الموعد النهائي</p>
                    <p class="font-bold {{ $rfq->deadline && $rfq->deadline->isPast() ? 'text-medical-red-600' : 'text-medical-gray-900' }}">
                        {{ $rfq->deadline ? $rfq->deadline->format('Y-m-d') : 'غير محدد' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        @if(isset($stats) && $rfq->quotations->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-medical-gray-200">
                <div class="p-3 bg-medical-green-50 rounded-lg">
                    <p class="text-xs text-medical-green-600 mb-1">أقل سعر</p>
                    <p class="text-lg font-bold text-medical-green-700">{{ number_format($stats['min_price'], 2) }} د.ل</p>
                </div>
                <div class="p-3 bg-medical-red-50 rounded-lg">
                    <p class="text-xs text-medical-red-600 mb-1">أعلى سعر</p>
                    <p class="text-lg font-bold text-medical-red-700">{{ number_format($stats['max_price'], 2) }} د.ل</p>
                </div>
                <div class="p-3 bg-medical-blue-50 rounded-lg">
                    <p class="text-xs text-medical-blue-600 mb-1">متوسط السعر</p>
                    <p class="text-lg font-bold text-medical-blue-700">{{ number_format($stats['avg_price'], 2) }} د.ل</p>
                </div>
                <div class="p-3 bg-medical-purple-50 rounded-lg">
                    <p class="text-xs text-medical-purple-600 mb-1">نطاق السعر</p>
                    <p class="text-lg font-bold text-medical-purple-700">{{ number_format($stats['price_range'], 2) }} د.ل</p>
                </div>
            </div>
        @endif

        {{-- Filters & Sort --}}
        <form method="GET" action="{{ route('admin.quotations.compare') }}" class="mt-4 pt-4 border-t border-medical-gray-200">
            <input type="hidden" name="rfq_id" value="{{ $rfq->id }}">
            <div class="flex flex-wrap items-center gap-4">
                <div>
                    <label for="sort_by" class="block text-xs text-medical-gray-600 mb-1">ترتيب حسب</label>
                    <select name="sort_by" id="sort_by" onchange="this.form.submit()"
                        class="px-3 py-2 border border-medical-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500">
                        <option value="">افتراضي (السعر)</option>
                        <option value="price_asc" {{ request('sort_by') == 'price_asc' ? 'selected' : '' }}>السعر: من الأقل للأعلى</option>
                        <option value="price_desc" {{ request('sort_by') == 'price_desc' ? 'selected' : '' }}>السعر: من الأعلى للأقل</option>
                        <option value="date_asc" {{ request('sort_by') == 'date_asc' ? 'selected' : '' }}>التاريخ: من الأقدم للأحدث</option>
                        <option value="date_desc" {{ request('sort_by') == 'date_desc' ? 'selected' : '' }}>التاريخ: من الأحدث للأقدم</option>
                        <option value="supplier" {{ request('sort_by') == 'supplier' ? 'selected' : '' }}>اسم المورد</option>
                    </select>
                </div>
                <div>
                    <label for="filter_status" class="block text-xs text-medical-gray-600 mb-1">فلترة حسب الحالة</label>
                    <select name="filter_status" id="filter_status" onchange="this.form.submit()"
                        class="px-3 py-2 border border-medical-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('filter_status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                        <option value="accepted" {{ request('filter_status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                        <option value="rejected" {{ request('filter_status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                    </select>
                </div>
                @if(request()->hasAny(['sort_by', 'filter_status']))
                    <a href="{{ route('admin.quotations.compare', ['rfq_id' => $rfq->id]) }}"
                        class="px-4 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-lg hover:bg-medical-gray-200 text-sm font-medium">
                        إعادة تعيين
                    </a>
                @endif
            </div>
        </form>
    </div>

    @if($rfq->quotations->count() > 0)
        {{-- Comparison Table --}}
        <div class="bg-white rounded-2xl shadow-medical overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider sticky right-0 bg-medical-gray-50">
                                المعيار
                            </th>
                            @foreach($rfq->quotations as $quotation)
                                <th class="px-6 py-4 text-center text-xs font-semibold text-medical-gray-600 uppercase tracking-wider min-w-[200px]">
                                    <div class="space-y-1">
                                        <p class="text-medical-blue-600 font-mono">{{ $quotation->reference_code }}</p>
                                        <p class="font-bold text-medical-gray-900">{{ $quotation->supplier->company_name ?? 'غير محدد' }}</p>
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
                                        <span class="inline-block px-2 py-1 rounded-full text-xs {{ $statusClasses[$quotation->status] ?? 'bg-medical-gray-100' }}">
                                            {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                                        </span>
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-medical-gray-200">
                        {{-- Total Price Row --}}
                        <tr class="bg-medical-green-50">
                            <td class="px-6 py-4 font-bold text-medical-gray-900 sticky right-0 bg-medical-green-50">
                                💰 إجمالي السعر
                            </td>
                            @php
                                $prices = $rfq->quotations->pluck('total_price');
                                $minPrice = $prices->min();
                                $maxPrice = $prices->max();
                            @endphp
                            @foreach($rfq->quotations as $quotation)
                                <td class="px-6 py-4 text-center">
                                    <span class="text-xl font-bold {{ $quotation->total_price == $minPrice ? 'text-medical-green-600' : ($quotation->total_price == $maxPrice ? 'text-medical-red-600' : 'text-medical-gray-900') }}">
                                        {{ number_format($quotation->total_price, 2) }}
                                    </span>
                                    <span class="text-medical-gray-500 text-sm">د.ل</span>
                                    @if($quotation->total_price == $minPrice)
                                        <span class="block text-xs text-medical-green-600 mt-1">✓ أقل سعر</span>
                                    @elseif($quotation->total_price == $maxPrice)
                                        <span class="block text-xs text-medical-red-600 mt-1">أعلى سعر</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Lead Time Row --}}
                        <tr>
                            <td class="px-6 py-4 font-bold text-medical-gray-900 sticky right-0 bg-white">
                                ⏱️ مدة التوصيل
                            </td>
                            @php
                                $leadTimes = $rfq->quotations->pluck('lead_time')->filter();
                                $minLeadTime = $leadTimes->min();
                            @endphp
                            @foreach($rfq->quotations as $quotation)
                                <td class="px-6 py-4 text-center">
                                    @if($quotation->lead_time)
                                        <span class="text-lg font-semibold {{ $quotation->lead_time == $minLeadTime ? 'text-medical-green-600' : 'text-medical-gray-900' }}">
                                            {{ $quotation->lead_time }}
                                        </span>
                                        <span class="text-medical-gray-500 text-sm">يوم</span>
                                        @if($quotation->lead_time == $minLeadTime)
                                            <span class="block text-xs text-medical-green-600 mt-1">✓ أسرع توصيل</span>
                                        @endif
                                    @else
                                        <span class="text-medical-gray-400">غير محدد</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Warranty Row --}}
                        <tr class="bg-medical-gray-50">
                            <td class="px-6 py-4 font-bold text-medical-gray-900 sticky right-0 bg-medical-gray-50">
                                🛡️ فترة الضمان
                            </td>
                            @php
                                $warranties = $rfq->quotations->pluck('warranty_period')->filter();
                                $maxWarranty = $warranties->max();
                            @endphp
                            @foreach($rfq->quotations as $quotation)
                                <td class="px-6 py-4 text-center">
                                    @if($quotation->warranty_period)
                                        <span class="text-lg font-semibold {{ $quotation->warranty_period == $maxWarranty ? 'text-medical-green-600' : 'text-medical-gray-900' }}">
                                            {{ $quotation->warranty_period }}
                                        </span>
                                        <span class="text-medical-gray-500 text-sm">شهر</span>
                                        @if($quotation->warranty_period == $maxWarranty)
                                            <span class="block text-xs text-medical-green-600 mt-1">✓ أطول ضمان</span>
                                        @endif
                                    @else
                                        <span class="text-medical-gray-400">غير محدد</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Date Row --}}
                        <tr>
                            <td class="px-6 py-4 font-bold text-medical-gray-900 sticky right-0 bg-white">
                                📅 تاريخ التقديم
                            </td>
                            @foreach($rfq->quotations as $quotation)
                                <td class="px-6 py-4 text-center">
                                    <span class="text-medical-gray-900">{{ $quotation->created_at->format('Y-m-d') }}</span>
                                    <span class="block text-xs text-medical-gray-500">{{ $quotation->created_at->diffForHumans() }}</span>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Item Details Section --}}
                        @if($rfq->items->count() > 0)
                            <tr class="bg-medical-blue-50">
                                <td colspan="{{ $rfq->quotations->count() + 1 }}" class="px-6 py-3 font-bold text-medical-blue-700">
                                    📦 تفاصيل تسعير العناصر
                                </td>
                            </tr>

                            @foreach($rfq->items as $rfqItem)
                                <tr>
                                    <td class="px-6 py-4 font-medium text-medical-gray-900 sticky right-0 bg-white">
                                        {{ $rfqItem->product_name }}
                                        <span class="text-sm text-medical-gray-500 block">{{ $rfqItem->quantity }} {{ $rfqItem->unit ?? 'وحدة' }}</span>
                                    </td>
                                    @foreach($rfq->quotations as $quotation)
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $qItem = $quotation->items->firstWhere('rfq_item_id', $rfqItem->id);
                                            @endphp
                                            @if($qItem)
                                                <span class="font-semibold text-medical-gray-900">{{ number_format($qItem->unit_price, 2) }} د.ل</span>
                                                <span class="block text-xs text-medical-gray-500">× {{ $qItem->quantity }} = {{ number_format($qItem->unit_price * $qItem->quantity, 2) }} د.ل</span>
                                            @else
                                                <span class="text-medical-gray-400">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endif

                        {{-- Actions Row --}}
                        <tr class="bg-medical-gray-50">
                            <td class="px-6 py-4 font-bold text-medical-gray-900 sticky right-0 bg-medical-gray-50">
                                الإجراءات
                            </td>
                            @foreach($rfq->quotations as $quotation)
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ route('admin.quotations.show', $quotation) }}"
                                            class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                                            عرض التفاصيل
                                        </a>
                                        @if($quotation->status === 'pending')
                                            <form action="{{ route('admin.quotations.accept', $quotation) }}" method="POST" class="inline">
                                                @csrf
                                                <input type="hidden" name="award_rfq" value="1">
                                                <button type="submit"
                                                    class="w-full px-4 py-2 bg-medical-green-600 text-white rounded-lg hover:bg-medical-green-700 transition-colors text-sm font-medium"
                                                    onclick="return confirm('هل أنت متأكد من قبول هذا العرض وترسية الطلب؟')">
                                                    قبول وترسية
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

        {{-- Legend --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h3 class="font-bold text-medical-gray-900 mb-4">دليل الألوان</h3>
            <div class="flex flex-wrap gap-6">
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-medical-green-500 rounded"></span>
                    <span class="text-sm text-medical-gray-600">الأفضل في الفئة</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-medical-red-500 rounded"></span>
                    <span class="text-sm text-medical-gray-600">الأقل تنافسية</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 bg-medical-gray-400 rounded"></span>
                    <span class="text-sm text-medical-gray-600">غير محدد</span>
                </div>
            </div>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-medical p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد عروض للمقارنة</h3>
            <p class="mt-2 text-medical-gray-600">لم يتم تقديم أي عروض أسعار لهذا الطلب بعد.</p>
        </div>
    @endif

</x-dashboard.layout>

