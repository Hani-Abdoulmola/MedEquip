{{-- Supplier Products - Create --}}
<x-dashboard.layout title="إضافة منتج جديد" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة منتج جديد</h1>
                <p class="mt-2 text-medical-gray-600">أنشئ منتجاً جديداً أو اربط منتجاً موجوداً</p>
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

    {{-- Create Product Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8" x-data="{ action: '{{ old('action', 'new') }}' }">
        <form method="POST" action="{{ route('supplier.products.store') }}" enctype="multipart/form-data">
            @csrf

            {{-- Action Selection Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    نوع العملية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- New Product Option --}}
                    <label
                        class="p-4 border-2 rounded-xl cursor-pointer flex items-center gap-3 transition-all duration-200"
                        :class="action === 'new' ? 'border-medical-blue-500 bg-medical-blue-50' :
                            'border-medical-gray-300 hover:border-medical-gray-400'">
                        <input type="radio" name="action" value="new" x-model="action"
                            class="w-5 h-5 text-medical-blue-600">
                        <div>
                            <p class="font-bold text-medical-gray-900">إنشاء منتج جديد</p>
                            <p class="text-sm text-medical-gray-600">إنشاء منتج جديد وإضافته لقائمتك</p>
                        </div>
                    </label>

                    {{-- Existing Product Option --}}
                    <label
                        class="p-4 border-2 rounded-xl cursor-pointer flex items-center gap-3 transition-all duration-200"
                        :class="action === 'existing' ? 'border-medical-blue-500 bg-medical-blue-50' :
                            'border-medical-gray-300 hover:border-medical-gray-400'">
                        <input type="radio" name="action" value="existing" x-model="action"
                            class="w-5 h-5 text-medical-blue-600">
                        <div>
                            <p class="font-bold text-medical-gray-900">ربط منتج موجود</p>
                            <p class="text-sm text-medical-gray-600">ربط منتج جاهز في النظام</p>
                        </div>
                    </label>
                </div>
            </div>

            {{-- New Product Fields Section --}}
            <div x-show="action === 'new'" x-transition class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    معلومات المنتج
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            اسم المنتج <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror">
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
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('model') border-red-500 @enderror">
                        @error('model')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            العلامة التجارية
                        </label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('brand') border-red-500 @enderror">
                        @error('brand')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الفئة
                        </label>
                        <select id="category_id" name="category_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('category_id') border-red-500 @enderror">
                            <option value="">اختر الفئة</option>
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

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Specifications --}}
                    <div class="md:col-span-2">
                        <label for="specifications" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المواصفات
                        </label>
                        <textarea id="specifications" name="specifications[]" rows="3" placeholder="أدخل المواصفات كل واحدة في سطر"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('specifications') border-red-500 @enderror">{{ old('specifications.0') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل مواصفة في سطر منفصل</p>
                        @error('specifications')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Features --}}
                    <div class="md:col-span-2">
                        <label for="features" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المميزات
                        </label>
                        <textarea id="features" name="features[]" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('features') border-red-500 @enderror">{{ old('features.0') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل ميزة في سطر منفصل</p>
                        @error('features')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Technical Data --}}
                    <div class="md:col-span-2">
                        <label for="technical_data" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            البيانات التقنية
                        </label>
                        <textarea id="technical_data" name="technical_data[]" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('technical_data') border-red-500 @enderror">{{ old('technical_data.0') }}</textarea>
                        @error('technical_data')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Certifications --}}
                    <div class="md:col-span-2">
                        <label for="certifications" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الشهادات
                        </label>
                        <textarea id="certifications" name="certifications[]" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('certifications') border-red-500 @enderror">{{ old('certifications.0') }}</textarea>
                        @error('certifications')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Installation Requirements --}}
                    <div class="md:col-span-2">
                        <label for="installation_requirements"
                            class="block text-sm font-medium text-medical-gray-700 mb-2">
                            متطلبات التثبيت
                        </label>
                        <textarea id="installation_requirements" name="installation_requirements" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('installation_requirements') border-red-500 @enderror">{{ old('installation_requirements') }}</textarea>
                        @error('installation_requirements')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Images --}}
                    <div class="md:col-span-2">
                        <label for="images" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            صور المنتج (متعددة)
                        </label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                            class="block w-full text-sm text-medical-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-medical-blue-50 file:text-medical-blue-700 hover:file:bg-medical-blue-100 cursor-pointer @error('images') border border-red-500 rounded-xl @enderror">
                        <p class="mt-1 text-xs text-medical-gray-500">الحد الأقصى لكل صورة 5MB</p>
                        @error('images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Existing Product Selection Section --}}
            <div x-show="action === 'existing'" x-transition class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    اختيار منتج موجود
                </h2>

                <div>
                    <label for="product_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                        المنتج <span class="text-red-500">*</span>
                    </label>
                    <select id="product_id" name="product_id" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('product_id') border-red-500 @enderror">
                        <option value="">اختر منتجاً</option>
                        @foreach ($existingProducts as $p)
                            <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->name }}
                                @if ($p->model)
                                    - {{ $p->model }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Offer Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
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
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('price') border-red-500 @enderror">
                        @error('price')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Stock Quantity --}}
                    <div>
                        <label for="stock_quantity" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الكمية <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                            value="{{ old('stock_quantity') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('stock_quantity') border-red-500 @enderror">
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
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('lead_time') border-red-500 @enderror">
                        @error('lead_time')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Warranty --}}
                    <div>
                        <label for="warranty" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الضمان
                        </label>
                        <input type="text" id="warranty" name="warranty" value="{{ old('warranty') }}"
                            placeholder="مثال: سنة واحدة"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('warranty') border-red-500 @enderror">
                        @error('warranty')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-red-500 @enderror">
                            <option value="available"
                                {{ old('status', 'available') == 'available' ? 'selected' : '' }}>
                                متوفر
                            </option>
                            <option value="out_of_stock" {{ old('status') == 'out_of_stock' ? 'selected' : '' }}>نفد
                                من المخزون</option>
                            <option value="suspended" {{ old('status') == 'suspended' ? 'selected' : '' }}>معلق
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label for="notes" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            ملاحظات
                        </label>
                        <textarea id="notes" name="notes" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('notes') border-red-500 @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('supplier.products.index') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    <span>إلغاء</span>
                </a>
                <button type="submit"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    <span>حفظ المنتج</span>
                </button>
            </div>
        </form>
    </div>

</x-dashboard.layout>
