{{-- Buyer Orders - Index --}}
<x-dashboard.layout title="طلباتي" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">طلباتي</h1>
                <p class="mt-2 text-medical-gray-600">متابعة طلبات الشراء الخاصة بك</p>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('error') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">إجمالي الطلبات</p>
                    <p class="text-2xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">قيد الانتظار</p>
                    <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">قيد المعالجة</p>
                    <p class="text-2xl font-bold text-medical-blue-600 mt-1">{{ $stats['processing'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">تم الشحن</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $stats['shipped'] }}</p>
                </div>
                <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">تم التسليم</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['delivered'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">إجمالي المصروفات</p>
                    <p class="text-lg font-bold text-medical-gray-900 mt-1">{{ number_format($stats['total_spending'], 2) }} د.ل</p>
                </div>
                <div class="w-10 h-10 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('buyer.orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم الطلب أو المورد..."
                    class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد المعالجة</option>
                    <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغى</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الفترة</label>
                <select name="date_filter" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent">
                    <option value="">الكل</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>اليوم</option>
                    <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>هذا الأسبوع</option>
                    <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="last_month" {{ request('date_filter') === 'last_month' ? 'selected' : '' }}>الشهر الماضي</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors">
                    بحث
                </button>
                <a href="{{ route('buyer.orders.index') }}" class="px-4 py-2 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-colors">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if($orders->isEmpty())
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-medical-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-medical-gray-900 mb-2">لا توجد طلبات</h3>
                <p class="text-medical-gray-600 mb-6">ابدأ بإنشاء طلب عرض سعر (RFQ) واقبل عرضاً لإنشاء طلب شراء</p>
                <a href="{{ route('buyer.rfqs.create') }}" class="inline-flex items-center px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-colors">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    إنشاء طلب عرض سعر
                </a>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">رقم الطلب</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">المورد</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">المبلغ</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">الحالة</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">التاريخ</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-medical-gray-100">
                        @foreach($orders as $order)
                            <tr class="hover:bg-medical-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <a href="{{ route('buyer.orders.show', $order) }}" class="font-semibold text-medical-blue-600 hover:text-medical-blue-700">
                                        {{ $order->order_number }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-medical-blue-100 rounded-lg flex items-center justify-center ml-3">
                                            <span class="text-sm font-semibold text-medical-blue-600">
                                                {{ mb_substr($order->supplier->company_name ?? 'M', 0, 1) }}
                                            </span>
                                        </div>
                                        <span class="text-medical-gray-900">{{ $order->supplier->company_name ?? 'غير معروف' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-medical-gray-900">{{ number_format($order->total_amount, 2) }}</span>
                                    <span class="text-xs text-medical-gray-500">{{ $order->currency }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'processing' => 'bg-blue-100 text-blue-800',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'processing' => 'قيد المعالجة',
                                            'shipped' => 'تم الشحن',
                                            'delivered' => 'تم التسليم',
                                            'cancelled' => 'ملغى',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $statusLabels[$order->status] ?? $order->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-medical-gray-600 text-sm">
                                    {{ $order->order_date?->format('Y/m/d') ?? $order->created_at->format('Y/m/d') }}
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('buyer.orders.show', $order) }}" 
                                       class="text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                        عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

