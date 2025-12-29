{{-- Admin Deliveries Management - Create New Delivery --}}
<x-dashboard.layout title="إضافة تسليم جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    <div class="max-w-4xl mx-auto">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إضافة تسليم جديد</h1>
                <p class="mt-2 text-medical-gray-600">تسجيل عملية تسليم جديدة في النظام</p>
            </div>
            <a href="{{ route('admin.deliveries.index') }}"
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
        <form action="{{ route('admin.deliveries.store') }}" method="POST" class="bg-white rounded-2xl shadow-medical p-8">
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

                {{-- Supplier & Buyer --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المورد <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="supplier_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('supplier_id') border-medical-red-500 @enderror">
                            <option value="">اختر المورد</option>
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

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            المشتري <span class="text-medical-red-500">*</span>
                        </label>
                        <select name="buyer_id" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('buyer_id') border-medical-red-500 @enderror">
                            <option value="">اختر المشتري</option>
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
                </div>

                {{-- Delivery Date & Location --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            تاريخ التسليم <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="date" name="delivery_date" value="{{ old('delivery_date', date('Y-m-d')) }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('delivery_date') border-medical-red-500 @enderror">
                        @error('delivery_date')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            موقع التسليم <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="text" name="delivery_location" value="{{ old('delivery_location') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('delivery_location') border-medical-red-500 @enderror"
                            placeholder="العنوان الكامل للتسليم">
                        @error('delivery_location')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Receiver Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            اسم المستلم <span class="text-medical-red-500">*</span>
                        </label>
                        <input type="text" name="receiver_name" value="{{ old('receiver_name') }}" required
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('receiver_name') border-medical-red-500 @enderror"
                            placeholder="اسم الشخص الذي سيستلم">
                        @error('receiver_name')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            هاتف المستلم
                        </label>
                        <input type="tel" name="receiver_phone" value="{{ old('receiver_phone') }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('receiver_phone') border-medical-red-500 @enderror"
                            placeholder="رقم الهاتف للتواصل">
                        @error('receiver_phone')
                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        الحالة <span class="text-medical-red-500">*</span>
                    </label>
                    <select name="status" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('status') border-medical-red-500 @enderror">
                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>قيد التوصيل</option>
                        <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                        <option value="failed" {{ old('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-semibold text-medical-gray-700 mb-2">
                        ملاحظات
                    </label>
                    <textarea name="notes" rows="4"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('notes') border-medical-red-500 @enderror"
                        placeholder="أضف ملاحظات حول التسليم...">{{ old('notes') }}</textarea>
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
                        حفظ التسليم
                    </button>
                    <a href="{{ route('admin.deliveries.index') }}"
                        class="px-8 py-3 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition-all duration-200 font-semibold">
                        إلغاء
                    </a>
                </div>
            </div>
        </form>
    </div>

</x-dashboard.layout>

