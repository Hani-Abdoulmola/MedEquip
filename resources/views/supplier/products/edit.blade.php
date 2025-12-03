{{-- Supplier Products - Edit --}}
<x-dashboard.layout title="تعديل منتج" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    <div class="max-w-4xl mx-auto px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل منتج</h1>
                    <p class="mt-2 text-medical-gray-600">تعديل معلومات العرض: {{ $product->name }}</p>
                </div>
                <a href="{{ route('supplier.products.index') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
        </div>

        {{-- Product Info Card --}}
        <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
            <div class="flex items-center space-x-4 space-x-reverse">
                @if ($product->hasMedia('product_images'))
                    <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}" alt="{{ $product->name }}"
                        class="w-20 h-20 object-cover rounded-xl">
                @else
                    <div class="w-20 h-20 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-10 h-10 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                @endif
                <div>
                    <h3 class="text-xl font-bold text-medical-gray-900">{{ $product->name }}</h3>
                    @if ($product->model)
                        <p class="text-sm text-medical-gray-600">موديل: {{ $product->model }}</p>
                    @endif
                    @if ($product->category)
                        <span
                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700 mt-2">
                            {{ $product->category->name }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="bg-white rounded-2xl shadow-medical p-8">
            <form method="POST" action="{{ route('supplier.products.update', $product->id) }}">
                @csrf
                @method('PUT')

                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200 font-display">
                    معلومات العرض</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            السعر (د.ل) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price"
                            value="{{ old('price', $pivotData->price) }}" step="0.01" min="0" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stock Quantity --}}
                    <div>
                        <label for="stock_quantity" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الكمية المتوفرة <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity"
                            value="{{ old('stock_quantity', $pivotData->stock_quantity) }}" min="0" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('stock_quantity') border-red-500 @enderror">
                        @error('stock_quantity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lead Time --}}
                    <div>
                        <label for="lead_time" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            مدة التوصيل
                        </label>
                        <input type="text" id="lead_time" name="lead_time"
                            value="{{ old('lead_time', $pivotData->lead_time) }}"
                            placeholder="مثال: 3 أيام - أسبوع"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('lead_time') border-red-500 @enderror">
                        @error('lead_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Warranty --}}
                    <div>
                        <label for="warranty" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الضمان
                        </label>
                        <input type="text" id="warranty" name="warranty"
                            value="{{ old('warranty', $pivotData->warranty) }}" placeholder="مثال: سنة واحدة"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('warranty') border-red-500 @enderror">
                        @error('warranty')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-red-500 @enderror">
                            <option value="">اختر الحالة</option>
                            <option value="available"
                                {{ old('status', $pivotData->status) == 'available' ? 'selected' : '' }}>متوفر
                            </option>
                            <option value="out_of_stock"
                                {{ old('status', $pivotData->status) == 'out_of_stock' ? 'selected' : '' }}>نفد من
                                المخزون</option>
                            <option value="suspended"
                                {{ old('status', $pivotData->status) == 'suspended' ? 'selected' : '' }}>معلق
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('notes') border-red-500 @enderror">{{ old('notes', $pivotData->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-medical-gray-200">
                    <a href="{{ route('supplier.products.index') }}"
                        class="px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-dashboard.layout>

