<x-dashboard.layout title="عروض الأسعار" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
    {{-- Success/Error Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    
    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            <ul class="list-disc pr-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">عروض الأسعار المستلمة</h1>
            <p class="mt-1 text-sm text-gray-500">راجع وقارن عروض الأسعار من الموردين</p>
        </div>
        @if(request('rfq_id'))
            <a href="{{ route('buyer.quotations.compare', ['rfq_id' => request('rfq_id')]) }}" 
               class="inline-flex items-center justify-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                مقارنة العروض
            </a>
        @endif
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-gradient-to-br from-gray-50 to-white rounded-xl shadow-sm border-2 border-gray-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-gray-900 mb-1">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-xs font-medium text-gray-600 uppercase tracking-wide">إجمالي العروض</div>
        </div>
        <div class="bg-gradient-to-br from-yellow-50 to-white rounded-xl shadow-sm border-2 border-yellow-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-yellow-600 mb-1">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-xs font-medium text-yellow-600 uppercase tracking-wide">قيد المراجعة</div>
        </div>
        <div class="bg-gradient-to-br from-green-50 to-white rounded-xl shadow-sm border-2 border-green-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-green-600 mb-1">{{ $stats['accepted'] ?? 0 }}</div>
            <div class="text-xs font-medium text-green-600 uppercase tracking-wide">مقبول</div>
        </div>
        <div class="bg-gradient-to-br from-red-50 to-white rounded-xl shadow-sm border-2 border-red-200 p-5 text-center hover:shadow-md transition-shadow">
            <div class="text-3xl font-bold text-red-600 mb-1">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="text-xs font-medium text-red-600 uppercase tracking-wide">مرفوض</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border-2 border-gray-200 p-5">
        <form method="GET" action="{{ route('buyer.quotations.index') }}" class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1">
                <div class="relative">
                    <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="ابحث باسم المورد..."
                           class="w-full pr-10 pl-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all">
                </div>
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all bg-white">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>مقبول</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
            <div class="w-full sm:w-64">
                <select name="rfq_id" class="w-full px-4 py-2.5 border-2 border-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all bg-white">
                    <option value="">جميع الطلبات</option>
                    @foreach($rfqs ?? [] as $rfq)
                        <option value="{{ $rfq->id }}" {{ request('rfq_id') == $rfq->id ? 'selected' : '' }}>
                            {{ $rfq->reference_code }} - {{ Str::limit($rfq->title, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all font-semibold shadow-sm hover:shadow-md">
                بحث
            </button>
            @if(request('search') || request('status') || request('rfq_id'))
                <a href="{{ route('buyer.quotations.index') }}" class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-semibold">
                    مسح
                </a>
            @endif
        </form>
    </div>

    {{-- Quotations Cards Grid --}}
    @if($quotations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($quotations as $quotation)
                @php
                    $statusColors = [
                        'pending' => ['bg' => 'bg-yellow-50', 'text' => 'text-yellow-700', 'border' => 'border-yellow-200'],
                        'accepted' => ['bg' => 'bg-green-50', 'text' => 'text-green-700', 'border' => 'border-green-200'],
                        'rejected' => ['bg' => 'bg-red-50', 'text' => 'text-red-700', 'border' => 'border-red-200'],
                    ];
                    $statusLabels = [
                        'pending' => 'قيد المراجعة',
                        'accepted' => 'مقبول',
                        'rejected' => 'مرفوض',
                    ];
                    $statusConfig = $statusColors[$quotation->status] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-700', 'border' => 'border-gray-200'];
                @endphp
                <div class="bg-white rounded-xl shadow-sm border-2 {{ $statusConfig['border'] }} hover:shadow-md transition-all duration-200 overflow-hidden">
                    {{-- Status Header --}}
                    <div class="{{ $statusConfig['bg'] }} px-4 py-2 border-b {{ $statusConfig['border'] }}">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-semibold {{ $statusConfig['text'] }} uppercase tracking-wide">
                                {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                            </span>
                            <span class="text-xs font-mono {{ $statusConfig['text'] }} opacity-75">
                                {{ $quotation->reference_code }}
                            </span>
                        </div>
                    </div>

                    {{-- Card Content --}}
                    <div class="p-5">
                        {{-- Supplier Info --}}
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-full flex items-center justify-center text-medical-blue-700 font-bold text-lg flex-shrink-0">
                                {{ substr($quotation->supplier?->user?->name ?? 'م', 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 truncate">{{ $quotation->supplier?->user?->name ?? 'مورد' }}</h3>
                                <p class="text-sm text-gray-600 truncate">{{ $quotation->supplier?->company_name ?? '' }}</p>
                            </div>
                        </div>

                        {{-- RFQ Info --}}
                        <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <div class="flex items-center gap-2 text-xs text-gray-600 mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <span>للطلب: {{ $quotation->rfq?->reference_code }}</span>
                            </div>
                            <p class="text-sm font-medium text-gray-900 line-clamp-1">
                                {{ $quotation->rfq?->title ?? '' }}
                            </p>
                        </div>

                        {{-- Price --}}
                        <div class="mb-4 p-4 bg-gradient-to-br from-medical-blue-50 to-medical-green-50 rounded-lg border border-medical-blue-200">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900 mb-1">
                                    {{ number_format($quotation->total_price, 2) }}
                                </div>
                                <div class="text-sm text-gray-600">دينار ليبي</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $quotation->items_count ?? $quotation->items->count() }} بنود
                                </div>
                            </div>
                        </div>

                        {{-- Date --}}
                        <div class="mb-4 text-xs text-gray-500 text-center">
                            {{ $quotation->created_at->diffForHumans() }}
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 pt-4 border-t border-gray-100">
                            <a href="{{ route('buyer.quotations.show', $quotation) }}" 
                               class="flex-1 px-4 py-2 bg-medical-blue-600 text-white text-center rounded-lg hover:bg-medical-blue-700 transition-colors text-sm font-medium">
                                عرض التفاصيل
                            </a>
                            @if($quotation->status === 'pending')
                                <form action="{{ route('buyer.quotations.accept', $quotation) }}" method="POST" class="inline" id="accept-form-{{ $quotation->id }}">
                                    @csrf
                                    <button type="submit" 
                                            onclick="event.preventDefault(); if(confirm('هل أنت متأكد من قبول هذا العرض؟ سيتم رفض باقي العروض تلقائياً.')) { document.getElementById('accept-form-{{ $quotation->id }}').submit(); }"
                                            class="p-2 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg transition-colors"
                                            title="قبول">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </button>
                                </form>
                                <form action="{{ route('buyer.quotations.reject', $quotation) }}" method="POST" class="inline" id="reject-form-{{ $quotation->id }}">
                                    @csrf
                                    <button type="submit" 
                                            onclick="event.preventDefault(); if(confirm('هل أنت متأكد من رفض هذا العرض؟')) { document.getElementById('reject-form-{{ $quotation->id }}').submit(); }"
                                            class="p-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg transition-colors"
                                            title="رفض">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
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
            {{ $quotations->links() }}
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-100">
            <div class="text-center py-16">
                <div class="w-20 h-20 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">لا توجد عروض أسعار</h3>
                <p class="text-gray-500 mb-6 max-w-md mx-auto">لم تستلم أي عروض أسعار بعد. أنشئ طلب عرض سعر جديد لبدء تلقي العروض</p>
                <a href="{{ route('buyer.rfqs.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-sm hover:shadow-md">
                    عرض طلبات عروض الأسعار
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            </div>
        </div>
    @endif
</div>
</x-dashboard.layout>

