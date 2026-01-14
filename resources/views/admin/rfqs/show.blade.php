{{-- Admin RFQ - Show Details --}}
<x-dashboard.layout title="تفاصيل طلب عرض السعر" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.rfqs.index') }}"
                    class="p-2 bg-medical-gray-100 hover:bg-medical-gray-200 rounded-xl transition-colors">
                    <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">{{ $rfq->title }}</h1>
                    <p class="mt-1 text-medical-gray-600 font-mono">{{ $rfq->reference_code }}</p>
                </div>
            </div>

            {{-- Status Badge --}}
            @php
                $statusClasses = [
                    'open' => 'bg-medical-green-100 text-medical-green-700',
                    'closed' => 'bg-medical-gray-100 text-medical-gray-700',
                    'awarded' => 'bg-medical-purple-100 text-medical-purple-700',
                    'cancelled' => 'bg-medical-red-100 text-medical-red-700',
                ];
                $statusLabels = [
                    'open' => 'مفتوح',
                    'closed' => 'مغلق',
                    'awarded' => 'تم الترسية',
                    'cancelled' => 'ملغي',
                ];
            @endphp
            <span class="px-6 py-2 rounded-full text-lg font-semibold {{ $statusClasses[$rfq->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                {{ $statusLabels[$rfq->status] ?? $rfq->status }}
            </span>
        </div>
    </div>

    {{-- Flash Messages --}}
    {{-- Note: Success messages are displayed in dashboard layout component --}}
    {{-- Only show error messages here to avoid duplicates --}}
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
            {{-- RFQ Details --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    تفاصيل الطلب
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">المشتري</p>
                        <p class="font-semibold text-medical-gray-900">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                        @if($rfq->buyer && $rfq->buyer->user)
                            <p class="text-sm text-medical-gray-600">{{ $rfq->buyer->user->email }}</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">تاريخ الإنشاء</p>
                        <p class="font-semibold text-medical-gray-900">{{ $rfq->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-sm text-medical-gray-600">{{ $rfq->created_at->diffForHumans() }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">الموعد النهائي</p>
                        @if($rfq->deadline)
                            <p class="font-semibold {{ $rfq->deadline->isPast() ? 'text-medical-red-600' : 'text-medical-gray-900' }}">
                                {{ $rfq->deadline->format('Y-m-d H:i') }}
                            </p>
                            <p class="text-sm {{ $rfq->deadline->isPast() ? 'text-medical-red-500' : 'text-medical-gray-600' }}">
                                {{ $rfq->deadline->diffForHumans() }}
                            </p>
                        @else
                            <p class="text-medical-gray-400">غير محدد</p>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-medical-gray-500 mb-1">الرؤية</p>
                        <div class="flex items-center gap-2">
                            @if($rfq->is_public)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-blue-100 text-medical-blue-700">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                    </svg>
                                    عام - لجميع الموردين
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                    خاص - للموردين المعينين
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($rfq->description)
                    <div class="mt-6 pt-6 border-t border-medical-gray-200">
                        <p class="text-sm text-medical-gray-500 mb-2">الوصف</p>
                        <p class="text-medical-gray-700 leading-relaxed">{{ $rfq->description }}</p>
                    </div>
                @endif

                @if($rfq->notes)
                    <div class="mt-4 p-4 bg-medical-yellow-50 rounded-xl">
                        <p class="text-sm text-medical-yellow-700 font-medium mb-1">ملاحظات</p>
                        <p class="text-medical-yellow-800">{{ $rfq->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- RFQ Items --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                        العناصر المطلوبة ({{ $rfq->items->count() }})
                    </h2>
                    @if($rfq->status === 'draft' || $rfq->status === 'open')
                        <a href="{{ route('admin.rfqs.items.create', $rfq) }}"
                            class="px-4 py-2 bg-medical-green-600 text-white rounded-lg hover:bg-medical-green-700 transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            إضافة عنصر
                        </a>
                    @endif
                </div>

                @if($rfq->items->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-medical-gray-200">
                            <thead class="bg-medical-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">#</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">اسم المنتج</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الكمية</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الوحدة</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">المواصفات</th>
                                    @if($rfq->status === 'draft' || $rfq->status === 'open')
                                        <th class="px-4 py-3 text-right text-xs font-semibold text-medical-gray-600 uppercase">الإجراءات</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-medical-gray-200">
                                @foreach($rfq->items as $index => $item)
                                    <tr class="hover:bg-medical-gray-50">
                                        <td class="px-4 py-3 text-medical-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3 font-medium text-medical-gray-900">
                                            {{ $item->item_name }}
                                            @if($item->product)
                                                <span class="text-xs text-medical-blue-600 block">من الكتالوج: {{ $item->product->name }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-medical-gray-900">{{ $item->quantity }}</td>
                                        <td class="px-4 py-3 text-medical-gray-600">{{ $item->unit ?? 'وحدة' }}</td>
                                        <td class="px-4 py-3 text-medical-gray-600 text-sm">
                                            {{ Str::limit($item->specifications ?? '-', 50) }}
                                        </td>
                                        @if($rfq->status === 'draft' || $rfq->status === 'open')
                                            <td class="px-4 py-3">
                                                <div class="flex items-center gap-2">
                                                    <a href="{{ route('admin.rfqs.items.edit', [$rfq, $item]) }}"
                                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors"
                                                        title="تعديل">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </a>
                                                    <form action="{{ route('admin.rfqs.items.destroy', [$rfq, $item]) }}" method="POST"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟')"
                                                        class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors"
                                                            title="حذف">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-center text-medical-gray-500 py-8">لا توجد عناصر مضافة</p>
                @endif
            </div>

            {{-- Quotations Received --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    عروض الأسعار المستلمة ({{ $rfq->quotations->count() }})
                </h2>

                @if($rfq->quotations->count() > 0)
                    <div class="space-y-4">
                        @foreach($rfq->quotations as $quotation)
                            <div class="border border-medical-gray-200 rounded-xl p-5 hover:shadow-md transition-shadow">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <p class="font-mono text-sm text-medical-blue-600">{{ $quotation->reference_code }}</p>
                                        <p class="font-semibold text-medical-gray-900">{{ $quotation->supplier->company_name ?? 'غير محدد' }}</p>
                                        <p class="text-sm text-medical-gray-500">{{ $quotation->created_at->format('Y-m-d H:i') }}</p>
                                    </div>
                                    <div class="text-left">
                                        @php
                                            $qStatusClasses = [
                                                'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                                'accepted' => 'bg-medical-green-100 text-medical-green-700',
                                                'rejected' => 'bg-medical-red-100 text-medical-red-700',
                                            ];
                                            $qStatusLabels = [
                                                'pending' => 'قيد المراجعة',
                                                'accepted' => 'مقبول',
                                                'rejected' => 'مرفوض',
                                            ];
                                        @endphp
                                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $qStatusClasses[$quotation->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                            {{ $qStatusLabels[$quotation->status] ?? $quotation->status }}
                                        </span>
                                        <p class="text-2xl font-bold text-medical-green-600 mt-2">
                                            {{ number_format($quotation->total_price, 2) }} د.ل
                                        </p>
                                    </div>
                                </div>

                                {{-- Quotation Items --}}
                                @if($quotation->items && $quotation->items->count() > 0)
                                    <div class="border-t border-medical-gray-100 pt-4 mt-4">
                                        <p class="text-sm font-medium text-medical-gray-700 mb-2">تفاصيل التسعير:</p>
                                        <div class="space-y-2">
                                            @foreach($quotation->items as $qItem)
                                                <div class="flex items-center justify-between text-sm bg-medical-gray-50 px-3 py-2 rounded-lg">
                                                    <span class="text-medical-gray-700">{{ $qItem->rfqItem->product_name ?? 'منتج' }} × {{ $qItem->quantity }}</span>
                                                    <span class="font-medium text-medical-gray-900">{{ number_format($qItem->unit_price * $qItem->quantity, 2) }} د.ل</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center justify-between mt-4 pt-4 border-t border-medical-gray-100">
                                    <div class="flex items-center gap-4 text-sm text-medical-gray-600">
                                        @if($quotation->lead_time)
                                            <span>⏱️ {{ $quotation->lead_time }} يوم توصيل</span>
                                        @endif
                                        @if($quotation->warranty_period)
                                            <span>🛡️ {{ $quotation->warranty_period }} شهر ضمان</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('admin.quotations.show', $quotation) }}"
                                        class="text-medical-blue-600 hover:text-medical-blue-700 font-medium text-sm">
                                        عرض التفاصيل ←
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-medical-gray-50 rounded-xl">
                        <svg class="mx-auto h-12 w-12 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="mt-4 text-medical-gray-600">لم يتم استلام أي عروض أسعار بعد</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Actions --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">الإجراءات</h3>

                {{-- Update Status --}}
                <form action="{{ route('admin.rfqs.update-status', $rfq) }}" method="POST" class="mb-6">
                    @csrf
                    @method('PATCH')
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">تغيير الحالة</label>
                    <div class="flex gap-2">
                        <select name="status" class="flex-1 px-4 py-2 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500">
                            <option value="open" {{ $rfq->status == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="closed" {{ $rfq->status == 'closed' ? 'selected' : '' }}>مغلق</option>
                            <option value="awarded" {{ $rfq->status == 'awarded' ? 'selected' : '' }}>تم الترسية</option>
                            <option value="cancelled" {{ $rfq->status == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors">
                            حفظ
                        </button>
                    </div>
                </form>

                {{-- Toggle Visibility --}}
                <form action="{{ route('admin.rfqs.toggle-visibility', $rfq) }}" method="POST" class="mb-6">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full px-4 py-3 {{ $rfq->is_public ? 'bg-medical-yellow-50 text-medical-yellow-700 hover:bg-medical-yellow-100' : 'bg-medical-blue-50 text-medical-blue-700 hover:bg-medical-blue-100' }} rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                        @if($rfq->is_public)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            جعل الطلب خاصاً
                        @else
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                            </svg>
                            جعل الطلب عاماً
                        @endif
                    </button>
                </form>
            </div>

            {{-- Assigned Suppliers --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    الموردون المعينون ({{ $rfq->assignedSuppliers->count() }})
                </h3>

                @if($rfq->assignedSuppliers->count() > 0)
                    <div class="space-y-2 mb-4">
                        @foreach($rfq->assignedSuppliers as $supplier)
                            <div class="flex items-center justify-between p-3 bg-medical-gray-50 rounded-lg">
                                <div>
                                    <p class="font-medium text-medical-gray-900">{{ $supplier->company_name }}</p>
                                    <p class="text-xs text-medical-gray-500">{{ $supplier->user->email ?? '' }}</p>
                                </div>
                                @php
                                    $hasQuotation = $rfq->quotations->where('supplier_id', $supplier->id)->isNotEmpty();
                                @endphp
                                @if($hasQuotation)
                                    <span class="px-2 py-1 bg-medical-green-100 text-medical-green-700 text-xs rounded-full">قدم عرض</span>
                                @else
                                    <span class="px-2 py-1 bg-medical-yellow-100 text-medical-yellow-700 text-xs rounded-full">في الانتظار</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-medical-gray-500 text-sm mb-4">لم يتم تعيين موردين بعد</p>
                @endif

                {{-- Assign Suppliers Form --}}
                <form action="{{ route('admin.rfqs.assign-suppliers', $rfq) }}" method="POST" x-data="{ open: false }">
                    @csrf
                    @method('POST')
                    
                    <button type="button" @click="open = !open"
                        class="w-full px-4 py-3 bg-medical-purple-50 text-medical-purple-700 rounded-xl font-medium hover:bg-medical-purple-100 transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        تعيين موردين
                    </button>

                    <div x-show="open" x-transition class="mt-4 p-4 bg-medical-gray-50 rounded-xl">
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">اختر الموردين</label>
                        <div class="max-h-48 overflow-y-auto space-y-2">
                            @foreach($allSuppliers as $supplier)
                                <label class="flex items-center p-2 hover:bg-white rounded-lg cursor-pointer">
                                    <input type="checkbox" name="supplier_ids[]" value="{{ $supplier->id }}"
                                        {{ $rfq->assignedSuppliers->contains($supplier->id) ? 'checked' : '' }}
                                        class="w-4 h-4 text-medical-purple-600 border-medical-gray-300 rounded focus:ring-medical-purple-500">
                                    <span class="mr-3 text-medical-gray-900">{{ $supplier->company_name }}</span>
                                </label>
                            @endforeach
                        </div>
                        <button type="submit"
                            class="w-full mt-4 px-4 py-2 bg-medical-purple-600 text-white rounded-lg hover:bg-medical-purple-700 transition-colors">
                            حفظ التعيين
                        </button>
                    </div>
                </form>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">إحصائيات سريعة</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-medical-gray-600">العناصر المطلوبة</span>
                        <span class="font-bold text-medical-gray-900">{{ $rfq->items->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-medical-gray-600">عروض الأسعار</span>
                        <span class="font-bold text-medical-gray-900">{{ $rfq->quotations->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-medical-gray-600">الموردون المعينون</span>
                        <span class="font-bold text-medical-gray-900">{{ $rfq->assignedSuppliers->count() }}</span>
                    </div>
                    @if($rfq->quotations->count() > 0)
                        <div class="pt-4 border-t border-medical-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-medical-gray-600">أقل عرض</span>
                                <span class="font-bold text-medical-green-600">
                                    {{ number_format($rfq->quotations->min('total_price'), 2) }} د.ل
                                </span>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-medical-gray-600">أعلى عرض</span>
                                <span class="font-bold text-medical-red-600">
                                    {{ number_format($rfq->quotations->max('total_price'), 2) }} د.ل
                                </span>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

