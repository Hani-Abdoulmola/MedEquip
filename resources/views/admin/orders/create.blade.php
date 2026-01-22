{{-- Admin Orders Management - Create New Order --}}
<x-dashboard.layout title="إنشاء طلب جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إنشاء طلب جديد</h1>
                    <p class="mt-2 text-medical-gray-600">إنشاء أمر شراء من عرض أسعار مقبول</p>
                </div>
                <a href="{{ route('admin.orders') }}"
                    class="px-4 py-2 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    رجوع
                </a>
            </div>
        </div>

        {{-- Create Form --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h2 class="text-xl font-bold text-medical-gray-900 mb-6">معلومات الطلب</h2>

            <form action="{{ route('admin.orders.store') }}" method="POST" class="space-y-6" id="orderForm">
                @csrf

                {{-- Quotation Selection --}}
                <div>
                    <label for="quotation_id" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        عرض الأسعار <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="quotation_id" id="quotation_id" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('quotation_id') border-medical-red-500 @enderror">
                        <option value="">اختر عرض الأسعار...</option>
                        @foreach ($quotations as $id => $code)
                            <option value="{{ $id }}" {{ old('quotation_id') == $id ? 'selected' : '' }}>
                                {{ $code }}
                            </option>
                        @endforeach
                    </select>
                    @error('quotation_id')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-sm text-medical-gray-500">اختر عرض أسعار مقبول لتحويله إلى طلب</p>
                </div>

                {{-- Buyer Selection --}}
                <div>
                    <label for="buyer_id" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        المشتري <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="buyer_id" id="buyer_id" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('buyer_id') border-medical-red-500 @enderror">
                        <option value="">اختر المشتري...</option>
                        @foreach ($buyers as $id => $name)
                            <option value="{{ $id }}" {{ old('buyer_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('buyer_id')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Supplier Selection --}}
                <div>
                    <label for="supplier_id" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        المورد <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('supplier_id') border-medical-red-500 @enderror">
                        <option value="">اختر المورد...</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ old('supplier_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        حالة الطلب <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-medical-red-500 @enderror">
                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>قيد
                            الانتظار</option>
                        <option value="processing" {{ old('status') == 'processing' ? 'selected' : '' }}>قيد المعالجة
                        </option>
                        <option value="shipped" {{ old('status') == 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>تم التسليم
                        </option>
                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>ملغي</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Total Amount --}}
                <div>
                    <label for="total_amount" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        القيمة الإجمالية <span class="text-medical-red-500">*</span>
                    </label>
                    <input type="number" name="total_amount" id="total_amount" step="0.01" min="0" required
                        value="{{ old('total_amount') }}"
                        placeholder="0.00"
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('total_amount') border-medical-red-500 @enderror">
                    @error('total_amount')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Currency --}}
                <div>
                    <label for="currency" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        العملة <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="currency" id="currency" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('currency') border-medical-red-500 @enderror">
                        <option value="LYD" {{ old('currency', 'LYD') == 'LYD' ? 'selected' : '' }}>دينار ليبي (LYD)
                        </option>
                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)
                        </option>
                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                    </select>
                    @error('currency')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" id="notes" rows="4" placeholder="أضف ملاحظات حول الطلب..."
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('notes') border-medical-red-500 @enderror">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center gap-4 pt-4">
                    <button type="submit"
                        class="px-8 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm">
                        <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4v16m8-8H4" />
                        </svg>
                        إنشاء الطلب
                    </button>
                    <a href="{{ route('admin.orders') }}"
                        class="px-8 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-fill buyer and supplier when quotation is selected
            document.getElementById('quotation_id').addEventListener('change', function() {
                // This would require an AJAX call to fetch quotation details
                // For now, admin will manually select buyer and supplier
            });
        </script>
    @endpush

</x-dashboard.layout>
