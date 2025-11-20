{{-- Admin Orders Management - Edit Order (Status & Notes Only) --}}
<x-dashboard.layout title="تعديل الطلب" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto px-6 py-8">
        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تعديل الطلب</h1>
                    <p class="mt-2 text-medical-gray-600">تحديث حالة الطلب والملاحظات</p>
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

        {{-- Order Info Card --}}
        <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
            <h2 class="text-xl font-bold text-medical-gray-900 mb-4">معلومات الطلب</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-medical-gray-600">رقم الطلب</p>
                    <p class="text-lg font-semibold text-medical-gray-900">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-600">تاريخ الطلب</p>
                    <p class="text-lg font-semibold text-medical-gray-900">
                        {{ $order->order_date ? $order->order_date->format('Y-m-d') : 'غير محدد' }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-600">المشتري</p>
                    <p class="text-lg font-semibold text-medical-gray-900">{{ $order->buyer->organization_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-600">المورد</p>
                    <p class="text-lg font-semibold text-medical-gray-900">{{ $order->supplier->company_name }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-600">القيمة الإجمالية</p>
                    <p class="text-lg font-semibold text-medical-gray-900">
                        {{ number_format($order->total_amount, 2) }} {{ $order->currency }}</p>
                </div>
                <div>
                    <p class="text-sm text-medical-gray-600">عدد الأصناف</p>
                    <p class="text-lg font-semibold text-medical-gray-900">{{ $order->items->count() }} صنف</p>
                </div>
            </div>
        </div>

        {{-- Edit Form --}}
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <h2 class="text-xl font-bold text-medical-gray-900 mb-6">تحديث حالة الطلب</h2>

            <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Hidden fields to preserve order data --}}
                <input type="hidden" name="quotation_id" value="{{ $order->quotation_id }}">
                <input type="hidden" name="buyer_id" value="{{ $order->buyer_id }}">
                <input type="hidden" name="supplier_id" value="{{ $order->supplier_id }}">
                <input type="hidden" name="total_amount" value="{{ $order->total_amount }}">
                <input type="hidden" name="currency" value="{{ $order->currency }}">

                {{-- Status --}}
                <div>
                    <label for="status" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        حالة الطلب <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="status" id="status" required
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('status') border-medical-red-500 @enderror">
                        <option value="pending" {{ old('status', $order->status) == 'pending' ? 'selected' : '' }}>قيد
                            الانتظار</option>
                        <option value="processing" {{ old('status', $order->status) == 'processing' ? 'selected' : '' }}>
                            قيد المعالجة</option>
                        <option value="shipped" {{ old('status', $order->status) == 'shipped' ? 'selected' : '' }}>تم
                            الشحن</option>
                        <option value="delivered" {{ old('status', $order->status) == 'delivered' ? 'selected' : '' }}>تم
                            التسليم</option>
                        <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>
                            ملغي</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label for="notes" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" id="notes" rows="4" placeholder="أضف ملاحظات حول الطلب..."
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-4 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('notes') border-medical-red-500 @enderror">{{ old('notes', $order->notes) }}</textarea>
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
                                d="M5 13l4 4L19 7" />
                        </svg>
                        حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.orders') }}"
                        class="px-8 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>

</x-dashboard.layout>

