{{-- Admin Products Management - Edit Product --}}
<x-dashboard.layout title="تعديل منتج" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto px-6 py-8">

        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل منتج</h1>
                    <p class="mt-2 text-medical-gray-600">تعديل بيانات المنتج: {{ $product->name }}</p>
                </div>
                <a href="{{ route('admin.products') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-semibold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
        </div>

        {{-- Edit Product Form --}}
        <div class="bg-white rounded-2xl shadow-medical p-8">
            <form method="POST" action="{{ route('admin.products.update', $product->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Basic Information Section --}}
                <div class="mb-8">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200 font-display">
                        المعلومات الأساسية</h2>

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
                                <option value="">اختر الفئة</option>
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

                        {{-- Status --}}
                        <div>
                            <label for="is_active" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الحالة
                            </label>
                            <select id="is_active" name="is_active"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('is_active') border-red-500 @enderror">
                                <option value="1" {{ old('is_active', $product->is_active) == 1 ? 'selected' : '' }}>
                                    نشط</option>
                                <option value="0" {{ old('is_active', $product->is_active) == 0 ? 'selected' : '' }}>
                                    غير نشط</option>
                            </select>
                            @error('is_active')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="md:col-span-2">
                            <label for="description" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الوصف
                            </label>
                            <textarea id="description" name="description" rows="4"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('description') border-red-500 @enderror">{{ old('description', $product->description) }}</textarea>
                            @error('description')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Product Image --}}
                        <div class="md:col-span-2">
                            <label for="image" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                صورة المنتج
                            </label>
                            @if ($product->hasMedia('product_images'))
                                <div class="mb-3">
                                    <img src="{{ $product->getFirstMediaUrl('product_images') }}"
                                        alt="{{ $product->name }}" class="w-32 h-32 object-cover rounded-xl">
                                </div>
                            @endif
                            <input type="file" id="image" name="image" accept="image/*"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('image') border-red-500 @enderror">
                            <p class="mt-2 text-sm text-medical-gray-600">الصيغ المدعومة: JPG, PNG, WEBP (الحد الأقصى: 2MB)
                            </p>
                            @error('image')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-4 pt-6 border-t border-medical-gray-200">
                    <a href="{{ route('admin.products') }}"
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

