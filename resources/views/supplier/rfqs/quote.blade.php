{{-- Supplier RFQs - Create Quotation --}}
<x-dashboard.layout title="تقديم عرض سعر" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تقديم عرض سعر</h1>
                <p class="mt-2 text-medical-gray-600">{{ $rfq->title }} - {{ $rfq->reference_code }}</p>
            </div>
            <a href="{{ route('supplier.rfqs.show', $rfq) }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للطلب</span>
            </a>
        </div>
    </div>

    {{-- Show validation errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <ul class="list-disc pr-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('supplier.rfqs.quote.store', $rfq) }}" enctype="multipart/form-data" x-data="quotationForm()">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Form --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Items Pricing --}}
                @if ($rfq->items->isNotEmpty())
                    <div class="bg-white rounded-2xl shadow-medical p-8">
                        <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                            تسعير المنتجات المطلوبة
                        </h2>

                        <div class="space-y-6">
                            @foreach ($rfq->items as $index => $item)
                                <div class="p-6 bg-medical-gray-50 rounded-xl border border-medical-gray-200">
                                    <input type="hidden" name="items[{{ $index }}][rfq_item_id]" value="{{ $item->id }}">
                                    
                                    <div class="flex items-start justify-between mb-4">
                                        <div>
                                            <h3 class="font-bold text-medical-gray-900">{{ $item->display_name }}</h3>
                                            @if($item->specifications)
                                                <p class="text-sm text-medical-gray-600 mt-1">{{ $item->specifications }}</p>
                                            @endif
                                        </div>
                                        <span class="px-4 py-2 bg-medical-blue-100 text-medical-blue-700 rounded-full text-sm font-medium">
                                            {{ $item->quantity }} {{ $item->unit ?? 'وحدة' }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        {{-- Unit Price --}}
                                        <div>
                                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">
                                                سعر الوحدة (د.ل) <span class="text-red-500">*</span>
                                            </label>
                                            <input type="number" 
                                                name="items[{{ $index }}][unit_price]" 
                                                x-model.number="items[{{ $index }}].unit_price"
                                                @input="calculateItemTotal({{ $index }}, {{ $item->quantity }})"
                                                value="{{ old('items.'.$index.'.unit_price') }}"
                                                step="0.01" min="0" required
                                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                                        </div>

                                        {{-- Lead Time --}}
                                        <div>
                                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">
                                                مدة التوصيل
                                            </label>
                                            <input type="text" 
                                                name="items[{{ $index }}][lead_time]" 
                                                value="{{ old('items.'.$index.'.lead_time') }}"
                                                placeholder="مثال: 5-7 أيام"
                                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                                        </div>

                                        {{-- Warranty --}}
                                        <div>
                                            <label class="block text-sm font-medium text-medical-gray-700 mb-2">
                                                الضمان
                                            </label>
                                            <input type="text" 
                                                name="items[{{ $index }}][warranty]" 
                                                value="{{ old('items.'.$index.'.warranty') }}"
                                                placeholder="مثال: سنة واحدة"
                                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                                        </div>
                                    </div>

                                    {{-- Item Total --}}
                                    <div class="mt-4 pt-4 border-t border-medical-gray-200 flex items-center justify-between">
                                        <span class="text-medical-gray-600">إجمالي العنصر:</span>
                                        <span class="text-xl font-bold text-medical-blue-600" x-text="formatCurrency(items[{{ $index }}].total || 0)">0.00 د.ل</span>
                                    </div>

                                    {{-- Item Notes --}}
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">
                                            ملاحظات على هذا العنصر (اختياري)
                                        </label>
                                        <input type="text" 
                                            name="items[{{ $index }}][notes]" 
                                            value="{{ old('items.'.$index.'.notes') }}"
                                            placeholder="أي ملاحظات إضافية..."
                                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- General Quote Details --}}
                <div class="bg-white rounded-2xl shadow-medical p-8">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                        تفاصيل العرض العامة
                    </h2>

                    <div class="space-y-6">
                        {{-- Valid Until --}}
                        <div>
                            <label for="valid_until" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                صلاحية العرض حتى <span class="text-red-500">*</span>
                            </label>
                            <input type="date" id="valid_until" name="valid_until" value="{{ old('valid_until') }}"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">
                            <p class="mt-1 text-xs text-medical-gray-500">حدد تاريخ انتهاء صلاحية هذا العرض</p>
                        </div>

                        {{-- Terms --}}
                        <div>
                            <label for="terms" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الشروط والأحكام
                            </label>
                            <textarea id="terms" name="terms" rows="5"
                                placeholder="أدخل شروط الدفع، التوصيل، الضمان، وأي شروط أخرى..."
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200">{{ old('terms') }}</textarea>
                        </div>

                        {{-- Attachments --}}
                        <div>
                            <label for="attachments" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                المرفقات (اختياري)
                            </label>
                            <input type="file" id="attachments" name="attachments[]" multiple
                                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                class="block w-full text-sm text-medical-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-medical-blue-50 file:text-medical-blue-700 hover:file:bg-medical-blue-100 cursor-pointer">
                            <p class="mt-1 text-xs text-medical-gray-500">PDF, Word, أو صور - الحد الأقصى 10MB لكل ملف</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Quote Summary --}}
                <div class="bg-white rounded-2xl shadow-medical p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        ملخص العرض
                    </h3>

                    <div class="space-y-4">
                        {{-- Items Count --}}
                        <div class="flex items-center justify-between">
                            <span class="text-medical-gray-600">عدد العناصر</span>
                            <span class="font-medium text-medical-gray-900">{{ $rfq->items->count() }}</span>
                        </div>

                        {{-- Total Quantity --}}
                        <div class="flex items-center justify-between">
                            <span class="text-medical-gray-600">إجمالي الكمية</span>
                            <span class="font-medium text-medical-gray-900">{{ $rfq->items->sum('quantity') }}</span>
                        </div>

                        <hr class="border-medical-gray-200">

                        {{-- Grand Total --}}
                        <div class="flex items-center justify-between text-lg">
                            <span class="font-semibold text-medical-gray-900">الإجمالي الكلي</span>
                            <span class="font-bold text-medical-green-600" x-text="formatCurrency(grandTotal)">0.00 د.ل</span>
                        </div>

                        {{-- Hidden Total Price Input --}}
                        <input type="hidden" name="total_price" x-model="grandTotal">
                    </div>

                    {{-- Submit Button --}}
                    <div class="mt-6 pt-4 border-t border-medical-gray-200">
                        <button type="submit"
                            class="w-full px-6 py-4 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold text-lg shadow-sm">
                            تقديم العرض
                        </button>
                        <a href="{{ route('supplier.rfqs.show', $rfq) }}"
                            class="mt-3 w-full inline-flex justify-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                            إلغاء
                        </a>
                    </div>
                </div>

                {{-- RFQ Info --}}
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                        معلومات الطلب
                    </h3>

                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-medical-gray-600">المشتري</p>
                            <p class="font-semibold text-medical-gray-900 mt-1">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                        </div>

                        @if ($rfq->deadline)
                            <div>
                                <p class="text-sm text-medical-gray-600">الموعد النهائي</p>
                                <p class="font-semibold text-medical-gray-900 mt-1">{{ $rfq->deadline->format('Y-m-d') }}</p>
                                <p class="text-xs {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-medical-gray-500' }}">
                                    {{ $rfq->deadline->diffForHumans() }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tips --}}
                <div class="bg-medical-blue-50 rounded-2xl p-6">
                    <h3 class="text-lg font-semibold text-medical-blue-900 mb-3">نصائح</h3>
                    <ul class="space-y-2 text-sm text-medical-blue-700">
                        <li class="flex items-start">
                            <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>سعّر كل عنصر على حدة للشفافية</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>حدد مدة التوصيل والضمان لكل منتج</span>
                        </li>
                        <li class="flex items-start">
                            <svg class="w-5 h-5 ml-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>أرفق المستندات الداعمة لعرضك</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        function quotationForm() {
            return {
                items: {
                    @foreach ($rfq->items as $index => $item)
                    {{ $index }}: {
                        unit_price: {{ old('items.'.$index.'.unit_price', 0) }},
                        quantity: {{ $item->quantity }},
                        total: 0
                    },
                    @endforeach
                },
                grandTotal: 0,

                init() {
                    // Calculate initial totals
                    Object.keys(this.items).forEach(key => {
                        this.calculateItemTotal(parseInt(key), this.items[key].quantity);
                    });
                },

                calculateItemTotal(index, quantity) {
                    const item = this.items[index];
                    if (item) {
                        item.total = (item.unit_price || 0) * quantity;
                        this.calculateGrandTotal();
                    }
                },

                calculateGrandTotal() {
                    this.grandTotal = Object.values(this.items).reduce((sum, item) => sum + (item.total || 0), 0);
                },

                formatCurrency(value) {
                    return new Intl.NumberFormat('ar-LY', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(value) + ' د.ل';
                }
            }
        }
    </script>
    @endpush

</x-dashboard.layout>
