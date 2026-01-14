{{-- Buyer Deliveries - Index --}}
<x-dashboard.layout title="التوصيلات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-medical-gray-900 font-display">التوصيلات</h1>
        <p class="mt-2 text-medical-gray-600">تتبع شحنات طلباتك</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">إجمالي الشحنات</p>
            <p class="text-2xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">قيد الانتظار</p>
            <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">في الطريق</p>
            <p class="text-2xl font-bold text-medical-blue-600 mt-1">{{ $stats['in_transit'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">تم التسليم</p>
            <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['delivered'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">فشل التسليم</p>
            <p class="text-2xl font-bold text-medical-red-600 mt-1">{{ $stats['failed'] }}</p>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <p class="text-xs text-medical-gray-600">متأخرة</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['overdue'] }}</p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم التتبع..."
                    class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>في الطريق</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فشل التسليم</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الفترة</label>
                <select name="date_filter" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
                    <option value="">الكل</option>
                    <option value="this_week" {{ request('date_filter') === 'this_week' ? 'selected' : '' }}>هذا الأسبوع</option>
                    <option value="this_month" {{ request('date_filter') === 'this_month' ? 'selected' : '' }}>هذا الشهر</option>
                    <option value="overdue" {{ request('date_filter') === 'overdue' ? 'selected' : '' }}>متأخرة</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">بحث</button>
                <a href="{{ route('buyer.deliveries.index') }}" class="px-4 py-2 bg-medical-gray-100 rounded-xl">إعادة تعيين</a>
            </div>
        </form>
    </div>

    {{-- Deliveries Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if($deliveries->isEmpty())
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-medical-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-medical-gray-900 mb-2">لا توجد شحنات</h3>
                <p class="text-medical-gray-600">ستظهر الشحنات هنا بعد معالجة الطلبات</p>
            </div>
        @else
            <table class="w-full">
                <thead class="bg-medical-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">رقم التتبع</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">المورد</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">رقم الطلب</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">تاريخ التوصيل المتوقع</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase">إجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-100">
                    @foreach($deliveries as $delivery)
                        <tr class="hover:bg-medical-gray-50">
                            <td class="px-6 py-4 font-semibold text-medical-blue-600">{{ $delivery->delivery_number ?? 'غير محدد' }}</td>
                            <td class="px-6 py-4">{{ $delivery->order?->supplier?->company_name ?? 'غير معروف' }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('buyer.orders.show', $delivery->order) }}" class="text-medical-blue-600 hover:underline">
                                    {{ $delivery->order?->order_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $colors = ['pending' => 'bg-yellow-100 text-yellow-800', 'in_transit' => 'bg-blue-100 text-blue-800', 'delivered' => 'bg-green-100 text-green-800', 'failed' => 'bg-red-100 text-red-800'];
                                    $labels = ['pending' => 'قيد الانتظار', 'in_transit' => 'في الطريق', 'delivered' => 'تم التسليم', 'failed' => 'فشل التسليم'];
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $colors[$delivery->status] ?? 'bg-gray-100' }}">
                                    {{ $labels[$delivery->status] ?? $delivery->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-medical-gray-600">{{ $delivery->delivery_date?->format('Y/m/d') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('buyer.deliveries.show', $delivery) }}" class="text-medical-blue-600 hover:text-medical-blue-700">عرض التفاصيل</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-medical-gray-100">{{ $deliveries->links() }}</div>
        @endif
    </div>

</x-dashboard.layout>

