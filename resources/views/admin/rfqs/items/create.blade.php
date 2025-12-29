{{-- Admin RFQ Item - Create --}}
<x-dashboard.layout title="إضافة عنصر جديد" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

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
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة عنصر جديد</h1>
                <p class="mt-1 text-medical-gray-600">طلب: {{ $rfq->title }}</p>
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

    @if ($errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-6">
        <form action="{{ route('admin.rfqs.items.store', $rfq) }}" method="POST">
            @csrf

            <div class="space-y-6">
                {{-- Product Selection (Optional) --}}
                <div>
                    <label for="product_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                        المنتج (اختياري)
                    </label>
                    <select name="product_id" id="product_id"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        <option value="">-- اختر منتج من الكتالوج --</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}" {{ old('product_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-sm text-medical-gray-500">يمكنك اختيار منتج من الكتالوج أو إدخال اسم المنتج يدوياً</p>
                </div>

                {{-- Item Name --}}
                <div>
                    <label for="item_name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                        اسم المنتج <span class="text-medical-red-500">*</span>
                    </label>
                    <input type="text" name="item_name" id="item_name" value="{{ old('item_name') }}"
                        required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500"
                        placeholder="مثال: جهاز أشعة سينية محمول">
                    @error('item_name')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Quantity --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الكمية <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 1) }}"
                            required min="1" max="999999"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        @error('quantity')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الوحدة
                        </label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', 'وحدة') }}"
                            maxlength="50"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500"
                            placeholder="مثال: قطعة، كرتونة، لتر">
                        @error('unit')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Specifications --}}
                <div>
                    <label for="specifications" class="block text-sm font-medium text-medical-gray-700 mb-2">
                        المواصفات والتفاصيل
                    </label>
                    <textarea name="specifications" id="specifications" rows="5"
                        maxlength="5000"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500"
                        placeholder="أدخل المواصفات الفنية والتفاصيل المطلوبة...">{{ old('specifications') }}</textarea>
                    <p class="mt-1 text-sm text-medical-gray-500">يمكنك إدخال المواصفات الفنية، الموديل، أو أي تفاصيل إضافية</p>
                    @error('specifications')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-8 flex items-center justify-end gap-4">
                <a href="{{ route('admin.rfqs.show', $rfq) }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-lg hover:bg-medical-gray-200 transition-colors font-medium">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-medical-green-600 text-white rounded-lg hover:bg-medical-green-700 transition-colors font-medium flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    إضافة العنصر
                </button>
            </div>
        </form>
    </div>

    {{-- JavaScript for Product Selection --}}
    <script>
        document.getElementById('product_id').addEventListener('change', function() {
            const productId = this.value;
            if (productId) {
                // Optionally fetch product details and auto-fill item_name
                // This would require an AJAX call to get product name
            }
        });
    </script>

</x-dashboard.layout>

