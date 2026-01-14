<x-dashboard.layout title="تقييماتي" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">تقييماتي</h1>
            <p class="mt-1 text-sm text-gray-500">إدارة تقييماتك للموردين</p>
        </div>
        <a href="{{ route('buyer.reviews.create') }}"
           class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            إضافة تقييم جديد
        </a>
    </div>

    {{-- Reviews List --}}
    @if($reviews->count() > 0)
        <div class="space-y-4">
            @foreach($reviews as $review)
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <div class="flex flex-col lg:flex-row lg:items-start gap-6">
                        {{-- Supplier Info --}}
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="w-16 h-16 bg-gray-100 rounded-xl flex items-center justify-center overflow-hidden">
                                @if($review->supplier && $review->supplier->hasMedia('supplier_images'))
                                    <img src="{{ $review->supplier->getFirstMediaUrl('supplier_images', 'thumb') }}" 
                                         alt="{{ $review->supplier->company_name }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ $review->supplier->company_name ?? 'مورد محذوف' }}</h3>
                                @if($review->order)
                                    <p class="text-sm text-gray-500">طلب #{{ $review->order->order_number }}</p>
                                @endif
                                <p class="text-xs text-gray-400">{{ $review->created_at->format('Y-m-d') }}</p>
                            </div>
                        </div>

                        {{-- Review Content --}}
                        <div class="flex-1">
                            {{-- Rating Stars --}}
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex items-center">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-5 h-5 {{ $i <= $review->overall_rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                             fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm font-medium text-gray-700">{{ $review->overall_rating }}/5</span>
                                
                                {{-- Status Badge --}}
                                @switch($review->status)
                                    @case('pending')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-yellow-100 text-yellow-800">
                                            قيد المراجعة
                                        </span>
                                        @break
                                    @case('approved')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                            معتمد
                                        </span>
                                        @break
                                    @case('rejected')
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                            مرفوض
                                        </span>
                                        @break
                                @endswitch

                                @if($review->is_verified_purchase)
                                    <span class="flex items-center text-xs text-green-600 font-medium">
                                        <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        عملية شراء موثقة
                                    </span>
                                @endif
                            </div>

                            {{-- Review Title --}}
                            @if($review->title)
                                <h4 class="font-medium text-gray-900 mb-2">{{ $review->title }}</h4>
                            @endif

                            {{-- Review Text --}}
                            @if($review->review)
                                <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $review->review }}</p>
                            @endif

                            {{-- Detailed Ratings --}}
                            @if($review->quality_rating || $review->communication_rating || $review->delivery_rating || $review->value_rating)
                                <div class="flex flex-wrap gap-4 text-sm text-gray-500">
                                    @if($review->quality_rating)
                                        <span>الجودة: {{ $review->quality_rating }}/5</span>
                                    @endif
                                    @if($review->communication_rating)
                                        <span>التواصل: {{ $review->communication_rating }}/5</span>
                                    @endif
                                    @if($review->delivery_rating)
                                        <span>التوصيل: {{ $review->delivery_rating }}/5</span>
                                    @endif
                                    @if($review->value_rating)
                                        <span>القيمة: {{ $review->value_rating }}/5</span>
                                    @endif
                                </div>
                            @endif

                            {{-- Would Recommend --}}
                            @if($review->would_recommend)
                                <p class="mt-2 text-sm text-green-600 flex items-center">
                                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M2 10.5a1.5 1.5 0 113 0v6a1.5 1.5 0 01-3 0v-6zM6 10.333v5.43a2 2 0 001.106 1.79l.05.025A4 4 0 008.943 18h5.416a2 2 0 001.962-1.608l1.2-6A2 2 0 0015.56 8H12V4a2 2 0 00-2-2 1 1 0 00-1 1v.667a4 4 0 01-.8 2.4L6.8 7.933a4 4 0 00-.8 2.4z"></path>
                                    </svg>
                                    أوصي بهذا المورد
                                </p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2">
                            <a href="{{ route('buyer.reviews.show', $review) }}" 
                               class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                               title="عرض التفاصيل">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($review->canBeEdited())
                                <a href="{{ route('buyer.reviews.edit', $review) }}" 
                                   class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors"
                                   title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            @endif
                            @if($review->status === 'pending')
                                <form action="{{ route('buyer.reviews.destroy', $review) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="حذف">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد تقييمات</h3>
            <p class="text-gray-500 mb-4">لم تقم بإضافة أي تقييم للموردين بعد</p>
            <a href="{{ route('buyer.reviews.create') }}" 
               class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                إضافة تقييم
            </a>
        </div>
    @endif
</x-dashboard.layout>

