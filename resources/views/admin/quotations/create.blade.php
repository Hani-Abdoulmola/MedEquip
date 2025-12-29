{{-- Admin Quotations - Create New Quotation --}}
<x-dashboard.layout title="إنشاء عرض سعر جديد" userRole="admin" :userName="auth()->user()->name" userType="مسؤول">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إنشاء عرض سعر جديد</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء عرض سعر جديد في النظام</p>
            </div>
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

    {{-- Create Quotation Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.quotations.store') }}">
            @csrf

            {{-- Basic Information Section --}}
            <div class="mb-8">
                <h2 class="text-xl font-bold text-medical-gray-900 mb-6 pb-3 border-b border-medical-gray-200">
                    المعلومات الأساسية
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                                <option value="{{ $id }}" {{ old('rfq_id') == $id ? 'selected' : '' }}>
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
                                <option value="{{ $id }}" {{ old('supplier_id') == $id ? 'selected' : '' }}>
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
                        <input type="number" id="total_price" name="total_price" value="{{ old('total_price') }}" required
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
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="reviewed" {{ old('status') == 'reviewed' ? 'selected' : '' }}>تمت المراجعة</option>
                            <option value="accepted" {{ old('status') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                            <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
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
                        <input type="datetime-local" id="valid_until" name="valid_until" value="{{ old('valid_until') }}"
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
                            @error('terms') border-red-500 @enderror">{{ old('terms') }}</textarea>
                        <p class="mt-1 text-xs text-medical-gray-500">الحد الأقصى: 2000 حرف</p>
                        @error('terms')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end space-x-4 space-x-reverse pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.quotations.index') }}"
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
                        <span>إنشاء عرض السعر</span>
                    </span>
                </button>
            </div>

        </form>
    </div>

</x-dashboard.layout>

