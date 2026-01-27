{{-- Buyer RFQ Cart / Request Builder --}}
<x-dashboard.layout title="منشئ طلبات العروض" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6" x-data="{
        showClearModal: false,
        submitting: false,
        removeProduct(productId) {
            fetch(`/buyer/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data.success) location.reload();
            });
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    منشئ طلبات العروض
                </h1>
                <p class="mt-1 text-sm text-gray-500">أضف المنتجات التي تحتاجها وأرسلها كطلب عرض سعر واحد</p>
            </div>
            <a href="{{ route('buyer.products.index') }}"
                class="inline-flex items-center px-4 py-2 text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة منتجات
            </a>
        </div>

        {{-- Skipped Items Alert (Phase 2) --}}
        @if(session('skipped_items') && count(session('skipped_items')) > 0)
            <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div class="flex-1">
                        <h3 class="font-bold text-yellow-800 mb-2">تم تخطي بعض المنتجات</h3>
                        <ul class="text-sm text-yellow-700 space-y-1">
                            @foreach(session('skipped_items') as $skipped)
                                <li class="flex items-start gap-2">
                                    <span class="text-yellow-600">•</span>
                                    <span><strong>{{ $skipped['name'] }}</strong>: {{ $skipped['reason'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        @if (count($products) > 0)
            {{-- Cart Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">
                        المنتجات في منشئ الطلبات
                        <span class="text-sm text-gray-500 font-normal">({{ count($products) }} منتج)</span>
                    </h2>
                    <button @click="showClearModal = true" class="text-sm text-red-600 hover:text-red-700 font-medium">
                        إفراغ منشئ الطلبات
                    </button>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach ($products as $index => $item)
                        @php
                            $product = $item['product'];
                            $cartItem = $item['item'] ?? null;
                        @endphp
                        @php
                            $isValid = $cartItem ? ($cartItem->is_valid ?? true) : true;
                            $warnings = $cartItem ? ($cartItem->warnings ?? []) : [];
                        @endphp
                        <div class="p-4 hover:bg-gray-50 transition-colors {{ !$isValid ? 'bg-red-50 border-r-4 border-red-500' : '' }}">
                            {{-- Validation Warning (Phase 2) --}}
                            @if(!$isValid && !empty($warnings))
                                <div class="mb-3 p-3 bg-red-100 border border-red-300 rounded-lg">
                                    <div class="flex items-center gap-2 mb-1">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        <span class="text-sm font-bold text-red-800">تحذير: هذا المنتج غير صالح</span>
                                    </div>
                                    <ul class="text-xs text-red-700 list-disc list-inside space-y-1">
                                        @foreach($warnings as $warning)
                                            <li>{{ $warning }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="flex flex-col lg:flex-row gap-4">
                                {{-- Product Image --}}
                                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if ($product->getFirstMediaUrl('product_images'))
                                        <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}"
                                            alt="{{ $product->name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                </path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            @if ($product->category)
                                                <span class="text-xs text-medical-blue-600 font-medium">
                                                    {{ $product->category->name }}
                                                </span>
                                            @endif
                                            <h3 class="font-medium text-gray-900">{{ $product->name }}</h3>
                                            @if ($product->brand)
                                                <p class="text-sm text-gray-500">{{ $product->brand }}</p>
                                            @endif
                                        </div>
                                        @if ($cartItem)
                                            <a href="{{ route('buyer.cart.remove', $cartItem) }}"
                                                onclick="event.preventDefault(); document.getElementById('remove-form-{{ $cartItem->id }}').submit();"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </a>
                                            <form id="remove-form-{{ $cartItem->id }}"
                                                action="{{ route('buyer.cart.remove', $cartItem) }}" method="POST"
                                                class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @else
                                            <button @click="removeProduct({{ $product->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>

                                    {{-- Quantity & Specs --}}
                                    @if ($cartItem)
                                        <form action="{{ route('buyer.cart.update', $cartItem) }}" method="POST"
                                            class="mt-3">
                                            @csrf
                                            @method('PUT')
                                        @else
                                            <form action="{{ route('buyer.cart.update.legacy', $product) }}"
                                                method="POST" class="mt-3">
                                                @csrf
                                                @method('PUT')
                                    @endif
                                    <div class="flex flex-col sm:flex-row gap-3">
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">الكمية:</label>
                                            <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                                min="1" max="10000"
                                                class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <label class="text-sm text-gray-600">الوحدة:</label>
                                            <select name="unit"
                                                class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                                <option value="وحدة" {{ $item['unit'] == 'وحدة' ? 'selected' : '' }}>
                                                    وحدة</option>
                                                <option value="قطعة" {{ $item['unit'] == 'قطعة' ? 'selected' : '' }}>
                                                    قطعة</option>
                                                <option value="عبوة" {{ $item['unit'] == 'عبوة' ? 'selected' : '' }}>
                                                    عبوة</option>
                                                <option value="صندوق"
                                                    {{ $item['unit'] == 'صندوق' ? 'selected' : '' }}>صندوق</option>
                                                <option value="طقم" {{ $item['unit'] == 'طقم' ? 'selected' : '' }}>
                                                    طقم</option>
                                                <option value="جهاز" {{ $item['unit'] == 'جهاز' ? 'selected' : '' }}>
                                                    جهاز</option>
                                            </select>
                                        </div>
                                        <button type="submit"
                                            class="px-3 py-1.5 text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                            تحديث
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <input type="text" name="specifications"
                                            value="{{ $item['specifications'] }}"
                                            placeholder="مواصفات إضافية (اختياري)"
                                            class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                    </div>
                                    </form>

                                    {{-- Suppliers Count --}}
                                    @if ($product->suppliers->count() > 0)
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                                    </path>
                                                </svg>
                                                {{ $product->suppliers->count() }} مورد متاح لهذا المنتج
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Summary & Actions (Phase 2) --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="{ showSaveTemplate: false, templateName: '' }">
                @php
                    $summary = $summary ?? ['items_count' => count($products), 'valid_items' => count($products), 'invalid_items' => 0, 'can_submit' => true];
                @endphp
                <div class="space-y-4">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-semibold text-gray-900">ملخص منشئ الطلبات</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                {{ $summary['items_count'] }} منتج - {{ $totalItems }} وحدة إجمالية
                                @if($summary['invalid_items'] > 0)
                                    <span class="text-red-600 font-semibold">({{ $summary['invalid_items'] }} غير صالح)</span>
                                @endif
                            </p>
                            @if($summary['invalid_items'] > 0)
                                <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-xs text-red-700">
                                        ⚠️ يوجد {{ $summary['invalid_items'] }} منتج غير صالح. يرجى مراجعتها أو إزالتها قبل الإرسال.
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="flex flex-wrap gap-2 w-full sm:w-auto">
                            @if($templates->count() > 0)
                                <div class="relative" x-data="{ showTemplates: false }">
                                    <button @click="showTemplates = !showTemplates" 
                                        class="px-4 py-2.5 bg-medical-green-100 text-medical-green-700 rounded-lg hover:bg-medical-green-200 transition-colors text-sm font-medium">
                                        📋 تحميل قالب
                                    </button>
                                    <div x-show="showTemplates" @click.away="showTemplates = false" x-cloak
                                        class="absolute bottom-full mb-2 right-0 bg-white rounded-lg shadow-lg border border-gray-200 min-w-[200px] z-10">
                                        @foreach($templates as $template)
                                            <form action="{{ route('buyer.cart.template.load', $template) }}" method="POST" class="block">
                                                @csrf
                                                <button type="submit" class="w-full text-right px-4 py-2 hover:bg-gray-50 text-sm">
                                                    {{ $template->template_name }}
                                                </button>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            <button @click="showSaveTemplate = true" 
                                class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                💾 حفظ كقالب
                            </button>
                            <a href="{{ route('buyer.products.index') }}"
                                class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium">
                                متابعة التصفح
                            </a>
                            <a href="{{ route('buyer.cart.checkout') }}"
                                class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium {{ !$summary['can_submit'] ? 'opacity-50 cursor-not-allowed' : '' }}"
                                @if(!$summary['can_submit']) onclick="event.preventDefault(); alert('يوجد منتجات غير صالحة. يرجى مراجعتها أولاً.');" @endif>
                                إرسال كطلب عرض سعر
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Save Template Modal (Phase 2) --}}
                <div x-show="showSaveTemplate" x-cloak @click.away="showSaveTemplate = false"
                    class="fixed inset-0 z-50 overflow-y-auto mt-8">
                    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSaveTemplate = false"></div>
                        <div class="relative bg-white rounded-lg px-4 pt-5 pb-4 shadow-xl max-w-md w-full">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">حفظ كقالب</h3>
                            <form action="{{ route('buyer.cart.checkout') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="save_template" value="1">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم القالب</label>
                                    <input type="text" x-model="templateName" name="template_name" required
                                        placeholder="مثال: طلب شهري للمستشفى"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500">
                                </div>
                                <div class="flex gap-3">
                                    <button type="button" @click="showSaveTemplate = false"
                                        class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">
                                        إلغاء
                                    </button>
                                    <button type="submit" :disabled="!templateName"
                                        class="flex-1 px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 disabled:opacity-50">
                                        حفظ
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">منشئ الطلبات فارغ</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    لم تقم بإضافة أي منتجات بعد. تصفح كتالوج المنتجات وأضف ما تحتاجه لإنشاء طلب عرض سعر.
                </p>
                <a href="{{ route('buyer.products.index') }}"
                    class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    تصفح المنتجات
                </a>
            </div>
        @endif

        {{-- Clear Cart Modal --}}
        <div x-show="showClearModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showClearModal" @click="showClearModal = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showClearModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                إفراغ منشئ الطلبات
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    هل أنت متأكد من إفراغ منشئ الطلبات؟ سيتم حذف جميع المنتجات المضافة.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <form action="{{ route('buyer.cart.clear') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                                نعم، إفراغ منشئ الطلبات
                            </button>
                        </form>
                        <button @click="showClearModal = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-medical-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layout>
