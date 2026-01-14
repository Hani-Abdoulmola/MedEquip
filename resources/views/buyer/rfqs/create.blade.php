<x-dashboard.layout title="إنشاء طلب عرض سعر" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6" x-data="rfqForm()">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">إنشاء طلب عرض سعر جديد</h1>
            <p class="mt-1 text-sm text-gray-500">أنشئ طلب عرض سعر (RFQ) للحصول على عروض من الموردين</p>
        </div>
        <a href="{{ route('buyer.rfqs.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </a>
    </div>

    {{-- Pre-filled Product Alert --}}
    @if(session('rfq_product'))
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="text-blue-800">سيتم إضافة المنتج "{{ session('rfq_product.name') }}" تلقائياً</span>
        </div>
    </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h4 class="font-medium text-red-800">يرجى تصحيح الأخطاء التالية:</h4>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
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
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">المعلومات الأساسية</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="md:col-span-2">
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                        عنوان الطلب <span class="text-red-500">*</span>
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                           placeholder="مثال: طلب أجهزة تعقيم طبية">
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                        الوصف
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="3"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent"
                              placeholder="وصف تفصيلي للطلب والمتطلبات الخاصة...">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Deadline --}}
                <div>
                    <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                        الموعد النهائي لتقديم العروض
                    </label>
                    <input type="date" 
                           id="deadline" 
                           name="deadline" 
                           value="{{ old('deadline') }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    @error('deadline')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                        الحالة <span class="text-red-500">*</span>
                    </label>
                    <select id="status" 
                            name="status" 
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                        <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>مسودة (حفظ للتعديل لاحقاً)</option>
                        <option value="open" {{ old('status', 'open') === 'open' ? 'selected' : '' }}>مفتوح (إرسال للموردين)</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Visibility --}}
                <div class="md:col-span-2">
                    <label class="flex items-center gap-3">
                        <input type="hidden" name="is_public" value="0">
                        <input type="checkbox" 
                               name="is_public" 
                               value="1"
                               {{ old('is_public', true) ? 'checked' : '' }}
                               class="w-5 h-5 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500">
                        <span class="text-sm text-gray-700">
                            <strong>طلب عام</strong> - سيتمكن جميع الموردين الموثقين من رؤية هذا الطلب وتقديم عروض
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- RFQ Items --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900">بنود الطلب</h2>
                <button type="button" 
                        @click="addItem()"
                        class="inline-flex items-center px-3 py-1.5 bg-medical-blue-50 text-medical-blue-600 rounded-lg hover:bg-medical-blue-100 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إضافة بند
                </button>
            </div>

            <div class="space-y-4" id="items-container">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-sm font-medium text-gray-700">البند <span x-text="index + 1"></span></span>
                            <button type="button" 
                                    @click="removeItem(index)"
                                    x-show="items.length > 1"
                                    class="text-red-500 hover:text-red-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                            {{-- Product Selection (Optional) --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">المنتج (اختياري)</label>
                                <select :name="'items[' + index + '][product_id]'"
                                        x-model="item.product_id"
                                        @change="onProductSelect(index)"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm">
                                    <option value="">-- اختر منتج --</option>
                                    @foreach($products as $id => $name)
                                        <option value="{{ $id }}">{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Item Name --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    اسم البند <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       :name="'items[' + index + '][item_name]'"
                                       x-model="item.item_name"
                                       required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm"
                                       placeholder="اسم المنتج أو الخدمة">
                            </div>

                            {{-- Quantity --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    الكمية <span class="text-red-500">*</span>
                                </label>
                                <input type="number" 
                                       :name="'items[' + index + '][quantity]'"
                                       x-model="item.quantity"
                                       required
                                       min="1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm"
                                       placeholder="1">
                            </div>

                            {{-- Unit --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">الوحدة</label>
                                <input type="text" 
                                       :name="'items[' + index + '][unit]'"
                                       x-model="item.unit"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm"
                                       placeholder="وحدة">
                            </div>

                            {{-- Specifications --}}
                            <div class="md:col-span-2 lg:col-span-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">المواصفات</label>
                                <textarea :name="'items[' + index + '][specifications]'"
                                          x-model="item.specifications"
                                          rows="2"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent text-sm"
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
        <div class="flex items-center justify-end gap-4">
            <a href="{{ route('buyer.rfqs.index') }}" 
               class="px-6 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                إلغاء
            </a>
            <button type="submit" 
                    class="px-6 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
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
            @if(session('rfq_product'))
            {
                product_id: '{{ session("rfq_product.id") }}',
                item_name: '{{ session("rfq_product.name") }}',
                quantity: 1,
                unit: 'وحدة',
                specifications: ''
            }
            @elseif(old('items'))
            @foreach(old('items') as $item)
            {
                product_id: '{{ $item["product_id"] ?? "" }}',
                item_name: '{{ $item["item_name"] ?? "" }}',
                quantity: {{ $item['quantity'] ?? 1 }},
                unit: '{{ $item["unit"] ?? "وحدة" }}',
                specifications: '{{ $item["specifications"] ?? "" }}'
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

