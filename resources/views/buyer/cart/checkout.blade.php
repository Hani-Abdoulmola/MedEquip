{{-- Buyer Cart Checkout - Submit as RFQ --}}
<x-dashboard.layout title="إرسال طلب عرض السعر" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6" x-data="{ isPublic: true, status: 'open', submitting: false }">
        {{-- Breadcrumb --}}
        <nav class="flex items-center gap-2 text-sm text-gray-500">
            <a href="{{ route('buyer.dashboard') }}" class="hover:text-medical-blue-600">لوحة التحكم</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <a href="{{ route('buyer.cart.index') }}" class="hover:text-medical-blue-600">السلة</a>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            <span class="text-gray-900 font-medium">إرسال الطلب</span>
        </nav>

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">إرسال طلب عرض السعر</h1>
                <p class="mt-1 text-sm text-gray-500">راجع المنتجات وأكمل معلومات الطلب</p>
            </div>
        </div>

        <form action="{{ route('buyer.cart.submit-rfq') }}" method="POST" @submit="submitting = true">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- RFQ Details --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h2 class="text-lg font-bold text-gray-900 mb-6 pb-4 border-b border-gray-100">
                            تفاصيل الطلب
                        </h2>

                        <div class="space-y-4">
                            {{-- Title --}}
                            <div>
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    عنوان الطلب <span class="text-red-500">*</span>
                                </label>
                                <input type="text" 
                                       id="title" 
                                       name="title" 
                                       value="{{ old('title', 'طلب عرض سعر - ' . count($items) . ' منتج') }}"
                                       required
                                       placeholder="مثال: طلب معدات طبية للمستشفى"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('title') border-red-500 @enderror">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Description --}}
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                    وصف الطلب
                                </label>
                                <textarea id="description" 
                                          name="description" 
                                          rows="4"
                                          placeholder="أضف تفاصيل إضافية عن متطلباتك..."
                                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Deadline --}}
                            <div>
                                <label for="deadline" class="block text-sm font-medium text-gray-700 mb-1">
                                    الموعد النهائي للعروض
                                </label>
                                <input type="date" 
                                       id="deadline" 
                                       name="deadline" 
                                       value="{{ old('deadline') }}"
                                       min="{{ now()->addDay()->format('Y-m-d') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent @error('deadline') border-red-500 @enderror">
                                @error('deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">اختياري - حدد موعداً نهائياً لاستقبال عروض الأسعار</p>
                            </div>

                            {{-- Visibility --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    نوع الطلب
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                           :class="isPublic ? 'border-medical-blue-500 bg-medical-blue-50' : 'border-gray-200 bg-white'">
                                        <input type="radio" name="is_public" value="1" x-model="isPublic" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium" :class="isPublic ? 'text-medical-blue-900' : 'text-gray-900'">
                                                    طلب عام
                                                </span>
                                                <span class="mt-1 text-xs" :class="isPublic ? 'text-medical-blue-600' : 'text-gray-500'">
                                                    يظهر لجميع الموردين المعتمدين
                                                </span>
                                            </span>
                                        </span>
                                        <svg x-show="isPublic" class="h-5 w-5 text-medical-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                           :class="!isPublic ? 'border-medical-blue-500 bg-medical-blue-50' : 'border-gray-200 bg-white'">
                                        <input type="radio" name="is_public" value="0" x-model="isPublic" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium" :class="!isPublic ? 'text-medical-blue-900' : 'text-gray-900'">
                                                    طلب خاص
                                                </span>
                                                <span class="mt-1 text-xs" :class="!isPublic ? 'text-medical-blue-600' : 'text-gray-500'">
                                                    يمكن تحديد موردين محددين لاحقاً
                                                </span>
                                            </span>
                                        </span>
                                        <svg x-show="!isPublic" class="h-5 w-5 text-medical-blue-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </label>
                                </div>
                            </div>

                            {{-- Status --}}
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">
                                    حالة الطلب
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                           :class="status === 'open' ? 'border-medical-green-500 bg-medical-green-50' : 'border-gray-200 bg-white'">
                                        <input type="radio" name="status" value="open" x-model="status" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium" :class="status === 'open' ? 'text-medical-green-900' : 'text-gray-900'">
                                                    نشر فوراً
                                                </span>
                                                <span class="mt-1 text-xs" :class="status === 'open' ? 'text-medical-green-600' : 'text-gray-500'">
                                                    سيتم إرسال إشعارات للموردين
                                                </span>
                                            </span>
                                        </span>
                                        <svg x-show="status === 'open'" class="h-5 w-5 text-medical-green-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border p-4 focus:outline-none"
                                           :class="status === 'draft' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-200 bg-white'">
                                        <input type="radio" name="status" value="draft" x-model="status" class="sr-only">
                                        <span class="flex flex-1">
                                            <span class="flex flex-col">
                                                <span class="block text-sm font-medium" :class="status === 'draft' ? 'text-yellow-900' : 'text-gray-900'">
                                                    حفظ كمسودة
                                                </span>
                                                <span class="mt-1 text-xs" :class="status === 'draft' ? 'text-yellow-600' : 'text-gray-500'">
                                                    يمكنك التعديل ونشره لاحقاً
                                                </span>
                                            </span>
                                        </span>
                                        <svg x-show="status === 'draft'" class="h-5 w-5 text-yellow-600" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Products List --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-4 bg-gray-50 border-b border-gray-100">
                            <h2 class="font-semibold text-gray-900">
                                المنتجات المطلوبة
                                <span class="text-sm text-gray-500 font-normal">({{ count($items) }} منتج)</span>
                            </h2>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach($items as $index => $item)
                                <div class="p-4 flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                        @if($item['product']->getFirstMediaUrl('product_images'))
                                            <img src="{{ $item['product']->getFirstMediaUrl('product_images', 'thumb') }}" 
                                                 alt="{{ $item['product']->name }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-900 truncate">{{ $item['product']->name }}</h4>
                                        @if($item['specifications'])
                                            <p class="text-sm text-gray-500 truncate">{{ $item['specifications'] }}</p>
                                        @endif
                                    </div>
                                    <div class="text-left flex-shrink-0">
                                        <span class="font-medium text-gray-900">{{ $item['quantity'] }}</span>
                                        <span class="text-sm text-gray-500">{{ $item['unit'] }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-6 space-y-6">
                        {{-- Summary Card --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="font-bold text-gray-900 mb-4">ملخص الطلب</h3>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">عدد المنتجات</span>
                                    <span class="font-medium text-gray-900">{{ count($items) }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">إجمالي الوحدات</span>
                                    <span class="font-medium text-gray-900">{{ collect($items)->sum('quantity') }}</span>
                                </div>
                                <div class="flex justify-between pt-3 border-t border-gray-100">
                                    <span class="text-gray-500">نوع الطلب</span>
                                    <span class="font-medium" :class="isPublic ? 'text-medical-blue-600' : 'text-gray-600'" x-text="isPublic ? 'عام' : 'خاص'"></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">الحالة</span>
                                    <span class="font-medium" :class="status === 'open' ? 'text-medical-green-600' : 'text-yellow-600'" x-text="status === 'open' ? 'نشر فوري' : 'مسودة'"></span>
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <button type="submit" 
                                        :disabled="submitting"
                                        class="w-full py-3 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                    <template x-if="!submitting">
                                        <span>إرسال الطلب</span>
                                    </template>
                                    <template x-if="submitting">
                                        <span class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            جاري الإرسال...
                                        </span>
                                    </template>
                                </button>
                            </div>
                        </div>

                        {{-- Back to Cart --}}
                        <a href="{{ route('buyer.cart.index') }}" 
                           class="block text-center text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                            ← العودة للسلة
                        </a>

                        {{-- Info Box --}}
                        <div class="bg-blue-50 rounded-xl p-4">
                            <div class="flex gap-3">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div>
                                    <h4 class="font-medium text-blue-900 text-sm">ملاحظة</h4>
                                    <p class="text-xs text-blue-700 mt-1">
                                        سيتم إشعار الموردين المعتمدين عند نشر الطلب. يمكنك مراجعة العروض المستلمة من صفحة عروض الأسعار.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-dashboard.layout>

