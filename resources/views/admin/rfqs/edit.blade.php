{{-- Admin RFQs - Edit RFQ --}}
<x-dashboard.layout title="تعديل طلب عرض السعر" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل طلب عرض السعر</h1>
                <p class="mt-2 text-medical-gray-600">تعديل معلومات طلب عرض السعر: {{ $rfq->reference_code }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.rfqs.show', $rfq) }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>عرض التفاصيل</span>
                </a>
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

    {{-- Edit RFQ Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.rfqs.update', $rfq) }}">
            @csrf
            @method('PUT')

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    المعلومات الأساسية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Reference Code (Read-only) --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الرمز المرجعي
                        </label>
                        <input type="text" value="{{ $rfq->reference_code }}" readonly
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl bg-medical-gray-50 text-medical-gray-500 font-mono">
                        <p class="mt-1 text-xs text-medical-gray-500">لا يمكن تعديل الرمز المرجعي</p>
                    </div>

                    {{-- Buyer Selection --}}
                    <div>
                        <label for="buyer_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المشتري <span class="text-red-500">*</span>
                        </label>
                        <select id="buyer_id" name="buyer_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('buyer_id') border-red-500 @enderror">
                            <option value="">اختر المشتري</option>
                            @foreach($buyers as $id => $name)
                                <option value="{{ $id }}" {{ old('buyer_id', $rfq->buyer_id) == $id ? 'selected' : '' }}>
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
                        <input type="text" id="title" name="title" value="{{ old('title', $rfq->title) }}" required
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
                            @error('description') border-red-500 @enderror">{{ old('description', $rfq->description) }}</textarea>
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
                        <input type="datetime-local" id="deadline" name="deadline"
                            value="{{ old('deadline', $rfq->deadline ? $rfq->deadline->format('Y-m-d\TH:i') : '') }}"
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
                            <option value="draft" {{ old('status', $rfq->status) == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="open" {{ old('status', $rfq->status) == 'open' ? 'selected' : '' }}>مفتوح</option>
                            <option value="under_review" {{ old('status', $rfq->status) == 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="closed" {{ old('status', $rfq->status) == 'closed' ? 'selected' : '' }}>مغلق</option>
                            <option value="cancelled" {{ old('status', $rfq->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                                <input type="radio" name="is_public" value="1" {{ old('is_public', $rfq->is_public ? '1' : '0') == '1' ? 'checked' : '' }}
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
                                <input type="radio" name="is_public" value="0" {{ old('is_public', $rfq->is_public ? '1' : '0') == '0' ? 'checked' : '' }}
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

            {{-- Additional Info --}}
            <div class="mb-8 bg-medical-blue-50 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات إضافية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-medical-gray-600">تاريخ الإنشاء:</span>
                        <span class="font-medium text-medical-gray-900 mr-2">{{ $rfq->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($rfq->updated_at)
                        <div>
                            <span class="text-medical-gray-600">آخر تحديث:</span>
                            <span class="font-medium text-medical-gray-900 mr-2">{{ $rfq->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                    @if($rfq->closed_at)
                        <div>
                            <span class="text-medical-gray-600">تاريخ الإغلاق:</span>
                            <span class="font-medium text-medical-gray-900 mr-2">{{ $rfq->closed_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                    <div>
                        <span class="text-medical-gray-600">عدد العروض المستلمة:</span>
                        <span class="font-medium text-medical-gray-900 mr-2">{{ $rfq->quotations->count() }}</span>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.rfqs.show', $rfq) }}"
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
                        <span>حفظ التعديلات</span>
                    </span>
                </button>
            </div>

        </form>
    </div>

</x-dashboard.layout>

