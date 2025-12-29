{{-- Admin Orders Management - List All Orders --}}
<x-dashboard.layout title="إدارة الطلبات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة الطلبات</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراقبة جميع الطلبات في النظام</p>
            </div>
            <a href="{{ route('admin.orders.export', request()->all()) }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>تصدير Excel</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الطلبات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total_orders'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد الانتظار</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-2">
                        {{ $stats['pending_orders'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد المعالجة</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                        {{ $stats['processing_orders'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">تم التسليم</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ $stats['delivered_orders'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" action="{{ route('admin.orders') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                    <input name="search" type="text" value="{{ request('search') }}"
                        placeholder="رقم الطلب، المشتري، المورد..."
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>

                {{-- Buyer --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">المشتري</label>
                    <select name="buyer"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع المشترين</option>
                        @foreach ($buyers as $id => $name)
                            <option value="{{ $id }}" {{ request('buyer') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">المورد</label>
                    <select name="supplier"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع الموردين</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}" {{ request('supplier') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار
                        </option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>قيد
                            المعالجة</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>تم الشحن
                        </option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التسليم
                        </option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغي
                        </option>
                    </select>
                </div>

            </div>

            <div class="mt-4 flex justify-end">
                <button
                    class="px-6 py-2.5 bg-medical-blue-500 text-white rounded-xl hover:bg-medical-blue-600 transition-colors font-medium">
                    تطبيق الفلاتر
                </button>
            </div>
        </form>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b-2 border-medical-gray-200">
                    <tr>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            رقم الطلب</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            المشتري</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            المورد</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            المبلغ</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            التاريخ</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الحالة</th>
                        <th
                            class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">
                            الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200 bg-white">
                    @forelse($orders as $order)
                        <tr class="hover:bg-medical-gray-50 transition-colors duration-200">
                            {{-- Order Number --}}
                            <td class="px-6 py-4">
                                <p class="font-semibold text-medical-blue-600">{{ $order->order_number }}</p>
                            </td>

                            {{-- Buyer --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-700 font-medium">
                                    {{ $order->buyer->organization_name ?? '-' }}
                                </span>
                            </td>

                            {{-- Supplier --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-700 font-medium">
                                    {{ $order->supplier->company_name ?? '-' }}
                                </span>
                            </td>

                            {{-- Amount --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-900 font-semibold">
                                    {{ number_format($order->total_amount, 2) }} د.ل
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-500 text-sm">
                                    {{ $order->created_at->format('Y-m-d') }}
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => ['label' => 'قيد الانتظار', 'color' => 'yellow'],
                                        'processing' => ['label' => 'قيد المعالجة', 'color' => 'blue'],
                                        'shipped' => ['label' => 'تم الشحن', 'color' => 'purple'],
                                        'delivered' => ['label' => 'تم التسليم', 'color' => 'green'],
                                        'cancelled' => ['label' => 'ملغي', 'color' => 'red'],
                                    ];
                                    $config = $statusConfig[$order->status] ?? [
                                        'label' => $order->status,
                                        'color' => 'gray',
                                    ];
                                @endphp
                                <span
                                    class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-{{ $config['color'] }}-100 text-medical-{{ $config['color'] }}-700">
                                    <span
                                        class="w-2 h-2 bg-medical-{{ $config['color'] }}-600 rounded-full mr-2"></span>
                                    {{ $config['label'] }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                    class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-all"
                                    title="عرض التفاصيل">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-medical-gray-600 text-lg font-semibold">لا توجد طلبات</p>
                                    <p class="text-medical-gray-500 text-sm mt-1">لم يتم العثور على أي طلبات مطابقة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($orders->hasPages())
            <div class="px-6 py-4 border-t border-medical-gray-200 bg-white">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>
