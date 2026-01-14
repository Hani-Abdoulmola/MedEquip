<x-dashboard.layout title="إضافة تقييم جديد" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    {{-- Page Header --}}
    <div class="mb-6">
        <nav class="flex items-center text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="{{ route('buyer.reviews.index') }}" class="hover:text-medical-blue-600 transition-colors">تقييماتي</a>
            <svg class="w-5 h-5 mx-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            <span class="text-gray-900">إضافة تقييم</span>
        </nav>
        <h1 class="text-2xl font-bold text-gray-900">إضافة تقييم للمورد</h1>
        <p class="mt-1 text-sm text-gray-500">شاركنا تجربتك مع المورد</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" x-data="reviewForm()">
        <form method="POST" action="{{ route('buyer.reviews.store') }}" class="space-y-6">
            @csrf

            {{-- Supplier Selection --}}
            @if($supplier)
                <input type="hidden" name="supplier_id" value="{{ $supplier->id }}">
                @if($order)
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                @endif
                
                <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-16 h-16 bg-gray-200 rounded-xl flex items-center justify-center overflow-hidden">
                        @if($supplier->hasMedia('supplier_images'))
                            <img src="{{ $supplier->getFirstMediaUrl('supplier_images', 'thumb') }}" 
                                 alt="{{ $supplier->company_name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        @endif
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">{{ $supplier->company_name }}</h3>
                        @if($order)
                            <p class="text-sm text-gray-500">تقييم للطلب رقم #{{ $order->order_number }}</p>
                        @endif
                        @if($supplier->is_verified)
                            <span class="inline-flex items-center text-xs text-green-600">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                مورد موثق
                            </span>
                        @endif
                    </div>
                </div>
            @else
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700 mb-2">
                        اختر المورد <span class="text-red-500">*</span>
                    </label>
                    <select name="supplier_id" id="supplier_id" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('supplier_id') border-red-500 @enderror">
                        <option value="">-- اختر المورد --</option>
                        @foreach($availableSuppliers as $availableSupplier)
                            <option value="{{ $availableSupplier->id }}" {{ old('supplier_id') == $availableSupplier->id ? 'selected' : '' }}>
                                {{ $availableSupplier->company_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('supplier_id')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            {{-- Overall Rating --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    التقييم العام <span class="text-red-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    @for($i = 1; $i <= 5; $i++)
                        <button type="button" 
                                @click="overallRating = {{ $i }}"
                                :class="overallRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                class="focus:outline-none transition-colors hover:scale-110">
                            <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </button>
                    @endfor
                    <span class="mr-3 text-lg font-semibold text-gray-700" x-text="overallRating + '/5'"></span>
                </div>
                <input type="hidden" name="overall_rating" x-model="overallRating">
                @error('overall_rating')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            {{-- Detailed Ratings --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
                <h3 class="md:col-span-2 text-lg font-semibold text-gray-900">تقييمات تفصيلية (اختياري)</h3>
                
                {{-- Quality Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">جودة المنتجات</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="qualityRating = qualityRating === {{ $i }} ? 0 : {{ $i }}"
                                    :class="qualityRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                    class="focus:outline-none transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="quality_rating" x-model="qualityRating || ''">
                </div>

                {{-- Communication Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">التواصل والاستجابة</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="communicationRating = communicationRating === {{ $i }} ? 0 : {{ $i }}"
                                    :class="communicationRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                    class="focus:outline-none transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="communication_rating" x-model="communicationRating || ''">
                </div>

                {{-- Delivery Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">سرعة التوصيل</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="deliveryRating = deliveryRating === {{ $i }} ? 0 : {{ $i }}"
                                    :class="deliveryRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                    class="focus:outline-none transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="delivery_rating" x-model="deliveryRating || ''">
                </div>

                {{-- Value Rating --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">القيمة مقابل السعر</label>
                    <div class="flex items-center gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" 
                                    @click="valueRating = valueRating === {{ $i }} ? 0 : {{ $i }}"
                                    :class="valueRating >= {{ $i }} ? 'text-yellow-400' : 'text-gray-300'"
                                    class="focus:outline-none transition-colors">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="value_rating" x-model="valueRating || ''">
                </div>
            </div>

            {{-- Review Details --}}
            <div class="space-y-4 pt-4 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">تفاصيل التقييم</h3>

                {{-- Title --}}
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                        عنوان التقييم
                    </label>
                    <input type="text" name="title" id="title" 
                           value="{{ old('title') }}"
                           placeholder="مثال: تجربة ممتازة مع هذا المورد"
                           class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Review Text --}}
                <div>
                    <label for="review" class="block text-sm font-medium text-gray-700 mb-2">
                        تفاصيل التقييم
                    </label>
                    <textarea name="review" id="review" rows="4"
                              placeholder="شاركنا تجربتك مع هذا المورد بالتفصيل..."
                              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 @error('review') border-red-500 @enderror">{{ old('review') }}</textarea>
                    @error('review')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Pros & Cons --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pros" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center text-green-600">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                المميزات
                            </span>
                        </label>
                        <textarea name="pros" id="pros" rows="3"
                                  placeholder="ما الذي أعجبك في هذا المورد؟"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500">{{ old('pros') }}</textarea>
                    </div>
                    <div>
                        <label for="cons" class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center text-red-600">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                العيوب
                            </span>
                        </label>
                        <textarea name="cons" id="cons" rows="3"
                                  placeholder="ما الذي يمكن تحسينه؟"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500">{{ old('cons') }}</textarea>
                    </div>
                </div>

                {{-- Would Recommend --}}
                <div class="flex items-center">
                    <input type="checkbox" name="would_recommend" id="would_recommend" value="1"
                           {{ old('would_recommend') ? 'checked' : '' }}
                           class="w-5 h-5 text-medical-blue-600 border-gray-300 rounded focus:ring-medical-blue-500">
                    <label for="would_recommend" class="mr-3 text-sm font-medium text-gray-700">
                        أوصي بهذا المورد للآخرين
                    </label>
                </div>
            </div>

            {{-- Submit --}}
            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                <a href="{{ route('buyer.reviews.index') }}" 
                   class="px-6 py-3 text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition-colors font-medium">
                    إلغاء
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors font-medium">
                    إرسال التقييم
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function reviewForm() {
            return {
                overallRating: {{ old('overall_rating', 0) }},
                qualityRating: {{ old('quality_rating', 0) }},
                communicationRating: {{ old('communication_rating', 0) }},
                deliveryRating: {{ old('delivery_rating', 0) }},
                valueRating: {{ old('value_rating', 0) }},
            }
        }
    </script>
    @endpush
</x-dashboard.layout>

