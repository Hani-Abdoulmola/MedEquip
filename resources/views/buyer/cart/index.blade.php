{{-- Buyer RFQ Cart / Request Builder --}}
<x-dashboard.layout title="سلة طلب العروض" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    <div class="space-y-6" x-data="{ 
        showClearModal: false,
        submitting: false,
        removeProduct(productId) {
            fetch(`/buyer/cart/remove/${productId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if(data.success) location.reload();
            });
        }
    }">
        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-7 h-7 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    سلة طلب العروض
                </h1>
                <p class="mt-1 text-sm text-gray-500">أضف المنتجات التي تحتاجها وأرسلها كطلب عرض سعر واحد</p>
            </div>
            <a href="{{ route('buyer.products.index') }}" 
               class="inline-flex items-center px-4 py-2 text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                إضافة منتجات
            </a>
        </div>

        @if(count($products) > 0)
            {{-- Cart Items --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900">
                        المنتجات في السلة
                        <span class="text-sm text-gray-500 font-normal">({{ count($products) }} منتج)</span>
                    </h2>
                    <button @click="showClearModal = true" 
                            class="text-sm text-red-600 hover:text-red-700 font-medium">
                        إفراغ السلة
                    </button>
                </div>

                <div class="divide-y divide-gray-100">
                    @foreach($products as $index => $item)
                        <div class="p-4 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col lg:flex-row gap-4">
                                {{-- Product Image --}}
                                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                                    @if($item['product']->getFirstMediaUrl('product_images'))
                                        <img src="{{ $item['product']->getFirstMediaUrl('product_images', 'thumb') }}" 
                                             alt="{{ $item['product']->name }}"
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Details --}}
                                <div class="flex-1">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            @if($item['product']->category)
                                                <span class="text-xs text-medical-blue-600 font-medium">
                                                    {{ $item['product']->category->name }}
                                                </span>
                                            @endif
                                            <h3 class="font-medium text-gray-900">{{ $item['product']->name }}</h3>
                                            @if($item['product']->brand)
                                                <p class="text-sm text-gray-500">{{ $item['product']->brand }}</p>
                                            @endif
                                        </div>
                                        <button @click="removeProduct({{ $item['product']->id }})"
                                                class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Quantity & Specs --}}
                                    <form action="{{ route('buyer.cart.update', $item['product']) }}" method="POST" class="mt-3">
                                        @csrf
                                        @method('PUT')
                                        <div class="flex flex-col sm:flex-row gap-3">
                                            <div class="flex items-center gap-2">
                                                <label class="text-sm text-gray-600">الكمية:</label>
                                                <input type="number" name="quantity" value="{{ $item['quantity'] }}" 
                                                       min="1" max="10000"
                                                       class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-center focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <label class="text-sm text-gray-600">الوحدة:</label>
                                                <select name="unit" class="px-3 py-1.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                                    <option value="وحدة" {{ $item['unit'] == 'وحدة' ? 'selected' : '' }}>وحدة</option>
                                                    <option value="قطعة" {{ $item['unit'] == 'قطعة' ? 'selected' : '' }}>قطعة</option>
                                                    <option value="عبوة" {{ $item['unit'] == 'عبوة' ? 'selected' : '' }}>عبوة</option>
                                                    <option value="صندوق" {{ $item['unit'] == 'صندوق' ? 'selected' : '' }}>صندوق</option>
                                                    <option value="طقم" {{ $item['unit'] == 'طقم' ? 'selected' : '' }}>طقم</option>
                                                    <option value="جهاز" {{ $item['unit'] == 'جهاز' ? 'selected' : '' }}>جهاز</option>
                                                </select>
                                            </div>
                                            <button type="submit" class="px-3 py-1.5 text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                                تحديث
                                            </button>
                                        </div>
                                        <div class="mt-2">
                                            <input type="text" name="specifications" value="{{ $item['specifications'] }}" 
                                                   placeholder="مواصفات إضافية (اختياري)"
                                                   class="w-full px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                                        </div>
                                    </form>

                                    {{-- Suppliers Count --}}
                                    @if($item['product']->suppliers->count() > 0)
                                        <div class="mt-2 text-xs text-gray-500">
                                            <span class="inline-flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                                {{ $item['product']->suppliers->count() }} مورد متاح لهذا المنتج
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Summary & Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div>
                        <h3 class="font-semibold text-gray-900">ملخص السلة</h3>
                        <p class="text-sm text-gray-500">{{ count($products) }} منتج - {{ $totalItems }} وحدة إجمالية</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="{{ route('buyer.products.index') }}" 
                           class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-center font-medium">
                            متابعة التصفح
                        </a>
                        <a href="{{ route('buyer.cart.checkout') }}" 
                           class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors text-center font-medium">
                            إرسال كطلب عرض سعر
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- Empty State --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">السلة فارغة</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">
                    لم تقم بإضافة أي منتجات بعد. تصفح كتالوج المنتجات وأضف ما تحتاجه لإنشاء طلب عرض سعر.
                </p>
                <a href="{{ route('buyer.products.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    تصفح المنتجات
                </a>
            </div>
        @endif

        {{-- Clear Cart Modal --}}
        <div x-show="showClearModal" 
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showClearModal" 
                     @click="showClearModal = false"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showClearModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                إفراغ السلة
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    هل أنت متأكد من إفراغ السلة؟ سيتم حذف جميع المنتجات المضافة.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse gap-3">
                        <form action="{{ route('buyer.cart.clear') }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                                نعم، إفراغ السلة
                            </button>
                        </form>
                        <button @click="showClearModal = false" 
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-medical-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layout>

