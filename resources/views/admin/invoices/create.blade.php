{{-- Admin Invoices Management - Create New Invoice --}}
<x-dashboard.layout title="إضافة فاتورة جديدة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة فاتورة جديدة</h1>
                <p class="mt-2 text-medical-gray-600">إنشاء فاتورة جديدة في النظام</p>
            </div>
            <a href="{{ route('admin.invoices.index') }}"
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
        <form action="{{ route('admin.invoices.store') }}" method="POST" class="bg-white rounded-2xl shadow-medical p-8">
            @csrf

            <div class="space-y-6">
                {{-- Order Selection --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        الطلب المرتبط <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="order_id" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('order_id') border-medical-red-500 @enderror">
                        <option value="">اختر الطلب</option>
                        @foreach($orders as $id => $orderNumber)
                            <option value="{{ $id }}" {{ old('order_id') == $id ? 'selected' : '' }}>
                                {{ $orderNumber }}
                            </option>
                        @endforeach
                    </select>
                    @error('order_id')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Invoice Date --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        تاريخ الفاتورة <span class="text-medical-red-500">*</span>
                    </label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', date('Y-m-d')) }}" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('invoice_date') border-medical-red-500 @enderror">
                    @error('invoice_date')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Amounts --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المجموع الفرعي <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="number" name="subtotal" step="0.01" min="0" value="{{ old('subtotal') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('subtotal') border-medical-red-500 @enderror">
                        @error('subtotal')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الضريبة
                        </label>
                        <input type="number" name="tax" step="0.01" min="0" value="{{ old('tax', 0) }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('tax') border-medical-red-500 @enderror">
                        @error('tax')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الخصم
                        </label>
                        <input type="number" name="discount" step="0.01" min="0" value="{{ old('discount', 0) }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('discount') border-medical-red-500 @enderror">
                        @error('discount')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Total Amount --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        المبلغ الإجمالي <span class="text-medical-red-500">*</span>
                    </label>
                    <input type="number" name="total_amount" step="0.01" min="0" value="{{ old('total_amount') }}" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('total_amount') border-medical-red-500 @enderror">
                    @error('total_amount')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            حالة الفاتورة <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('status') border-medical-red-500 @enderror">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="issued" {{ old('status') == 'issued' ? 'selected' : '' }}>صادرة</option>
                            <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>معتمدة</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغية</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            حالة الدفع <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="payment_status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('payment_status') border-medical-red-500 @enderror">
                            <option value="unpaid" {{ old('payment_status') == 'unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
                            <option value="partial" {{ old('payment_status') == 'partial' ? 'selected' : '' }}>جزئية</option>
                            <option value="paid" {{ old('payment_status') == 'paid' ? 'selected' : '' }}>مدفوعة</option>
                        </select>
                        @error('payment_status')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" rows="4"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('notes') border-medical-red-500 @enderror"
                        placeholder="أضف ملاحظات حول الفاتورة...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit"
                        class="px-8 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ الفاتورة
                    </button>
                    <a href="{{ route('admin.invoices.index') }}"
                        class="px-8 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

</x-dashboard.layout>

