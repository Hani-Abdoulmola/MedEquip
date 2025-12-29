{{-- Admin Category Management - Create New Category --}}
<x-dashboard.layout title="إضافة فئة جديدة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto">

        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة فئة جديدة</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء فئة منتجات جديدة في الكتالوج</p>
            </div>
            <a href="{{ route('admin.categories.index') }}"
                class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 bg-medical-red-50 border-r-4 border-medical-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-medical-red-500 ml-2 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <p class="text-medical-red-700 font-medium mb-1">يرجى تصحيح الأخطاء التالية:</p>
                        <ul class="list-disc list-inside text-medical-red-600 text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.categories.store') }}" method="POST" class="bg-white rounded-2xl shadow-medical p-8">
            @csrf

            <div class="space-y-6">

                {{-- Basic Information Section --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        المعلومات الأساسية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Name (English) --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                اسم الفئة (بالإنجليزية) <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('name') border-medical-red-500 @enderror"
                                placeholder="Medical Equipment">
                            @error('name')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name (Arabic) --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                اسم الفئة (بالعربية)
                            </label>
                            <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('name_ar') border-medical-red-500 @enderror"
                                placeholder="المعدات الطبية">
                            @error('name_ar')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Description --}}
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الوصف
                        </label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('description') border-medical-red-500 @enderror"
                            placeholder="وصف مختصر للفئة...">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Hierarchy Section --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                        </svg>
                        التسلسل الهرمي
                    </h3>

                    {{-- Parent Category --}}
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الفئة الأب (اختياري)
                        </label>
                        <select name="parent_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('parent_id') border-medical-red-500 @enderror">
                            <option value="">-- فئة رئيسية (بدون فئة أب) --</option>
                            @foreach ($parentCategories as $parent)
                                <option value="{{ $parent->id }}"
                                    {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                                @foreach ($parent->children as $child)
                                    <option value="{{ $child->id }}"
                                        {{ old('parent_id') == $child->id ? 'selected' : '' }}>
                                        &nbsp;&nbsp;└─ {{ $child->name }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-medical-gray-500">اترك الحقل فارغاً لإنشاء فئة رئيسية، أو اختر فئة
                            أب لإنشاء فئة فرعية</p>
                        @error('parent_id')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Settings Section --}}
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        الإعدادات
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Sort Order --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                ترتيب العرض
                            </label>
                            <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('sort_order') border-medical-red-500 @enderror">
                            <p class="mt-1 text-xs text-medical-gray-500">الأرقام الأقل تظهر أولاً (افتراضي: 0)</p>
                            @error('sort_order')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Active Status --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الحالة
                            </label>
                            <div class="flex items-center space-x-reverse space-x-4 mt-3">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                                    <span class="mr-2 text-sm text-medical-gray-700">فئة نشطة</span>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-medical-gray-500">الفئات النشطة فقط تظهر للموردين والمشترين</p>
                        </div>

                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-medical-gray-200 mt-6">
                <a href="{{ route('admin.categories.index') }}"
                    class="px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-lg shadow-medical-blue-200">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ الفئة
                    </span>
                </button>
            </div>

        </form>

    </div>

</x-dashboard.layout>

