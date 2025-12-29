{{-- Admin Manufacturer Management - Create New Manufacturer --}}
<x-dashboard.layout title="إضافة شركة مصنعة جديدة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة شركة مصنعة جديدة</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء شركة مصنعة جديدة في الكتالوج</p>
            </div>
            <a href="{{ route('admin.manufacturers.index') }}"
                class="inline-flex items-center px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                العودة للقائمة
            </a>
        </div>

        {{-- Error Messages --}}
        @if ($errors->any())
            <div class="mb-6 bg-medical-red-50 border-r-4 border-medical-red-500 p-4 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-medical-red-500 ml-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        <form action="{{ route('admin.manufacturers.store') }}" method="POST" class="bg-white rounded-2xl shadow-medical p-8">
            @csrf

            <div class="space-y-6">
                {{-- Basic Information Section --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        المعلومات الأساسية
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Name (English) --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                اسم الشركة المصنعة (بالإنجليزية) <span class="text-medical-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name') }}" required
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('name') border-medical-red-500 @enderror"
                                placeholder="Manufacturer Name">
                            @error('name')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Name (Arabic) --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                اسم الشركة المصنعة (بالعربية)
                            </label>
                            <input type="text" name="name_ar" value="{{ old('name_ar') }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('name_ar') border-medical-red-500 @enderror"
                                placeholder="اسم الشركة المصنعة">
                            @error('name_ar')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="mt-6">
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الرابط المخصص (Slug)
                        </label>
                        <input type="text" name="slug" value="{{ old('slug') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('slug') border-medical-red-500 @enderror"
                            placeholder="manufacturer-name (سيتم إنشاؤه تلقائياً إذا تركت فارغاً)">
                        <p class="mt-1 text-xs text-medical-gray-500">اتركه فارغاً ليتم إنشاؤه تلقائياً من الاسم</p>
                        @error('slug')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Category & Location Section --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        الفئة والموقع
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Category --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الفئة (اختياري)
                            </label>
                            <select name="category_id"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('category_id') border-medical-red-500 @enderror">
                                <option value="">-- اختر الفئة --</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Country --}}
                        <div>
                            <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الدولة
                            </label>
                            <input type="text" name="country" value="{{ old('country') }}"
                                class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('country') border-medical-red-500 @enderror"
                                placeholder="ليبيا">
                            @error('country')
                                <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Website Section --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                        </svg>
                        الموقع الإلكتروني
                    </h3>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            رابط الموقع الإلكتروني
                        </label>
                        <input type="url" name="website" value="{{ old('website') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('website') border-medical-red-500 @enderror"
                            placeholder="https://www.example.com">
                        @error('website')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Settings Section --}}
                <div class="pb-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 text-medical-blue-600 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        الإعدادات
                    </h3>

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
                                <span class="mr-2 text-sm text-medical-gray-700">شركة نشطة</span>
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-medical-gray-500">الشركات النشطة فقط تظهر في القوائم</p>
                    </div>
                </div>

            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-medical-gray-200 mt-6">
                <a href="{{ route('admin.manufacturers.index') }}"
                    class="px-6 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-lg shadow-medical-blue-200">
                    <span class="flex items-center">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        حفظ الشركة المصنعة
                    </span>
                </button>
            </div>

        </form>

    </div>

</x-dashboard.layout>

