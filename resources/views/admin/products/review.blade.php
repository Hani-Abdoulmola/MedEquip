{{-- Admin - Review Product --}}
<x-dashboard.layout title="مراجعة منتج" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">مراجعة المنتج</h1>
                <p class="mt-2 text-medical-gray-600">تحقق من صحة البيانات قبل اعتماد المنتج</p>
            </div>

            <a href="{{ route('admin.products.index') }}"
                class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700
               rounded-xl hover:bg-medical-gray-200 transition font-semibold">
                ← رجوع
            </a>
        </div>

        {{-- Review Badge --}}
        @php
            $badge = [
                'pending' => 'bg-yellow-100 text-yellow-700',
                'approved' => 'bg-green-100 text-green-700',
                'rejected' => 'bg-red-100 text-red-700',
                'needs_update' => 'bg-blue-100 text-blue-700',
            ][$product->review_status];
        @endphp

        <span class="px-5 py-2.5 rounded-xl text-sm font-semibold {{ $badge }}">
            {{ ['pending' => '⏳ قيد المراجعة', 'approved' => '✔ معتمد', 'rejected' => '✖ مرفوض', 'needs_update' => '✏️ يحتاج تعديل'][$product->review_status] }}
        </span>

        {{-- GRID LAYOUT --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

            {{-- LEFT COLUMN --}}
            <div class="lg:col-span-1">
                <div class="bg-white rounded-2xl shadow-medical p-6">

                    {{-- Product Image --}}
                    <div
                        class="w-full h-64 bg-medical-gray-100 rounded-xl mb-6 flex items-center justify-center overflow-hidden">
                        @if ($product->hasMedia('product_images'))
                            <img src="{{ $product->getFirstMediaUrl('product_images') }}"
                                class="w-full h-full object-cover">
                        @else
                            <svg class="w-24 h-24 text-medical-gray-400" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        @endif
                    </div>

                    {{-- Basic Info --}}
                    <div class="space-y-4">

                        <x-admin.review.field label="اسم المنتج" :value="$product->name" />

                        @if ($product->model)
                            <x-admin.review.field label="الموديل" :value="$product->model" />
                        @endif

                        @if ($product->brand)
                            <x-admin.review.field label="العلامة التجارية" :value="$product->brand" />
                        @endif

                        {{-- Manufacturer --}}
                        <div>
                            <p class="text-sm text-medical-gray-600">الشركة المصنّعة</p>
                            @if ($product->manufacturer)
                                <span
                                    class="inline-flex px-3 py-1 text-xs rounded-full bg-medical-purple-100 text-medical-purple-700 mt-1">
                                    {{ $product->manufacturer->name }}
                                </span>
                            @else
                                <span class="text-sm text-medical-gray-500">غير محدد</span>
                            @endif
                        </div>

                        {{-- Category --}}
                        <div>
                            <p class="text-sm text-medical-gray-600">الفئة</p>
                            @if ($product->category)
                                <span
                                    class="inline-flex px-3 py-1 text-xs rounded-full bg-medical-blue-100 text-medical-blue-700 mt-1">
                                    {{ $product->category->name }}
                                </span>
                            @else
                                <span class="text-sm text-medical-gray-500">غير محدد</span>
                            @endif
                        </div>

                        {{-- Previous Notes --}}
                        @if ($product->review_notes)
                            <div class="bg-blue-50 p-4 rounded-xl border border-blue-200">
                                <p class="text-sm font-semibold text-blue-700">ملاحظات سابقة:</p>
                                <p class="text-blue-800 mt-1">{{ $product->review_notes }}</p>
                            </div>
                        @endif

                        @if ($product->rejection_reason)
                            <div class="bg-red-50 p-4 rounded-xl border border-red-200">
                                <p class="text-sm font-semibold text-red-700">سبب الرفض السابق:</p>
                                <p class="text-red-800 mt-1">{{ $product->rejection_reason }}</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Description --}}
                <x-admin.review.block title="الوصف">
                    {{ $product->description ?: 'لا يوجد وصف' }}
                </x-admin.review.block>

                {{-- Specs --}}
                @if (!empty($product->specifications))
                    <x-admin.review.list title="المواصفات" :items="$product->specifications" />
                @endif

                {{-- Features --}}
                @if (!empty($product->features))
                    <x-admin.review.list title="المميزات" :items="$product->features" />
                @endif

                {{-- Technical Data --}}
                @if (!empty($product->technical_data))
                    <x-admin.review.list title="البيانات التقنية" :items="$product->technical_data" />
                @endif

                {{-- Certifications --}}
                @if (!empty($product->certifications))
                    <x-admin.review.list title="الشهادات والاعتمادات" :items="$product->certifications" />
                @endif

                {{-- Installation Requirements --}}
                @if (!empty($product->installation_requirements))
                    <x-admin.review.block title="متطلبات التركيب">
                        {{ $product->installation_requirements }}
                    </x-admin.review.block>
                @endif

                {{-- Suppliers --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">
                        الموردون ({{ $product->suppliers->count() }})
                    </h3>

                    @foreach ($product->suppliers as $supplier)
                        <div class="p-4 bg-medical-gray-50 rounded-xl mb-3 flex items-center justify-between">

                            {{-- Supplier --}}
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500
                                    rounded-xl flex items-center justify-center text-white text-lg font-bold">
                                    {{ mb_substr($supplier->company_name, 0, 1) }}
                                </div>

                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $supplier->company_name }}</p>

                                    <p class="text-sm text-medical-gray-600 mt-1">
                                        السعر:
                                        <span class="font-bold text-medical-blue-600">
                                            {{ number_format($supplier->pivot->price, 2) }} د.ل
                                        </span>
                                    </p>

                                    <p class="text-sm text-medical-gray-600">
                                        المخزون: {{ $supplier->pivot->stock_quantity }}
                                    </p>
                                </div>
                            </div>

                            {{-- Supplier Offer Status --}}
                            @php
                                $status = [
                                    'available' => ['متوفر', 'bg-medical-green-100 text-medical-green-700'],
                                    'out_of_stock' => ['نفد من المخزون', 'bg-medical-red-100 text-medical-red-700'],
                                    'suspended' => ['معلق', 'bg-medical-yellow-100 text-medical-yellow-700'],
                                ][$supplier->pivot->status];
                            @endphp

                            <span class="px-3 py-1 text-xs rounded-full font-semibold {{ $status[1] }}">
                                {{ $status[0] }}
                            </span>

                        </div>
                    @endforeach
                </div>

                {{-- Documents --}}
                @if ($product->hasMedia('product_documents'))
                    <div class="bg-white rounded-2xl shadow-medical p-6">
                        <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">المستندات</h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($product->getMedia('product_documents') as $document)
                                <a href="{{ $document->getUrl() }}" target="_blank"
                                    class="flex items-center p-4 bg-medical-gray-50 rounded-xl hover:bg-medical-blue-50 transition">
                                    <svg class="w-8 h-8 text-medical-red-600" fill="none" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707L12
                                              3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <div class="mr-3 flex-1">
                                        <p class="font-medium text-medical-gray-900">{{ $document->file_name }}</p>
                                        <p class="text-sm text-medical-gray-600">{{ $document->human_readable_size }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>

        {{-- ACTION BUTTONS --}}
        <div class="mt-10 flex items-center justify-end gap-4">

            <form method="POST" action="{{ route('admin.products.approve', $product->id) }}">
                @csrf
                <button
                    class="px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700
                    transition font-semibold">
                    ✔ اعتماد المنتج
                </button>
            </form>

            <button onclick="rejectModal.showModal()"
                class="px-6 py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 transition font-semibold">
                ✖ رفض المنتج
            </button>

            <button onclick="changesModal.showModal()"
                class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition font-semibold">
                ✏ طلب تعديلات
            </button>

        </div>


        {{-- REJECT MODAL --}}
        <dialog id="rejectModal" class="rounded-2xl shadow-xl w-full max-w-md p-0">
            <form method="POST" action="{{ route('admin.products.reject', $product->id) }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-medical-gray-900 mb-4">سبب الرفض</h2>

                <textarea name="reason" rows="4" required
                    class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl
                                 focus:ring-4 focus:ring-medical-red-500 focus:border-medical-red-500"></textarea>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="rejectModal.close()"
                        class="px-4 py-2 bg-medical-gray-200 rounded-xl">
                        إلغاء
                    </button>
                    <button class="px-4 py-2 bg-medical-red-600 text-white rounded-xl">رفض المنتج</button>
                </div>
            </form>
        </dialog>


        {{-- CHANGES MODAL --}}
        <dialog id="changesModal" class="rounded-2xl shadow-xl w-full max-w-md p-0">
            <form method="POST" action="{{ route('admin.products.request_changes', $product->id) }}" class="p-6">
                @csrf
                <h2 class="text-lg font-bold text-medical-gray-900 mb-4">ملاحظات طلب التعديلات</h2>

                <textarea name="notes" rows="4" required
                    class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl
                                 focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500"></textarea>

                <div class="flex items-center justify-end gap-3 mt-6">
                    <button type="button" onclick="changesModal.close()"
                        class="px-4 py-2 bg-medical-gray-200 rounded-xl">
                        إلغاء
                    </button>
                    <button class="px-4 py-2 bg-medical-blue-600 text-white rounded-xl">
                        إرسال الطلب
                    </button>
                </div>
            </form>
        </dialog>

    </div>

</x-dashboard.layout>
