{{-- Supplier Products - Edit Offer --}}
<x-dashboard.layout title="تعديل العرض" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل عرضك</h1>
                <p class="mt-2 text-medical-gray-600">تعديل السعر والكمية ومعلومات العرض للمنتج</p>
            </div>
            <a href="{{ route('supplier.products.index') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Show all validation errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <ul class="list-disc pr-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Form --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-medical p-8">
                <form method="POST" action="{{ route('supplier.products.update', $product->id) }}">
                    @csrf
                    @method('PUT')

                    {{-- Offer Information Section --}}
                    <div class="mb-8">
                        <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200 font-display">
                            معلومات عرضك
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Price --}}
                            <div>
                                <label for="price" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                    السعر (د.ل) <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="price" name="price"
                                    value="{{ old('price', $pivot->price) }}" step="0.01" min="0" required
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
                                    value="{{ old('stock_quantity', $pivot->stock_quantity) }}" min="0" required
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
                                    value="{{ old('lead_time', $pivot->lead_time) }}" placeholder="مثال: 3 أيام - أسبوع"
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
                                    value="{{ old('warranty', $pivot->warranty) }}" placeholder="مثال: سنة واحدة"
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
                                    <option value="available" {{ old('status', $pivot->status) == 'available' ? 'selected' : '' }}>
                                        متوفر
                                    </option>
                                    <option value="out_of_stock" {{ old('status', $pivot->status) == 'out_of_stock' ? 'selected' : '' }}>
                                        نفد من المخزون
                                    </option>
                                    <option value="suspended" {{ old('status', $pivot->status) == 'suspended' ? 'selected' : '' }}>
                                        معلق
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
                                    class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('notes') border-red-500 @enderror">{{ old('notes', $pivot->notes) }}</textarea>
                                @error('notes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
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

        {{-- Product Info Sidebar (Read Only) --}}
        <div class="space-y-6">
            {{-- Product Card --}}
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                {{-- Product Image --}}
                @if($product->hasMedia('product_images'))
                    <img src="{{ $product->getFirstMediaUrl('product_images', 'thumb') }}" 
                         alt="{{ $product->name }}"
                         class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-medical-gray-100 flex items-center justify-center">
                        <svg class="w-16 h-16 text-medical-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                @endif
                
                <div class="p-6">
                    <h3 class="text-lg font-bold text-medical-gray-900 mb-2">{{ $product->name }}</h3>
                    
                    @if($product->brand || $product->model)
                        <p class="text-sm text-medical-gray-500 mb-4">
                            {{ $product->brand }}{{ $product->brand && $product->model ? ' - ' : '' }}{{ $product->model }}
                        </p>
                    @endif

                    {{-- SKU Badge --}}
                    @if($product->sku)
                        <div class="mb-4">
                            <span class="px-3 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">
                                SKU: {{ $product->sku }}
                            </span>
                        </div>
                    @endif

                    {{-- Category --}}
                    @if($product->category)
                        <div class="flex items-center gap-2 text-sm text-medical-gray-600 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            {{ $product->category->name }}
                        </div>
                    @endif

                    {{-- Manufacturer --}}
                    @if($product->manufacturer)
                        <div class="flex items-center gap-2 text-sm text-medical-gray-600 mb-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            {{ $product->manufacturer->name }}
                        </div>
                    @endif

                    {{-- Compliance Badges --}}
                    @if($product->ce_marked || $product->fda_cleared || $product->medical_class)
                        <div class="flex flex-wrap gap-2 mt-4">
                            @if($product->ce_marked)
                                <span class="px-2 py-1 text-xs bg-green-100 text-green-700 rounded">CE</span>
                            @endif
                            @if($product->fda_cleared)
                                <span class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded">FDA</span>
                            @endif
                            @if($product->medical_class)
                                <span class="px-2 py-1 text-xs bg-purple-100 text-purple-700 rounded">
                                    Class {{ $product->medical_class }}
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- Info Card --}}
            <div class="bg-medical-blue-50 rounded-2xl p-6 border border-medical-blue-200">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-medical-blue-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <h4 class="font-bold text-medical-blue-900">نظام الكتالوج الموحد</h4>
                        <p class="text-sm text-medical-blue-700 mt-1">
                            بيانات المنتج (الاسم، المواصفات، الشهادات) تتم إدارتها من قبل مديري النظام لضمان جودة واتساق الكتالوج.
                        </p>
                        <p class="text-sm text-medical-blue-700 mt-2">
                            يمكنك تعديل <strong>عرضك فقط</strong> (السعر، الكمية، مدة التوصيل، الضمان).
                        </p>
                    </div>
                </div>
            </div>

            {{-- Specifications (Read Only) --}}
            @if($product->specifications)
                <div class="bg-white rounded-2xl shadow-medical p-6">
                    <h4 class="font-bold text-medical-gray-900 mb-3">المواصفات</h4>
                    <ul class="space-y-1 text-sm text-medical-gray-600">
                        @foreach($product->specifications as $spec)
                            <li class="flex items-start gap-2">
                                <svg class="w-4 h-4 text-medical-green-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $spec }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>
