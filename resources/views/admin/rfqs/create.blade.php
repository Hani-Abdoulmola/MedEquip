{{-- Admin RFQs - Create New RFQ --}}
<x-dashboard.layout title="إنشاء طلب عرض سعر جديد" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إنشاء طلب عرض سعر جديد</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء طلب عرض سعر جديد في النظام</p>
            </div>
            <a href="{{ route('admin.rfqs.index') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if ($errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Create RFQ Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.rfqs.store') }}">
            @csrf

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    المعلومات الأساسية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Buyer Selection --}}
                    <div class="md:col-span-2">
                        <label for="buyer_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المشتري <span class="text-red-500">*</span>
                        </label>
                        <select id="buyer_id" name="buyer_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('buyer_id') border-red-500 @enderror">
                            <option value="">اختر المشتري</option>
                            @foreach($buyers as $id => $name)
                                <option value="{{ $id }}" {{ old('buyer_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('buyer_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Title --}}
                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            عنوان الطلب <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" required
                            placeholder="مثال: طلب أجهزة تعقيم طبية"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('title') border-red-500 @enderror">
                        @error('title')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الوصف (اختياري)
                        </label>
                        <textarea id="description" name="description" rows="5"
                            placeholder="أدخل تفاصيل الطلب والمواصفات المطلوبة..."
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">الحد الأقصى: 5000 حرف</p>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Deadline --}}
                    <div>
                        <label for="deadline" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الموعد النهائي لتقديم العروض (اختياري)
                        </label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline') }}"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('deadline') border-red-500 @enderror">
                        @error('deadline')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div>
                        <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الحالة <span class="text-red-500">*</span>
                        </label>
                        <select id="status" name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('status') border-red-500 @enderror">
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="open" {{ old('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="under_review" {{ old('status') == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="closed" {{ old('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Visibility Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    إعدادات الرؤية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Is Public --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-4">
                            رؤية الطلب <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center space-x-3 space-x-reverse cursor-pointer p-4 border border-medical-gray-300 rounded-xl hover:bg-medical-gray-50 transition-colors">
                                <input type="radio" name="is_public" value="1" {{ old('is_public', '1') == '1' ? 'checked' : '' }}
                                    class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 focus:ring-2 focus:ring-medical-blue-500">
                                <div class="flex-1">
                                    <span class="font-medium text-medical-gray-900">عام - مرئي لجميع الموردين</span>
                                    <p class="text-xs text-medical-gray-500 mt-1">يمكن لجميع الموردين الموثقين رؤية هذا الطلب وتقديم عروض</p>
                                </div>
                                <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064" />
                                </svg>
                            </label>

                            <label class="flex items-center space-x-3 space-x-reverse cursor-pointer p-4 border border-medical-gray-300 rounded-xl hover:bg-medical-gray-50 transition-colors">
                                <input type="radio" name="is_public" value="0" {{ old('is_public') == '0' ? 'checked' : '' }}
                                    class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 focus:ring-2 focus:ring-medical-blue-500">
                                <div class="flex-1">
                                    <span class="font-medium text-medical-gray-900">خاص - للموردين المعينين فقط</span>
                                    <p class="text-xs text-medical-gray-500 mt-1">يمكن فقط للموردين المعينين من قبل الإدارة رؤية هذا الطلب</p>
                                </div>
                                <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </label>
                        </div>
                        @error('is_public')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.rfqs.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl
                    hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إلغاء
                </a>

                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl
                    hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <span class="flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M5 13l4 4L19 7" />
                        </svg>
                        <span>إنشاء طلب عرض السعر</span>
                    </span>
                </button>
            </div>

        </form>
    </div>

</x-dashboard.layout>

