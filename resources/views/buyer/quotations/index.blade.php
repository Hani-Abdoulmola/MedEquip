<x-dashboard.layout title="عروض الأسعار" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">
<div class="space-y-6">
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
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">إجمالي العروض</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">قيد المراجعة</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['accepted'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">مقبول</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['rejected'] ?? 0 }}</div>
            <div class="text-sm text-gray-500">مرفوض</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('buyer.quotations.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="ابحث باسم المورد..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="accepted" {{ request('status') === 'accepted' ? 'selected' : '' }}>مقبول</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>
            <div class="w-full sm:w-64">
                <select name="rfq_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    <option value="">جميع الطلبات</option>
                    @foreach($rfqs ?? [] as $rfq)
                        <option value="{{ $rfq->id }}" {{ request('rfq_id') == $rfq->id ? 'selected' : '' }}>
                            {{ $rfq->reference_code }} - {{ Str::limit($rfq->title, 30) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                بحث
            </button>
            @if(request('search') || request('status') || request('rfq_id'))
                <a href="{{ route('buyer.quotations.index') }}" class="px-6 py-2 text-gray-500 hover:text-gray-700 font-medium">
                    مسح
                </a>
            @endif
        </form>
    </div>

    {{-- Quotations List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($quotations->count() > 0)
            <div class="divide-y divide-gray-200">
                @foreach($quotations as $quotation)
                <div class="p-6 hover:bg-gray-50 transition-colors">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                        {{-- Supplier Info --}}
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center text-gray-600 font-bold flex-shrink-0">
                                {{ substr($quotation->supplier?->user?->name ?? 'م', 0, 1) }}
                            </div>
                            <div>
                                <h3 class="font-medium text-gray-900">{{ $quotation->supplier?->user?->name ?? 'مورد' }}</h3>
                                <p class="text-sm text-gray-500">{{ $quotation->supplier?->company_name ?? '' }}</p>
                                <div class="mt-1 flex items-center gap-2 text-xs text-gray-400">
                                    <span>للطلب: {{ $quotation->rfq?->reference_code }}</span>
                                    <span>•</span>
                                    <span>{{ $quotation->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Price & Status --}}
                        <div class="flex items-center gap-6">
                            <div class="text-left">
                                <div class="text-lg font-bold text-gray-900">
                                    {{ number_format($quotation->total_price, 2) }} د.ل
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $quotation->items_count ?? $quotation->items->count() }} بنود
                                </div>
                            </div>
                            
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'accepted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد المراجعة',
                                    'accepted' => 'مقبول',
                                    'rejected' => 'مرفوض',
                                ];
                            @endphp
                            <span class="px-3 py-1 text-sm font-medium rounded-full {{ $statusColors[$quotation->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ $statusLabels[$quotation->status] ?? $quotation->status }}
                            </span>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('buyer.quotations.show', $quotation) }}" 
                                   class="p-2 text-gray-500 hover:text-medical-blue-600 hover:bg-gray-100 rounded-lg transition-colors"
                                   title="عرض التفاصيل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </a>
                                @if($quotation->status === 'pending')
                                    <form action="{{ route('buyer.quotations.accept', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من قبول هذا العرض؟')"
                                                class="p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-colors"
                                                title="قبول">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('buyer.quotations.reject', $quotation) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" 
                                                onclick="return confirm('هل أنت متأكد من رفض هذا العرض؟')"
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors"
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
                </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $quotations->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عروض أسعار</h3>
                <p class="text-gray-500 mb-4">لم تستلم أي عروض أسعار بعد</p>
                <a href="{{ route('buyer.rfqs.index') }}" 
                   class="inline-flex items-center text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                    عرض طلبات عروض الأسعار ←
                </a>
            </div>
        @endif
    </div>
</div>
</x-dashboard.layout>

