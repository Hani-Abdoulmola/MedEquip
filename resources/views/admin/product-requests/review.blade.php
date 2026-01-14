{{-- Admin Product Request - Review --}}
<x-dashboard.layout title="مراجعة طلب منتج" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">مراجعة طلب منتج</h1>
                <p class="mt-2 text-medical-gray-600">
                    مقدم من: <span class="font-semibold">{{ $productRequest->supplier?->company_name }}</span>
                </p>
            </div>
            <a href="{{ route('admin.product-requests.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                العودة
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Product Details --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b">بيانات المنتج المقترح</h2>
                
                <div class="grid grid-cols-2 gap-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-medical-gray-500 mb-1">اسم المنتج</label>
                        <p class="text-lg font-semibold text-medical-gray-900">{{ $productRequest->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-medical-gray-500 mb-1">العلامة التجارية</label>
                        <p class="text-medical-gray-900">{{ $productRequest->brand ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-medical-gray-500 mb-1">الموديل</label>
                        <p class="text-medical-gray-900">{{ $productRequest->model ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-medical-gray-500 mb-1">الفئة</label>
                        <p class="text-medical-gray-900">{{ $productRequest->category?->name ?? '-' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-medical-gray-500 mb-1">المصنع</label>
                        <p class="text-medical-gray-900">{{ $productRequest->manufacturer?->name ?? '-' }}</p>
                    </div>

                    @if($productRequest->description)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">الوصف</label>
                            <p class="text-medical-gray-900">{{ $productRequest->description }}</p>
                        </div>
                    @endif

                    @if($productRequest->specifications)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">المواصفات</label>
                            <ul class="list-disc list-inside text-medical-gray-900 space-y-1">
                                @foreach($productRequest->specifications as $spec)
                                    <li>{{ $spec }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($productRequest->features)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">المميزات</label>
                            <ul class="list-disc list-inside text-medical-gray-900 space-y-1">
                                @foreach($productRequest->features as $feature)
                                    <li>{{ $feature }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($productRequest->certifications)
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">الشهادات</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach($productRequest->certifications as $cert)
                                    <span class="px-3 py-1 text-sm bg-medical-green-100 text-medical-green-700 rounded-full">{{ $cert }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Supplier's Offer --}}
                <div class="mt-6 pt-6 border-t">
                    <h3 class="font-bold text-medical-gray-900 mb-4">عرض المورد</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-medical-blue-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-medical-gray-600">السعر المقترح</p>
                            <p class="text-xl font-bold text-medical-blue-600">
                                {{ $productRequest->proposed_price ? number_format($productRequest->proposed_price, 2) . ' د.ل' : '-' }}
                            </p>
                        </div>
                        <div class="bg-medical-green-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-medical-gray-600">الكمية</p>
                            <p class="text-xl font-bold text-medical-green-600">{{ $productRequest->proposed_stock ?? 0 }}</p>
                        </div>
                        <div class="bg-medical-purple-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-medical-gray-600">مدة التوصيل</p>
                            <p class="text-lg font-bold text-medical-purple-600">{{ $productRequest->proposed_lead_time ?? '-' }}</p>
                        </div>
                        <div class="bg-medical-yellow-50 rounded-xl p-4 text-center">
                            <p class="text-sm text-medical-gray-600">الضمان</p>
                            <p class="text-lg font-bold text-medical-yellow-600">{{ $productRequest->proposed_warranty ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Potential Duplicates Warning --}}
            @if($potentialDuplicates->isNotEmpty())
                <div class="bg-medical-yellow-50 border border-medical-yellow-200 rounded-2xl p-6">
                    <div class="flex items-start gap-3">
                        <svg class="w-6 h-6 text-medical-yellow-600 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <div class="flex-1">
                            <h3 class="font-bold text-medical-yellow-800">⚠️ منتجات مشابهة موجودة</h3>
                            <p class="text-sm text-medical-yellow-700 mt-1">تم العثور على منتجات مشابهة في الكتالوج. يمكنك دمج هذا الطلب مع منتج موجود.</p>
                            
                            <div class="mt-4 space-y-3">
                                @foreach($potentialDuplicates->take(5) as $duplicate)
                                    <div class="bg-white rounded-lg p-4 border border-medical-yellow-200">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="font-semibold text-medical-gray-900">{{ $duplicate->name }}</p>
                                                <p class="text-sm text-medical-gray-500">
                                                    {{ $duplicate->brand }} {{ $duplicate->model ? '- ' . $duplicate->model : '' }}
                                                </p>
                                            </div>
                                            <span class="px-3 py-1 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">
                                                {{ $duplicate->suppliers_count ?? $duplicate->suppliers->count() }} موردين
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            {{-- Supplier Info --}}
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <h3 class="font-bold text-medical-gray-900 mb-4">معلومات المورد</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-medical-gray-500">الشركة</p>
                        <p class="font-medium text-medical-gray-900">{{ $productRequest->supplier?->company_name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500">البريد الإلكتروني</p>
                        <p class="font-medium text-medical-gray-900">{{ $productRequest->supplier?->contact_email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-medical-gray-500">تاريخ الطلب</p>
                        <p class="font-medium text-medical-gray-900">{{ $productRequest->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Action Forms --}}
            @if($productRequest->canBeReviewed())
                {{-- Approve Form --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="font-bold text-medical-green-700 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        الموافقة (إنشاء منتج جديد)
                    </h3>
                    <form action="{{ route('admin.product-requests.approve', $productRequest) }}" method="POST">
                        @csrf
                        <textarea name="admin_notes" rows="2" placeholder="ملاحظات (اختياري)"
                            class="w-full px-4 py-2 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-green-500 mb-4"></textarea>
                        <button type="submit" class="w-full py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 font-medium">
                            ✅ الموافقة وإنشاء المنتج
                        </button>
                    </form>
                </div>

                {{-- Merge Form --}}
                @if($existingProducts->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="font-bold text-medical-blue-700 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            الدمج مع منتج موجود
                        </h3>
                        <form action="{{ route('admin.product-requests.merge', $productRequest) }}" method="POST">
                            @csrf
                            <select name="existing_product_id" required
                                class="w-full px-4 py-2 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 mb-3">
                                <option value="">اختر المنتج...</option>
                                @foreach($existingProducts as $product)
                                    <option value="{{ $product->id }}" {{ $productRequest->duplicate_of == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }} {{ $product->model ? '(' . $product->model . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <textarea name="admin_notes" rows="2" placeholder="ملاحظات (اختياري)"
                                class="w-full px-4 py-2 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 mb-4"></textarea>
                            <button type="submit" class="w-full py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 font-medium">
                                🔗 دمج مع المنتج المحدد
                            </button>
                        </form>
                    </div>
                @endif

                {{-- Reject Form --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="font-bold text-medical-red-700 mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        رفض الطلب
                    </h3>
                    <form action="{{ route('admin.product-requests.reject', $productRequest) }}" method="POST">
                        @csrf
                        <textarea name="rejection_reason" rows="2" placeholder="سبب الرفض (مطلوب)" required
                            class="w-full px-4 py-2 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-red-500 mb-3"></textarea>
                        <textarea name="admin_notes" rows="2" placeholder="ملاحظات إضافية (اختياري)"
                            class="w-full px-4 py-2 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-red-500 mb-4"></textarea>
                        <button type="submit" class="w-full py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 font-medium"
                            onclick="return confirm('هل أنت متأكد من رفض هذا الطلب؟')">
                            ❌ رفض الطلب
                        </button>
                    </form>
                </div>
            @else
                {{-- Already Reviewed --}}
                <div class="bg-medical-gray-50 rounded-2xl p-6 text-center">
                    <p class="text-medical-gray-600">تمت مراجعة هذا الطلب</p>
                    <p class="font-bold text-medical-gray-900 mt-2">{{ $productRequest->status_label }}</p>
                    @if($productRequest->reviewer)
                        <p class="text-sm text-medical-gray-500 mt-2">
                            بواسطة: {{ $productRequest->reviewer->name }}
                            <br>
                            {{ $productRequest->reviewed_at?->format('Y-m-d H:i') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>

