{{-- Admin Quotations - Edit Quotation --}}
<x-dashboard.layout title="تعديل عرض السعر" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل عرض السعر</h1>
                <p class="mt-2 text-medical-gray-600">تعديل معلومات عرض السعر: {{ $quotation->reference_code }}</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.quotations.show', $quotation) }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>عرض التفاصيل</span>
                </a>
                <a href="{{ route('admin.quotations.index') }}"
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

    {{-- Edit Quotation Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.quotations.update', $quotation) }}">
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
                        <input type="text" value="{{ $quotation->reference_code }}" readonly
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl bg-medical-gray-50 text-medical-gray-500 font-mono">
                        <p class="mt-1 text-xs text-medical-gray-500">لا يمكن تعديل الرمز المرجعي</p>
                    </div>

                    {{-- RFQ Selection --}}
                    <div>
                        <label for="rfq_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            طلب عرض السعر (RFQ) <span class="text-red-500">*</span>
                        </label>
                        <select id="rfq_id" name="rfq_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('rfq_id') border-red-500 @enderror">
                            <option value="">اختر طلب عرض السعر</option>
                            @foreach($rfqs as $id => $title)
                                <option value="{{ $id }}" {{ old('rfq_id', $quotation->rfq_id) == $id ? 'selected' : '' }}>
                                    {{ $title }}
                                </option>
                            @endforeach
                        </select>
                        @error('rfq_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Supplier Selection --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            المورد <span class="text-red-500">*</span>
                        </label>
                        <select id="supplier_id" name="supplier_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('supplier_id') border-red-500 @enderror">
                            <option value="">اختر المورد</option>
                            @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ old('supplier_id', $quotation->supplier_id) == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Total Price --}}
                    <div>
                        <label for="total_price" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            السعر الإجمالي (د.ل) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" id="total_price" name="total_price" value="{{ old('total_price', $quotation->total_price) }}" required
                            step="0.01" min="1" max="9999999.99"
                            placeholder="0.00"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('total_price') border-red-500 @enderror">
                        @error('total_price')
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
                            <option value="pending" {{ old('status', $quotation->status) == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="reviewed" {{ old('status', $quotation->status) == 'reviewed' ? 'selected' : '' }}>تمت المراجعة</option>
                            <option value="accepted" {{ old('status', $quotation->status) == 'accepted' ? 'selected' : '' }}>مقبول</option>
                            <option value="rejected" {{ old('status', $quotation->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="cancelled" {{ old('status', $quotation->status) == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        </select>
                        @error('status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Valid Until --}}
                    <div>
                        <label for="valid_until" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            صلاحية العرض حتى (اختياري)
                        </label>
                        <input type="datetime-local" id="valid_until" name="valid_until"
                            value="{{ old('valid_until', $quotation->valid_until ? $quotation->valid_until->format('Y-m-d\TH:i') : '') }}"
                            min="{{ now()->format('Y-m-d\TH:i') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('valid_until') border-red-500 @enderror">
                        @error('valid_until')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Terms Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    الشروط التجارية
                </h2>

                <div class="grid grid-cols-1 gap-6">
                    {{-- Terms --}}
                    <div>
                        <label for="terms" class="block text-sm font-medium text-medical-gray-700 mb-2">
                            الشروط والتفاصيل (اختياري)
                        </label>
                        <textarea id="terms" name="terms" rows="6"
                            placeholder="أدخل شروط الدفع والتسليم والتفاصيل الأخرى..."
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl
                            focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500
                            @error('terms') border-red-500 @enderror">{{ old('terms', $quotation->terms) }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">الحد الأقصى: 2000 حرف</p>
                        @error('terms')
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
                        <span class="font-medium text-medical-gray-900 mr-2">{{ $quotation->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    @if($quotation->updated_at)
                        <div>
                            <span class="text-medical-gray-600">آخر تحديث:</span>
                            <span class="font-medium text-medical-gray-900 mr-2">{{ $quotation->updated_at->format('Y-m-d H:i') }}</span>
                        </div>
                    @endif
                    @if($quotation->rfq)
                        <div>
                            <span class="text-medical-gray-600">الطلب المرتبط:</span>
                            <span class="font-medium text-medical-gray-900 mr-2">{{ $quotation->rfq->title }}</span>
                        </div>
                    @endif
                    @if($quotation->supplier)
                        <div>
                            <span class="text-medical-gray-600">المورد:</span>
                            <span class="font-medium text-medical-gray-900 mr-2">{{ $quotation->supplier->company_name }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.quotations.show', $quotation) }}"
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

