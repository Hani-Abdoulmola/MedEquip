{{-- Supplier Deliveries - Index --}}
<x-dashboard.layout title="عمليات التسليم" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">عمليات التسليم</h1>
                <p class="mt-2 text-medical-gray-600">تتبع جميع عمليات التسليم الخاصة بك</p>
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="bg-medical-blue-50 border border-medical-blue-200 text-medical-blue-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('info') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي التسليمات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد الانتظار</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد التوصيل</p>
                    <p class="text-3xl font-bold text-medical-purple-600 mt-1">{{ $stats['in_transit'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">تم التسليم</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['delivered'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        @if(isset($stats['failed']) && $stats['failed'] > 0)
            <div class="bg-white rounded-2xl shadow-medical p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">فشل التسليم</p>
                        <p class="text-3xl font-bold text-medical-red-600 mt-1">{{ $stats['failed'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('supplier.deliveries.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث برقم التسليم، اسم المستلم..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                    <option value="in_transit" {{ request('status') == 'in_transit' ? 'selected' : '' }}>قيد التوصيل</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فشل التسليم</option>
                </select>
            </div>

            <div class="w-48">
                <label for="from_date" class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="to_date" class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('supplier.deliveries.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Deliveries List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($deliveries->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد عمليات تسليم</h3>
                <p class="mt-2 text-medical-gray-600">ستظهر عمليات التسليم هنا عند إنشائها.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                رقم التسليم
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الطلب
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المستلم
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                تاريخ التسليم
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($deliveries as $delivery)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <p class="font-mono font-semibold text-medical-gray-900">{{ $delivery->delivery_number }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('supplier.orders.show', $delivery->order) }}"
                                        class="text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                        {{ $delivery->order->order_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-medium text-medical-gray-900">{{ $delivery->receiver_name }}</p>
                                    <p class="text-sm text-medical-gray-500">{{ $delivery->receiver_phone }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-medical-gray-900">{{ $delivery->delivery_date?->format('Y-m-d') }}</p>
                                    <p class="text-sm text-medical-gray-500">{{ $delivery->delivery_date?->diffForHumans() }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClasses = [
                                            'pending' => 'bg-medical-yellow-100 text-medical-yellow-700',
                                            'in_transit' => 'bg-medical-purple-100 text-medical-purple-700',
                                            'delivered' => 'bg-medical-green-100 text-medical-green-700',
                                            'failed' => 'bg-medical-red-100 text-medical-red-700',
                                        ];
                                        $statusLabels = [
                                            'pending' => 'قيد الانتظار',
                                            'in_transit' => 'قيد التوصيل',
                                            'delivered' => 'تم التسليم',
                                            'failed' => 'فشل التسليم',
                                        ];
                                    @endphp
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium {{ $statusClasses[$delivery->status] ?? 'bg-medical-gray-100 text-medical-gray-700' }}">
                                        {{ $statusLabels[$delivery->status] ?? $delivery->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('supplier.deliveries.show', $delivery) }}"
                                        class="inline-flex items-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        عرض
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $deliveries->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

