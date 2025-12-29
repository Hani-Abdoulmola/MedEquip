{{-- Admin Payments Management - Create New Payment --}}
<x-dashboard.layout title="إضافة دفعة جديدة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة دفعة جديدة</h1>
                <p class="mt-2 text-medical-gray-600">تسجيل دفعة مالية جديدة في النظام</p>
            </div>
            <a href="{{ route('admin.payments.index') }}"
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
        <form action="{{ route('admin.payments.store') }}" method="POST" class="bg-white rounded-2xl shadow-medical p-8">
            @csrf

            <div class="space-y-6">
                {{-- Invoice & Order Selection --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الفاتورة
                        </label>
                        <select name="invoice_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('invoice_id') border-medical-red-500 @enderror">
                            <option value="">اختر الفاتورة (اختياري)</option>
                            @foreach($invoices as $id => $invoiceNumber)
                                <option value="{{ $id }}" {{ old('invoice_id') == $id ? 'selected' : '' }}>
                                    {{ $invoiceNumber }}
                                </option>
                            @endforeach
                        </select>
                        @error('invoice_id')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الطلب
                        </label>
                        <select name="order_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('order_id') border-medical-red-500 @enderror">
                            <option value="">اختر الطلب (اختياري)</option>
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
                </div>

                {{-- Buyer & Supplier --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المشتري
                        </label>
                        <select name="buyer_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('buyer_id') border-medical-red-500 @enderror">
                            <option value="">اختر المشتري (اختياري)</option>
                            @foreach($buyers as $id => $name)
                                <option value="{{ $id }}" {{ old('buyer_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('buyer_id')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المورد
                        </label>
                        <select name="supplier_id"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('supplier_id') border-medical-red-500 @enderror">
                            <option value="">اختر المورد (اختياري)</option>
                            @foreach($suppliers as $id => $name)
                                <option value="{{ $id }}" {{ old('supplier_id') == $id ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                        @error('supplier_id')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Amount & Currency --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المبلغ <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="number" name="amount" step="0.01" min="0.01" value="{{ old('amount') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('amount') border-medical-red-500 @enderror">
                        @error('amount')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            العملة <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="currency" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('currency') border-medical-red-500 @enderror">
                            <option value="LYD" {{ old('currency') == 'LYD' ? 'selected' : '' }}>د.ل (دينار ليبي)</option>
                            <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>USD (دولار)</option>
                            <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>EUR (يورو)</option>
                        </select>
                        @error('currency')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Method & Status --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            طريقة الدفع <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="method" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('method') border-medical-red-500 @enderror">
                            <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>نقدي</option>
                            <option value="bank_transfer" {{ old('method') == 'bank_transfer' ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="credit_card" {{ old('method') == 'credit_card' ? 'selected' : '' }}>بطاقة ائتمانية</option>
                            <option value="paypal" {{ old('method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                            <option value="other" {{ old('method') == 'other' ? 'selected' : '' }}>أخرى</option>
                        </select>
                        @error('method')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الحالة <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="status" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('status') border-medical-red-500 @enderror">
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                            <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                            <option value="refunded" {{ old('status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Transaction ID & Paid At --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            رقم العملية
                        </label>
                        <input type="text" name="transaction_id" value="{{ old('transaction_id') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('transaction_id') border-medical-red-500 @enderror"
                            placeholder="رقم العملية (إن وجد)">
                        @error('transaction_id')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            تاريخ الدفع
                        </label>
                        <input type="datetime-local" name="paid_at" value="{{ old('paid_at') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('paid_at') border-medical-red-500 @enderror">
                        @error('paid_at')
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
                        placeholder="أضف ملاحظات حول الدفعة...">{{ old('notes') }}</textarea>
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
                        حفظ الدفعة
                    </button>
                    <a href="{{ route('admin.payments.index') }}"
                        class="px-8 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

</x-dashboard.layout>

