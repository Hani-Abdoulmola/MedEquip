{{-- Supplier RFQs - Show --}}
<x-dashboard.layout title="تفاصيل الطلب" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل طلب عرض السعر</h1>
                <p class="mt-2 text-medical-gray-600">{{ $rfq->reference_code }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if ($rfq->status === 'open' && !$myQuotation)
                    <a href="{{ route('supplier.rfqs.quote.create', $rfq) }}"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-medium shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <span>تقديم عرض سعر</span>
                    </a>
                @elseif ($myQuotation)
                    <a href="{{ route('supplier.quotations.edit', $myQuotation) }}"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>تعديل عرضي</span>
                    </a>
                @endif
                <a href="{{ route('supplier.rfqs.index') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
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

    @if (session('error') || session('info') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                {{ session('info') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- RFQ Details --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Basic Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات الطلب
                </h3>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-medical-gray-600">عنوان الطلب</p>
                        <p class="text-lg font-semibold text-medical-gray-900 mt-1">{{ $rfq->title }}</p>
                    </div>

                    @if ($rfq->description)
                        <div>
                            <p class="text-sm text-medical-gray-600">الوصف</p>
                            <p class="text-medical-gray-900 mt-1 whitespace-pre-wrap">{{ $rfq->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-medical-gray-600">الحالة</p>
                            <div class="mt-1">
                                @if ($rfq->status === 'open')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-green-100 text-medical-green-700">
                                        مفتوح
                                    </span>
                                @elseif ($rfq->status === 'closed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-gray-100 text-medical-gray-700">
                                        مغلق
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-red-100 text-medical-red-700">
                                        ملغى
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div>
                            <p class="text-sm text-medical-gray-600">الموعد النهائي</p>
                            <p class="text-medical-gray-900 mt-1">
                                @if ($rfq->deadline)
                                    {{ $rfq->deadline->format('Y-m-d H:i') }}
                                    <span class="{{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-medical-gray-500' }} text-sm">
                                        ({{ $rfq->deadline->diffForHumans() }})
                                    </span>
                                @else
                                    <span class="text-medical-gray-400">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RFQ Items --}}
            @if ($rfq->items->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        المنتجات المطلوبة
                    </h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الوحدة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">ملاحظات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-medical-gray-200">
                                @foreach ($rfq->items as $item)
                                    <tr>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->product_name ?? 'منتج' }}</td>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->quantity ?? 1 }}</td>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->unit ?? 'قطعة' }}</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->notes ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- My Quotation --}}
            @if ($myQuotation)
                <div class="bg-white rounded-2xl shadow-medical p-6 border-2 border-medical-blue-200">
                    <div class="flex items-center justify-between mb-4 pb-3 border-b border-medical-gray-200">
                        <h3 class="text-lg font-semibold text-medical-gray-900">
                            عرضك المقدم
                        </h3>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if ($myQuotation->status === 'pending') bg-medical-yellow-100 text-medical-yellow-700
                            @elseif ($myQuotation->status === 'accepted') bg-medical-green-100 text-medical-green-700
                            @else bg-medical-red-100 text-medical-red-700 @endif">
                            {{ $myQuotation->status === 'pending' ? 'قيد المراجعة' : ($myQuotation->status === 'accepted' ? 'مقبول' : 'مرفوض') }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="p-4 bg-medical-blue-50 rounded-xl">
                            <p class="text-sm text-medical-gray-600">السعر الإجمالي</p>
                            <p class="text-2xl font-bold text-medical-blue-600 mt-1">
                                {{ number_format($myQuotation->total_price, 2) }} د.ل
                            </p>
                        </div>

                        <div class="p-4 bg-medical-gray-50 rounded-xl">
                            <p class="text-sm text-medical-gray-600">صالح حتى</p>
                            <p class="text-lg font-semibold text-medical-gray-900 mt-1">
                                {{ $myQuotation->valid_until?->format('Y-m-d') ?? 'غير محدد' }}
                            </p>
                        </div>
                    </div>

                    @if ($myQuotation->terms)
                        <div class="mt-4">
                            <p class="text-sm text-medical-gray-600">الشروط والأحكام</p>
                            <p class="text-medical-gray-900 mt-1 whitespace-pre-wrap">{{ $myQuotation->terms }}</p>
                        </div>
                    @endif

                    <div class="mt-4 pt-4 border-t border-medical-gray-200">
                        <a href="{{ route('supplier.quotations.edit', $myQuotation) }}"
                            class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors duration-150 text-sm font-medium">
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            تعديل العرض
                        </a>
                    </div>
                </div>
            @endif

            {{-- RFQ Documents --}}
            @if ($rfq->hasMedia('rfq_documents'))
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        مستندات الطلب
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($rfq->getMedia('rfq_documents') as $document)
                            <a href="{{ $document->getUrl() }}" target="_blank"
                                class="flex items-center space-x-3 space-x-reverse p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition-colors duration-200">
                                <svg class="w-8 h-8 text-medical-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                <div class="flex-1">
                                    <p class="font-medium text-medical-gray-900">{{ $document->file_name }}</p>
                                    <p class="text-sm text-medical-gray-600">{{ $document->human_readable_size }}</p>
                                </div>
                                <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Buyer Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات المشتري
                </h3>

                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-600">اسم المنشأة</p>
                        <p class="font-semibold text-medical-gray-900 mt-1">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                    </div>

                    @if ($rfq->buyer?->city)
                        <div>
                            <p class="text-sm text-medical-gray-600">المدينة</p>
                            <p class="text-medical-gray-900 mt-1">{{ $rfq->buyer->city }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    ملخص
                </h3>

                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">تاريخ الإنشاء</span>
                        <span class="font-medium text-medical-gray-900">{{ $rfq->created_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">عدد المنتجات</span>
                        <span class="font-medium text-medical-gray-900">{{ $rfq->items->count() }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-medical-gray-600">حالة عرضك</span>
                        @if ($myQuotation)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-medical-blue-100 text-medical-blue-700">
                                تم التقديم
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                لم يُقدم
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Action Card --}}
            @if ($rfq->status === 'open' && !$myQuotation)
                <div class="bg-gradient-to-br from-medical-blue-600 to-medical-blue-700 rounded-2xl shadow-medical p-6 text-white">
                    <h3 class="text-lg font-semibold mb-2">قدم عرضك الآن</h3>
                    <p class="text-medical-blue-100 text-sm mb-4">استغل الفرصة وقدم عرض سعر تنافسي لهذا الطلب.</p>
                    <a href="{{ route('supplier.rfqs.quote.create', $rfq) }}"
                        class="inline-flex items-center px-4 py-2 bg-white text-medical-blue-600 rounded-lg hover:bg-medical-blue-50 transition-colors duration-150 text-sm font-medium">
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        تقديم عرض سعر
                    </a>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

