{{-- Admin Product Request - Show --}}
<x-dashboard.layout title="تفاصيل طلب المنتج" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل طلب المنتج</h1>
                <p class="mt-2 text-medical-gray-600">{{ $productRequest->name }}</p>
            </div>
            <div class="flex items-center gap-3">
                @if($productRequest->canBeReviewed())
                    <a href="{{ route('admin.product-requests.review', $productRequest) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        مراجعة
                    </a>
                @endif
                <a href="{{ route('admin.product-requests.index') }}"
                   class="inline-flex items-center gap-2 px-4 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    العودة
                </a>
            </div>
        </div>
    </div>

    {{-- Status Badge --}}
    @php
        $statusColors = [
            'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'duplicate' => 'bg-purple-100 text-purple-800 border-purple-200',
            'approved' => 'bg-green-100 text-green-800 border-green-200',
            'merged' => 'bg-blue-100 text-blue-800 border-blue-200',
            'rejected' => 'bg-red-100 text-red-800 border-red-200',
            'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
        ];
    @endphp
    <div class="mb-6">
        <span class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-full border {{ $statusColors[$productRequest->status] ?? 'bg-gray-100' }}">
            {{ $productRequest->status_label }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Product Information --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">معلومات المنتج</h2>
                
                <dl class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <dt class="text-sm font-medium text-medical-gray-500">اسم المنتج</dt>
                        <dd class="mt-1 text-lg font-semibold text-medical-gray-900">{{ $productRequest->name }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-medical-gray-500">العلامة التجارية</dt>
                        <dd class="mt-1 text-medical-gray-900">{{ $productRequest->brand ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-medical-gray-500">الموديل</dt>
                        <dd class="mt-1 text-medical-gray-900">{{ $productRequest->model ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-medical-gray-500">الفئة</dt>
                        <dd class="mt-1 text-medical-gray-900">{{ $productRequest->category?->full_path ?? '-' }}</dd>
                    </div>

                    <div>
                        <dt class="text-sm font-medium text-medical-gray-500">الشركة المصنعة</dt>
                        <dd class="mt-1 text-medical-gray-900">{{ $productRequest->manufacturer?->name ?? '-' }}</dd>
                    </div>

                    @if($productRequest->description)
                        <div class="col-span-2">
                            <dt class="text-sm font-medium text-medical-gray-500">الوصف</dt>
                            <dd class="mt-1 text-medical-gray-900">{{ $productRequest->description }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            {{-- Specifications & Features --}}
            @if($productRequest->specifications || $productRequest->features || $productRequest->certifications)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">المواصفات والمميزات</h2>
                    
                    @if($productRequest->specifications)
                        <div class="mb-6">
                            <h3 class="font-semibold text-medical-gray-900 mb-3">المواصفات</h3>
                            <ul class="list-disc list-inside space-y-1 text-medical-gray-700">
                                @foreach($productRequest->specifications as $spec)
                                    <li>{{ $spec }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($productRequest->features)
                        <div class="mb-6">
                            <h3 class="font-semibold text-medical-gray-900 mb-3">المميزات</h3>
                            <ul class="list-disc list-inside space-y-1 text-medical-gray-700">
                                @foreach($productRequest->features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($productRequest->certifications)
                        <div>
                            <h3 class="font-semibold text-medical-gray-900 mb-3">الشهادات</h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productRequest->certifications as $cert)
                                    <span class="px-3 py-1 bg-medical-green-100 text-medical-green-700 rounded-full text-sm">{{ $cert }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Linked Product (if merged) --}}
            @if($productRequest->existingProduct)
                <div class="bg-medical-blue-50 rounded-2xl p-6 border border-medical-blue-200">
                    <h3 class="font-bold text-medical-blue-800 mb-3">🔗 مدمج مع منتج موجود</h3>
                    <div class="bg-white rounded-xl p-4">
                        <p class="font-semibold text-medical-gray-900">{{ $productRequest->existingProduct->name }}</p>
                        <p class="text-sm text-medical-gray-500 mt-1">
                            {{ $productRequest->existingProduct->brand }} 
                            {{ $productRequest->existingProduct->model ? '- ' . $productRequest->existingProduct->model : '' }}
                        </p>
                        <a href="{{ route('admin.products.show', $productRequest->existingProduct) }}"
                           class="inline-flex items-center gap-1 text-sm text-medical-blue-600 hover:underline mt-2">
                            عرض المنتج
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endif

            {{-- Rejection Reason --}}
            @if($productRequest->rejection_reason)
                <div class="bg-medical-red-50 rounded-2xl p-6 border border-medical-red-200">
                    <h3 class="font-bold text-medical-red-800 mb-2">❌ سبب الرفض</h3>
                    <p class="text-medical-red-700">{{ $productRequest->rejection_reason }}</p>
                </div>
            @endif

            {{-- Admin Notes --}}
            @if($productRequest->admin_notes)
                <div class="bg-medical-gray-50 rounded-2xl p-6">
                    <h3 class="font-bold text-medical-gray-800 mb-2">📝 ملاحظات الإدارة</h3>
                    <p class="text-medical-gray-700">{{ $productRequest->admin_notes }}</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="font-bold text-medical-gray-900 mb-4">معلومات المورد</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-medical-gray-500">الشركة</p>
                        <p class="font-semibold text-medical-gray-900">{{ $productRequest->supplier?->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500">البريد الإلكتروني</p>
                        <p class="text-medical-gray-900">{{ $productRequest->supplier?->contact_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500">الهاتف</p>
                        <p class="text-medical-gray-900">{{ $productRequest->supplier?->contact_phone ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Proposed Offer --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="font-bold text-medical-gray-900 mb-4">العرض المقترح</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-medical-gray-600">السعر</span>
                        <span class="font-bold text-medical-blue-600">
                            {{ $productRequest->proposed_price ? number_format($productRequest->proposed_price, 2) . ' د.ل' : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-medical-gray-600">الكمية</span>
                        <span class="font-semibold">{{ $productRequest->proposed_stock ?? 0 }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b">
                        <span class="text-medical-gray-600">مدة التوصيل</span>
                        <span>{{ $productRequest->proposed_lead_time ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-medical-gray-600">الضمان</span>
                        <span>{{ $productRequest->proposed_warranty ?? '-' }}</span>
                    </div>
                </div>
            </div>

            {{-- Timeline --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="font-bold text-medical-gray-900 mb-4">الجدول الزمني</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 bg-medical-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-medical-gray-900">تم إنشاء الطلب</p>
                            <p class="text-sm text-medical-gray-500">{{ $productRequest->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    @if($productRequest->reviewed_at)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 {{ $productRequest->status === 'rejected' ? 'bg-medical-red-100' : 'bg-medical-green-100' }} rounded-full flex items-center justify-center flex-shrink-0">
                                @if($productRequest->status === 'rejected')
                                    <svg class="w-4 h-4 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                @else
                                    <svg class="w-4 h-4 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <p class="font-medium text-medical-gray-900">تمت المراجعة</p>
                                <p class="text-sm text-medical-gray-500">{{ $productRequest->reviewed_at->format('Y-m-d H:i') }}</p>
                                @if($productRequest->reviewer)
                                    <p class="text-sm text-medical-gray-500">بواسطة: {{ $productRequest->reviewer->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

