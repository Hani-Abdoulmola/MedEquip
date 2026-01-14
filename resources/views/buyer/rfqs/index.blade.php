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
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
            <div class="text-sm text-gray-500">الإجمالي</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $stats['open'] }}</div>
            <div class="text-sm text-gray-500">مفتوح</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $stats['awarded'] }}</div>
            <div class="text-sm text-gray-500">تم الترسية</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-gray-600">{{ $stats['closed'] }}</div>
            <div class="text-sm text-gray-500">مغلق</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</div>
            <div class="text-sm text-gray-500">ملغي</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_quotations'] }}</div>
            <div class="text-sm text-gray-500">عروض مستلمة</div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="{{ route('buyer.rfqs.index') }}" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="ابحث بالعنوان أو الرمز المرجعي..."
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
            </div>
            <div class="w-full sm:w-48">
                <select name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>مفتوح</option>
                    <option value="under_review" {{ request('status') === 'under_review' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>مغلق</option>
                    <option value="awarded" {{ request('status') === 'awarded' ? 'selected' : '' }}>تم الترسية</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                </select>
            </div>
            <button type="submit" class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                بحث
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('buyer.rfqs.index') }}" class="px-6 py-2 text-gray-500 hover:text-gray-700 font-medium">
                    مسح
                </a>
            @endif
        </form>
    </div>

    {{-- RFQs Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        @if($rfqs->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الرمز المرجعي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">العنوان</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">البنود</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عروض الأسعار</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الموعد النهائي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">تاريخ الإنشاء</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($rfqs as $rfq)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono font-medium text-gray-900">{{ $rfq->reference_code }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ Str::limit($rfq->title, 40) }}</div>
                                @if($rfq->description)
                                    <div class="text-xs text-gray-500 mt-1">{{ Str::limit($rfq->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-900">{{ $rfq->items->count() }} بند</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'open' => 'bg-blue-100 text-blue-800',
                                        'under_review' => 'bg-yellow-100 text-yellow-800',
                                        'closed' => 'bg-gray-100 text-gray-800',
                                        'awarded' => 'bg-green-100 text-green-800',
                                        'cancelled' => 'bg-red-100 text-red-800',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'مسودة',
                                        'open' => 'مفتوح',
                                        'under_review' => 'قيد المراجعة',
                                        'closed' => 'مغلق',
                                        'awarded' => 'تم الترسية',
                                        'cancelled' => 'ملغي',
                                    ];
                                @endphp
                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$rfq->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$rfq->status] ?? $rfq->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rfq->quotations->count() > 0)
                                    <a href="{{ route('buyer.quotations.index', ['rfq_id' => $rfq->id]) }}" 
                                       class="text-sm text-medical-blue-600 hover:text-medical-blue-800 font-medium">
                                        {{ $rfq->quotations->count() }} عروض
                                    </a>
                                @else
                                    <span class="text-sm text-gray-400">لا يوجد</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($rfq->deadline)
                                    <span class="text-sm {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-gray-900' }}">
                                        {{ $rfq->deadline->format('Y-m-d') }}
                                    </span>
                                    @if($rfq->deadline->isPast())
                                        <span class="block text-xs text-red-500">انتهى</span>
                                    @else
                                        <span class="block text-xs text-gray-500">{{ $rfq->deadline->diffForHumans() }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm text-gray-500">{{ $rfq->created_at->format('Y-m-d') }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('buyer.rfqs.show', $rfq) }}" 
                                       class="text-medical-blue-600 hover:text-medical-blue-800 text-sm font-medium"
                                       title="عرض">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                    @if(in_array($rfq->status, ['draft', 'open']) && $rfq->quotations->count() === 0)
                                        <a href="{{ route('buyer.rfqs.edit', $rfq) }}" 
                                           class="text-yellow-600 hover:text-yellow-800 text-sm font-medium"
                                           title="تعديل">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('buyer.rfqs.destroy', $rfq) }}" method="POST" class="inline" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-800 text-sm font-medium"
                                                    title="حذف">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $rfqs->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد طلبات عروض أسعار</h3>
                <p class="text-gray-500 mb-4">ابدأ بإنشاء طلب عرض سعر جديد للحصول على عروض من الموردين</p>
                <a href="{{ route('buyer.rfqs.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-medical-blue-600 text-white rounded-lg hover:bg-medical-blue-700 transition-colors font-medium">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    إنشاء طلب جديد
                </a>
            </div>
        @endif
    </div>
</div>
</x-dashboard.layout>

