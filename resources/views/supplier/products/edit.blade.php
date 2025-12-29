{{-- Supplier Products - Edit --}}
<x-dashboard.layout title="تعديل منتج" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل منتج</h1>
                <p class="mt-2 text-medical-gray-600">تعديل معلومات المنتج والعرض: {{ $product->name }}</p>
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

    {{-- Edit Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('supplier.products.update', $product->id) }}"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Product Information Section --}}
            <div class="mb-8">
                <h2
                    class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200 font-display">
                    معلومات المنتج
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Product Name --}}
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            اسم المنتج <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}"
                            required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Model --}}
                    <div>
                        <label for="model" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الموديل
                        </label>
                        <input type="text" id="model" name="model" value="{{ old('model', $product->model) }}"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('model') border-red-500 @enderror">
                        @error('model')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Brand --}}
                    <div>
                        <label for="brand" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            العلامة التجارية
                        </label>
                        <input type="text" id="brand" name="brand" value="{{ old('brand', $product->brand) }}"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('brand') border-red-500 @enderror">
                        @error('brand')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Category --}}
                    <div>
                        <label for="category_id" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الفئة
                        </label>
                        <select id="category_id" name="category_id"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('category_id') border-red-500 @enderror">
                            <option value="">-- اختر الفئة --</option>
                            @foreach ($categories as $id => $name)
                                <option value="{{ $id }}"
                                    {{ old('category_id', $product->category_id) == $id ? 'selected' : '' }}>
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
                        <label for="manufacturer_id" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الشركة المصنعة
                        </label>
                        <select id="manufacturer_id" name="manufacturer_id"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('manufacturer_id') border-red-500 @enderror">
                            <option value="">-- اختر الشركة المصنعة --</option>
                            @foreach ($manufacturers as $manufacturer)
                                <option value="{{ $manufacturer->id }}"
                                    {{ old('manufacturer_id', $product->manufacturer_id) == $manufacturer->id ? 'selected' : '' }}>
                                    {{ $manufacturer->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('manufacturer_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea id="description" name="description" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Specifications --}}
                    <div class="md:col-span-2">
                        <label for="specifications" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المواصفات
                        </label>
                        <textarea id="specifications" name="specifications" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('specifications') border-red-500 @enderror">{{ old('specifications', is_array($product->specifications) ? implode("\n", $product->specifications) : $product->specifications) }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل مواصفة في سطر منفصل</p>
                        @error('specifications')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Features --}}
                    <div class="md:col-span-2">
                        <label for="features" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المميزات
                        </label>
                        <textarea id="features" name="features" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('features') border-red-500 @enderror">{{ old('features', is_array($product->features) ? implode("\n", $product->features) : $product->features) }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">أدخل كل ميزة في سطر منفصل</p>
                        @error('features')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Technical Data --}}
                    <div class="md:col-span-2">
                        <label for="technical_data" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            البيانات التقنية
                        </label>
                        <textarea id="technical_data" name="technical_data" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('technical_data') border-red-500 @enderror">{{ old('technical_data', is_array($product->technical_data) ? implode("\n", $product->technical_data) : $product->technical_data) }}</textarea>
                        @error('technical_data')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Certifications --}}
                    <div class="md:col-span-2">
                        <label for="certifications" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الشهادات
                        </label>
                        <textarea id="certifications" name="certifications" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('certifications') border-red-500 @enderror">{{ old('certifications', is_array($product->certifications) ? implode("\n", $product->certifications) : $product->certifications) }}</textarea>
                        @error('certifications')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Installation Requirements --}}
                    <div class="md:col-span-2">
                        <label for="installation_requirements"
                            class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            متطلبات التثبيت
                        </label>
                        <textarea id="installation_requirements" name="installation_requirements" rows="3"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('installation_requirements') border-red-500 @enderror">{{ old('installation_requirements', $product->installation_requirements) }}</textarea>
                        @error('installation_requirements')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Product Images --}}
                    <div class="md:col-span-2">
                        <label for="images" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            تحديث صور المنتج (اختياري)
                        </label>
                        <input type="file" id="images" name="images[]" multiple accept="image/*"
                            class="block w-full text-sm text-medical-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-medical-blue-50 file:text-medical-blue-700 hover:file:bg-medical-blue-100 cursor-pointer @error('images') border border-red-500 rounded-xl @enderror">
                        <p class="mt-1 text-xs text-medical-gray-500">سيتم استبدال الصور الحالية إذا قمت برفع صور
                            جديدة</p>
                        @error('images')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Offer Information Section --}}
            <div class="mb-8">
                <h2
                    class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200 font-display">
                    معلومات العرض
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
                            <option value="available"
                                {{ old('status', $pivot->status) == 'available' ? 'selected' : '' }}>متوفر
                            </option>
                            <option value="out_of_stock"
                                {{ old('status', $pivot->status) == 'out_of_stock' ? 'selected' : '' }}>نفد من
                                المخزون</option>
                            <option value="suspended"
                                {{ old('status', $pivot->status) == 'suspended' ? 'selected' : '' }}>معلق
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

</x-dashboard.layout>
