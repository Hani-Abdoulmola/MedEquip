{{-- Supplier Products - Create --}}
<x-dashboard.layout title="إضافة منتج جديد" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة منتج جديد</h1>
                <p class="mt-2 text-medical-gray-600">أضف منتجاً جديداً أو اربط منتجاً موجوداً من الكتالوج</p>
            </div>
            <a href="{{ route('supplier.products.index') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <h4 class="font-bold mb-1">يرجى تصحيح الأخطاء التالية:</h4>
                    <ul class="list-disc pr-4 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Create Product Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8" x-data="{ action: '{{ old('action', 'existing') }}' }">
        <form method="POST" action="{{ route('supplier.products.store') }}" enctype="multipart/form-data" id="product-form">
            @csrf

            {{-- Action Selection --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    نوع العملية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Existing Product Option --}}
                    <label
                        class="relative p-4 border-2 rounded-xl cursor-pointer flex items-start gap-3 transition-all duration-200"
                        :class="action === 'existing' ? 'border-medical-green-500 bg-medical-green-50' : 'border-medical-gray-300 hover:border-medical-gray-400'">
                        <input type="radio" name="action" value="existing" x-model="action"
                            class="w-5 h-5 text-medical-green-600 mt-1">
                        <div>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-medical-gray-900">ربط منتج موجود</p>
                                <span class="px-2 py-0.5 text-xs bg-medical-green-100 text-medical-green-700 rounded-full">موصى به</span>
                            </div>
                            <p class="text-sm text-medical-gray-600 mt-1">اختر من المنتجات المعتمدة وأضف عرضك</p>
                        </div>
                    </label>

                    {{-- New Product Option --}}
                    <label
                        class="p-4 border-2 rounded-xl cursor-pointer flex items-start gap-3 transition-all duration-200"
                        :class="action === 'new' ? 'border-medical-blue-500 bg-medical-blue-50' : 'border-medical-gray-300 hover:border-medical-gray-400'">
                        <input type="radio" name="action" value="new" x-model="action"
                            class="w-5 h-5 text-medical-blue-600 mt-1">
                        <div>
                            <p class="font-bold text-medical-gray-900">إضافة منتج جديد</p>
                            <p class="text-sm text-medical-gray-600 mt-1">لم تجد المنتج؟ أضفه (يخضع لمراجعة الإدارة)</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- NEW PRODUCT FIELDS --}}
            <div x-show="action === 'new'" x-transition class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    بيانات المنتج
                </h2>

                {{-- Review Notice --}}
                <div class="mb-6 p-4 rounded-xl bg-yellow-50 border border-yellow-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-sm text-yellow-800">
                            المنتجات الجديدة تحتاج موافقة الإدارة قبل ظهورها للمشترين.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            اسم المنتج <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Model --}}
                    <div>
                        <label for="model" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الموديل
                        </label>
                        <input type="text" id="model" name="model" value="{{ old('model') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('model') border-red-500 @enderror">
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            العلامة التجارية
                        </label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('brand') border-red-500 @enderror">
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الفئة <span class="text-red-500">*</span>
                        </label>
                        <select id="category_id" name="category_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('category_id') border-red-500 @enderror">
                            <option value="">-- اختر الفئة --</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}" {{ old('category_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Manufacturer --}}
                    <div>
                        <label for="manufacturer_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الشركة المصنعة
                        </label>
                        <select id="manufacturer_id" name="manufacturer_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                            <option value="">-- اختر الشركة المصنعة --</option>
                            @foreach ($manufacturers as $manufacturer)
                                <option value="{{ $manufacturer->id }}" {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                                    {{ $manufacturer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">{{ old('description') }}</textarea>
                    </div>

                    {{-- Specifications --}}
                    <div class="md:col-span-2">
                        <label for="specifications" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المواصفات
                        </label>
                        <textarea id="specifications" name="specifications" rows="3"
                            placeholder="أدخل كل مواصفة في سطر منفصل"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">{{ old('specifications') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل مواصفة في سطر منفصل</p>
                    </div>

                    {{-- Features --}}
                    <div class="md:col-span-2">
                        <label for="features" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المميزات
                        </label>
                        <textarea id="features" name="features" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">{{ old('features') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل ميزة في سطر منفصل</p>
                    </div>

                    {{-- Product Images --}}
                    <div class="md:col-span-2">
                        <label for="images" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            صور المنتج
                        </label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                            class="block w-full text-sm text-medical-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-medical-blue-50 file:text-medical-blue-700 hover:file:bg-medical-blue-100 cursor-pointer">
                        <p class="mt-1 text-xs text-medical-gray-500">الحد الأقصى لكل صورة 5MB (JPG, PNG, WEBP)</p>
                    </div>
                </div>
            </div>

            {{-- EXISTING PRODUCT SELECTION --}}
            <div x-show="action === 'existing'" x-transition class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    اختيار منتج من الكتالوج
                </h2>

                @error('product_id')
                    <p class="mb-4 text-sm text-red-600 bg-red-50 px-4 py-2 rounded-lg">{{ $message }}</p>
                @enderror

                @if($existingProducts->isEmpty())
                    <div class="p-8 text-center bg-medical-gray-50 rounded-xl">
                        <svg class="w-12 h-12 text-medical-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                        <p class="text-medical-gray-500 font-medium">لا توجد منتجات متاحة للربط</p>
                        <p class="text-sm text-medical-gray-400 mt-1">جميع المنتجات مرتبطة بك أو اختر "إضافة منتج جديد"</p>
                    </div>
                @else
                    <div class="border border-medical-gray-200 rounded-xl overflow-hidden">
                        <div class="divide-y divide-medical-gray-100 max-h-96 overflow-y-auto">
                            @foreach($existingProducts as $existingProduct)
                                <label class="p-4 cursor-pointer hover:bg-medical-gray-50 flex items-center gap-4 transition-colors">
                                    <input type="radio" name="product_id" value="{{ $existingProduct->id }}"
                                        {{ old('product_id') == $existingProduct->id ? 'checked' : '' }}
                                        class="w-5 h-5 text-medical-green-600">
                                    
                                    {{-- Product Image --}}
                                    <div class="w-14 h-14 bg-medical-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($existingProduct->getFirstMediaUrl('product_images', 'thumb'))
                                            <img src="{{ $existingProduct->getFirstMediaUrl('product_images', 'thumb') }}" 
                                                class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-medical-gray-300">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    {{-- Product Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            @if($existingProduct->category)
                                                <span class="text-xs text-medical-blue-600 font-medium">{{ $existingProduct->category->name }}</span>
                                            @endif
                                            @if($existingProduct->review_status === 'approved')
                                                <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">معتمد</span>
                                            @else
                                                <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">قيد المراجعة</span>
                                            @endif
                                        </div>
                                        <h4 class="font-semibold text-medical-gray-900 truncate">{{ $existingProduct->name }}</h4>
                                        <p class="text-sm text-medical-gray-500 truncate">
                                            {{ $existingProduct->brand }}
                                            @if($existingProduct->model)
                                                • {{ $existingProduct->model }}
                                            @endif
                                        </p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-medical-gray-500">عرض {{ $existingProducts->count() }} منتج</p>
                @endif
            </div>

            {{-- OFFER INFORMATION --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-4 pb-3 border-b border-medical-gray-200">
                    معلومات العرض
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Price --}}
                    <div>
                        <label for="price" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            السعر (د.ل) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="price" name="price" step="0.01" min="0"
                            value="{{ old('price') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stock Quantity --}}
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الكمية المتوفرة <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                            value="{{ old('stock_quantity') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('stock_quantity') border-red-500 @enderror">
                        @error('stock_quantity')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Lead Time --}}
                    <div>
                        <label for="lead_time" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            مدة التوصيل
                        </label>
                        <input type="text" id="lead_time" name="lead_time" value="{{ old('lead_time') }}"
                            placeholder="مثال: 3-5 أيام عمل"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    </div>

                    {{-- Warranty --}}
                    <div>
                        <label for="warranty" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الضمان
                        </label>
                        <input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}"
                            placeholder="مثال: سنة واحدة"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                            <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>متوفر</option>
                            <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>نفد من المخزون</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق</option>
                        </select>
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea id="notes" name="notes" rows="2"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('supplier.products.index') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>حفظ المنتج</span>
                </button>
            </div>
        </form>
    </div>

</x-dashboard.layout>
