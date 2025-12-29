{{-- Supplier RFQs - Index --}}
<x-dashboard.layout title="طلبات عروض الأسعار" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">طلبات عروض الأسعار</h1>
                <p class="mt-2 text-medical-gray-600">استعرض الطلبات المعينة لك وقدم عروض الأسعار</p>
            </div>
            <a href="{{ route('supplier.quotations.index') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>عروضي المقدمة</span>
            </a>
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

    @if (session('error') || session('info') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                {{ session('info') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الطلبات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">طلبات مفتوحة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['open'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">عروض مقدمة</p>
                    <p class="text-3xl font-bold text-medical-purple-600 mt-1">{{ $stats['quoted'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">بانتظار الرد</p>
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
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('supplier.rfqs.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث بالعنوان أو الكود..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوح</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلق</option>
                </select>
            </div>

            <div class="w-40">
                <label for="date_from" class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-40">
                <label for="date_to" class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    بحث
                </button>
                <a href="{{ route('supplier.rfqs.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- RFQs List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($rfqs->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد طلبات</h3>
                <p class="mt-2 text-medical-gray-600">لم يتم تعيين أي طلبات عروض أسعار لك بعد.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-medical-gray-200">
                    <thead class="bg-medical-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الطلب
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                المشتري
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الموعد النهائي
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الحالة
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                عرضك
                            </th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-medical-gray-600 uppercase tracking-wider">
                                الإجراءات
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-medical-gray-200">
                        @foreach ($rfqs as $rfq)
                            <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-semibold text-medical-gray-900">{{ $rfq->title }}</p>
                                        <p class="text-sm text-medical-gray-500">{{ $rfq->reference_code }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-medical-gray-900">{{ $rfq->buyer->organization_name ?? 'غير محدد' }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rfq->deadline)
                                        <p class="text-medical-gray-900">{{ $rfq->deadline->format('Y-m-d') }}</p>
                                        <p class="text-sm {{ $rfq->deadline->isPast() ? 'text-red-600' : 'text-medical-gray-500' }}">
                                            {{ $rfq->deadline->diffForHumans() }}
                                        </p>
                                    @else
                                        <span class="text-medical-gray-400">غير محدد</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rfq->status === 'open')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-green-100 text-medical-green-700">
                                            مفتوح
                                        </span>
                                    @elseif ($rfq->status === 'closed')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-gray-100 text-medical-gray-700">
                                            مغلق
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-red-100 text-medical-red-700">
                                            ملغى
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rfq->quotations->isNotEmpty())
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-blue-100 text-medical-blue-700">
                                            تم التقديم
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-medical-yellow-100 text-medical-yellow-700">
                                            لم يُقدم
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('supplier.rfqs.show', $rfq) }}"
                                            class="inline-flex items-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض
                                        </a>
                                        @if ($rfq->status === 'open' && $rfq->quotations->isEmpty())
                                            <a href="{{ route('supplier.rfqs.quote.create', $rfq) }}"
                                                class="inline-flex items-center px-4 py-2 bg-medical-green-50 text-medical-green-700 rounded-lg hover:bg-medical-green-100 transition-colors duration-150 text-sm font-medium">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                </svg>
                                                قدم عرض
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $rfqs->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

