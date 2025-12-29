{{-- Admin Payments Management - List All Payments --}}
<x-dashboard.layout title="إدارة المدفوعات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة المدفوعات</h1>
                <p class="mt-2 text-medical-gray-600">عرض ومراقبة جميع المدفوعات في النظام</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.payments.export', request()->all()) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>تصدير Excel</span>
                </a>
                <a href="{{ route('admin.payments.create') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-semibold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة دفعة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المدفوعات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المبالغ</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-2">{{ number_format($stats['total_amount'] ?? 0, 2) }} د.ل</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مكتملة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">{{ $stats['completed'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد الانتظار</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-2">{{ $stats['pending'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" action="{{ route('admin.payments.index') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                    <input name="search" type="text" value="{{ request('search') }}"
                        placeholder="رقم الدفعة، الطلب، المشتري..."
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">جميع الحالات</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتملة</option>
                        <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>فاشلة</option>
                        <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>مستردة</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                    <input name="from_date" type="date" value="{{ request('from_date') }}"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                    <input name="to_date" type="date" value="{{ request('to_date') }}"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
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

    {{-- Payments Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b-2 border-medical-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">رقم الدفعة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الفاتورة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المشتري</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المورد</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">المبلغ</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الطريقة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">التاريخ</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-medical-gray-200 bg-white">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-medical-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4">
                                <p class="font-mono font-semibold text-medical-blue-600">{{ $payment->payment_reference }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($payment->invoice)
                                    <a href="{{ route('admin.invoices.show', $payment->invoice_id) }}"
                                        class="text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                        {{ $payment->invoice->invoice_number }}
                                    </a>
                                @else
                                    <span class="text-medical-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-700 font-medium">
                                    {{ $payment->buyer->organization_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-700 font-medium">
                                    {{ $payment->supplier->company_name ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-900 font-semibold">
                                    {{ number_format($payment->amount, 2) }} {{ $payment->currency }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-600 text-sm">{{ $payment->method ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusConfig = [
                                        'pending' => ['label' => 'قيد الانتظار', 'color' => 'yellow'],
                                        'completed' => ['label' => 'مكتملة', 'color' => 'green'],
                                        'failed' => ['label' => 'فاشلة', 'color' => 'red'],
                                        'refunded' => ['label' => 'مستردة', 'color' => 'gray'],
                                    ];
                                    $config = $statusConfig[$payment->status] ?? ['label' => $payment->status, 'color' => 'gray'];
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full bg-medical-{{ $config['color'] }}-100 text-medical-{{ $config['color'] }}-700">
                                    <span class="w-2 h-2 bg-medical-{{ $config['color'] }}-600 rounded-full mr-2"></span>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-medical-gray-500 text-sm">
                                    {{ $payment->paid_at?->format('Y-m-d') ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('admin.payments.show', $payment->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-all"
                                        title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.payments.edit', $payment->id) }}"
                                        class="p-2 text-medical-yellow-600 hover:bg-medical-yellow-50 rounded-lg transition-all"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-medical-gray-600 text-lg font-semibold">لا توجد مدفوعات</p>
                                    <p class="text-medical-gray-500 text-sm mt-1">لم يتم العثور على أي مدفوعات مطابقة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($payments->hasPages())
            <div class="px-6 py-4 border-t border-medical-gray-200 bg-white">
                {{ $payments->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

