<x-dashboard.layout title="طلبات عروض الأسعار" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">طلبات عروض الأسعار</h1>
            <p class="mt-1 text-sm text-gray-500">إدارة طلبات عروض الأسعار الخاصة بك</p>
        </div>
        <a href="{{ route('buyer.rfqs.create') }}" 
           class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            إنشاء طلب جديد
        </a>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-sm border-2 border-gray-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total'] }}</div>
            <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">الإجمالي</div>
        </div>
        <div class="bg-gradient-to-br from-blue-50 to-white rounded-xl shadow-sm border-2 border-blue-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-blue-600 mb-1">{{ $stats['open'] }}</div>
            <div class="text-xs font-medium text-blue-600 uppercase tracking-wide">مفتوح</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-sm border-2 border-green-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['awarded'] }}</div>
            <div class="text-xs font-medium text-green-600 uppercase tracking-wide">تم الترسية</div>
        </div>
        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-sm border-2 border-gray-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-gray-600 mb-1">{{ $stats['closed'] }}</div>
            <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">مغلق</div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-sm border-2 border-red-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-red-600 mb-1">{{ $stats['cancelled'] }}</div>
            <div class="text-xs font-medium text-red-600 uppercase tracking-wide">ملغي</div>
        </div>
        <div class="bg-gradient-to-br from-purple-50 to-white rounded-xl shadow-sm border-2 border-purple-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-purple-600 mb-1">{{ $stats['total_quotations'] }}</div>
            <div class="text-xs font-medium text-purple-600 uppercase tracking-wide">عروض مستلمة</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-5">
        <form method="GET" action="{{ route('buyer.rfqs.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="ابحث بالعنوان أو الرمز المرجعي..."
                           class="w-full pr-10 pl-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all bg-white">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوح</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلق</option>
                    <option value="awarded" {{ request('status') === 'awarded' ? 'selected' : '' }}>تم الترسية</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all font-semibold shadow-sm hover:shadow-md">
                بحث
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('buyer.rfqs.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-semibold">
                    مسح
                </a>
            @endif
        </form>
    </div>

    {{-- RFQs Cards Grid --}}
    @if($rfqs->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($rfqs as $rfq)
                @php
                    $statusColors = [
                        'draft' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
                        'open' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-200'],
                        'under_review' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                        'closed' => ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'],
                        'awarded' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                        'cancelled' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                    ];
                    $statusLabels = [
                        'draft' => 'مسودة',
                        'open' => 'مفتوح',
                        'under_review' => 'قيد المراجعة',
                        'closed' => 'مغلق',
                        'awarded' => 'تم الترسية',
                        'cancelled' => 'ملغي',
                    ];
                    $statusConfig = $statusColors[$rfq->status] ?? $statusColors['draft'];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border-2 {{ $statusConfig['border'] }} hover:shadow-md transition-all duration-200 overflow-hidden">
                    {{-- Status Header --}}
                    <div class="{{ $statusConfig['bg'] }} px-4 py-2 border-b {{ $statusConfig['border'] }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold {{ $statusConfig['text'] }} uppercase tracking-wide">
                                {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                            </span>
                            <span class="text-xs font-mono {{ $statusConfig['text'] }} opacity-75">
                                {{ $rfq->reference_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-5">
                        {{-- Title --}}
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            {{ $rfq->title }}
                        </h3>
                        
                        @if($rfq->description)
                            <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                                {{ $rfq->description }}
                            </p>
                        @endif

                        {{-- Info Grid --}}
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>{{ $rfq->items->count() }} بند</span>
                            </div>
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                @if($rfq->quotations->count() > 0)
                                    <a href="{{ route('buyer.quotations.index', ['rfq_id' => $rfq->id]) }}" 
                                       class="text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                        {{ $rfq->quotations->count() }} عروض
                                    </a>
                                @else
                                    <span class="text-gray-400">لا يوجد</span>
                                @endif
                            </div>
                        </div>

                        {{-- Deadline --}}
                        @if($rfq->deadline)
                            <div class="mb-4 p-3 rounded-lg {{ $rfq->deadline->isPast() ? 'bg-red-50 border border-red-200' : 'bg-gray-50 border border-gray-200' }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 {{ $rfq->deadline->isPast() ? 'text-red-500' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-xs font-medium {{ $rfq->deadline->isPast() ? 'text-red-700' : 'text-gray-700' }}">
                                            الموعد النهائي
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $rfq->deadline->format('Y-m-d') }}
                                    </span>
                                </div>
                                @if(!$rfq->deadline->isPast())
                                    <p class="text-xs text-gray-500 mt-1">{{ $rfq->deadline->diffForHumans() }}</p>
                                @else
                                    <p class="text-xs text-red-500 mt-1">انتهت الصلاحية</p>
                                @endif
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('buyer.rfqs.show', $rfq) }}" 
                               class="flex-1 px-4 py-2 bg-medical-blue-600 text-white text-center rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
                                عرض التفاصيل
                            </a>
                            @if(in_array($rfq->status, ['draft', 'open']) && $rfq->quotations->count() === 0)
                                <a href="{{ route('buyer.rfqs.edit', $rfq) }}" 
                                   class="p-2 text-gray-600 hover:text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                   title="تعديل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        {{-- Pagination --}}
        <div class="mt-6">
            {{ $rfqs->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد طلبات عروض أسعار</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">ابدأ بإنشاء طلب عرض سعر جديد للحصول على عروض تنافسية من الموردين</p>
                <a href="{{ route('buyer.rfqs.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm hover:shadow-md">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إنشاء طلب جديد
                </a>
            </div>
        </div>
    @endif
</div>
</x-dashboard.layout>

