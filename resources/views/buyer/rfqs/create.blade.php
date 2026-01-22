<x-dashboard.layout title="إنشاء طلب عرض سعر" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6" x-data="rfqForm()">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-green-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">إنشاء طلب عرض سعر جديد</h1>
                        <p class="mt-1 text-sm text-gray-600">أنشئ طلب عرض سعر (RFQ) للحصول على عروض تنافسية من الموردين
                        </p>
                    </div>
                </div>
            </div>
            <a href="{{ route('buyer.rfqs.index') }}"
                class="px-4 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-semibold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                رجوع
            </a>
        </div>

        {{-- Pre-filled Product Alert --}}
        @if (session('rfq_product'))
            <div
                class="bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border-2 border-medical-blue-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-center gap-4">
                    <div
                        class="w-10 h-10 bg-medical-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-medical-blue-900 mb-1">تم تحديد منتج مسبقاً</h4>
                        <p class="text-sm text-medical-blue-700">سيتم إضافة المنتج
                            "<strong>{{ session('rfq_product.name') }}</strong>" تلقائياً إلى بنود الطلب</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-200 rounded-xl p-5 shadow-sm">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-red-900 mb-2">يرجى تصحيح الأخطاء التالية:</h4>
                        <ul class="space-y-1 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-start gap-2">
                                    <span class="text-red-500 mt-1">•</span>
                                    <span>{{ $error }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('buyer.rfqs.store') }}" method="POST" class="space-y-6">
            @csrf
            {{-- Note: buyer_id is automatically set from authenticated user in controller --}}

            {{-- Basic Information --}}
            <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b-2 border-gray-200">
                    <div
                        class="w-10 h-10 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">المعلومات الأساسية</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-900 mb-2">
                            عنوان الطلب <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-lg font-medium"
                            placeholder="مثال: طلب أجهزة تعقيم طبية">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-900 mb-2">
                            الوصف
                        </label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all resize-none"
                            placeholder="وصف تفصيلي للطلب والمتطلبات الخاصة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label for="deadline" class="block text-sm font-bold text-gray-900 mb-2">
                            الموعد النهائي لتقديم العروض
                        </label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline') }}"
                            min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all font-medium">
                        @error('deadline')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-bold text-gray-900 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all font-medium bg-white">
                            <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>مسودة (حفظ
                                للتعديل لاحقاً)</option>
                            <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>مفتوح
                                (إرسال للموردين)</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Visibility --}}
                    <div class="md:col-span-2">
                        <div
                            class="p-4 bg-gradient-to-r from-medical-blue-50 to-medical-green-50 rounded-xl border-2 border-medical-blue-200">
                            <label class="flex items-center gap-3 cursor-pointer">
                                <input type="hidden" name="is_public" value="0">
                                <input type="checkbox" name="is_public" value="1"
                                    {{ old('is_public', true) ? 'checked' : '' }}
                                    class="w-5 h-5 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500 cursor-pointer">
                                <div>
                                    <span class="text-sm font-bold text-gray-900">طلب عام</span>
                                    <p class="text-xs text-gray-600 mt-0.5">سيتمكن جميع الموردين الموثقين من رؤية هذا
                                        الطلب وتقديم عروض</p>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RFQ Items --}}
            <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-6">
                <div class="flex items-center justify-between mb-6 pb-4 border-b-2 border-gray-200">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-medical-green-100 to-medical-green-200 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">بنود الطلب</h2>
                            <p class="text-xs text-gray-500 mt-0.5">أضف المنتجات أو الخدمات المطلوبة</p>
                        </div>
                    </div>
                    <button type="button" @click="addItem()"
                        class="inline-flex items-center px-4 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all text-sm font-semibold shadow-sm hover:shadow-md">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        إضافة بند
                    </button>
                </div>

                <div class="space-y-4" id="items-container">
                    <template x-for="(item, index) in items" :key="index">
                        <div
                            class="border-2 border-gray-200 rounded-xl p-5 bg-gradient-to-br from-gray-50 to-white hover:border-medical-blue-300 transition-all duration-200">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-8 h-8 bg-medical-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-medical-blue-700"
                                            x-text="index + 1"></span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-700">البند <span
                                            x-text="index + 1"></span></span>
                                </div>
                                <button type="button" @click="removeItem(index)" x-show="items.length > 1"
                                    class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-all"
                                    title="حذف البند">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                {{-- Product Selection (Optional) --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">المنتج (اختياري)</label>
                                    <select :name="'items[' + index + '][product_id]'" x-model="item.product_id"
                                        @change="onProductSelect(index)"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-sm font-medium bg-white">
                                        <option value="">-- اختر منتج --</option>
                                        @foreach ($products as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Item Name --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">
                                        اسم البند <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" :name="'items[' + index + '][item_name]'"
                                        x-model="item.item_name" required
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-sm font-medium"
                                        placeholder="اسم المنتج أو الخدمة">
                                </div>

                                {{-- Quantity --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">
                                        الكمية <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" :name="'items[' + index + '][quantity]'"
                                        x-model="item.quantity" required min="1"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-sm font-medium"
                                        placeholder="1">
                                </div>

                                {{-- Unit --}}
                                <div>
                                    <label class="block text-sm font-bold text-gray-900 mb-2">الوحدة</label>
                                    <input type="text" :name="'items[' + index + '][unit]'" x-model="item.unit"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-sm font-medium"
                                        placeholder="وحدة">
                                </div>

                                {{-- Specifications --}}
                                <div class="md:col-span-2 lg:col-span-4">
                                    <label class="block text-sm font-bold text-gray-900 mb-2">المواصفات</label>
                                    <textarea :name="'items[' + index + '][specifications]'" x-model="item.specifications" rows="3"
                                        class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all text-sm resize-none"
                                        placeholder="مواصفات تفصيلية للبند..."></textarea>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>

                @error('items')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Buttons --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t-2 border-gray-200">
                <a href="{{ route('buyer.rfqs.index') }}"
                    class="px-8 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-all font-semibold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all font-semibold shadow-sm hover:shadow-md flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                        </path>
                    </svg>
                    إنشاء الطلب
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            function rfqForm() {
                return {
                    items: [
                        @if (session('rfq_product'))
                            {
                                product_id: '{{ session('rfq_product.id') }}',
                                item_name: '{{ session('rfq_product.name') }}',
                                quantity: 1,
                                unit: 'وحدة',
                                specifications: ''
                            }
                        @elseif (old('items'))
                            @foreach (old('items') as $item)
                                {
                                    product_id: '{{ $item['product_id'] ?? '' }}',
                                    item_name: '{{ $item['item_name'] ?? '' }}',
                                    quantity: {{ $item['quantity'] ?? 1 }},
                                    unit: '{{ $item['unit'] ?? 'وحدة' }}',
                                    specifications: '{{ $item['specifications'] ?? '' }}'
                                },
                            @endforeach
                        @else
                            {
                                product_id: '',
                                item_name: '',
                                quantity: 1,
                                unit: 'وحدة',
                                specifications: ''
                            }
                        @endif
                    ],

                    products: @json($products->toArray()),

                    addItem() {
                        this.items.push({
                            product_id: '',
                            item_name: '',
                            quantity: 1,
                            unit: 'وحدة',
                            specifications: ''
                        });
                    },

                    removeItem(index) {
                        if (this.items.length > 1) {
                            this.items.splice(index, 1);
                        }
                    },

                    onProductSelect(index) {
                        const productId = this.items[index].product_id;
                        if (productId && this.products[productId]) {
                            this.items[index].item_name = this.products[productId];
                        }
                    }
                };
            }
        </script>
    @endpush
</x-dashboard.layout>
