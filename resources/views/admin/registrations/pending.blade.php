{{-- Admin Registration Approvals - Pending Registrations --}}
<x-dashboard.layout title="طلبات التسجيل المعلقة" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{--
    ADMIN PENDING REGISTRATIONS PAGE
    Controller: RegistrationApprovalController@index
    --}}

    <div x-data="{ activeTab: 'suppliers', showFilters: false, showRejectModal: false, rejectType: '', rejectId: '', rejectName: '' }">

        {{-- Page Header --}}
        <div class="mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 font-display">طلبات التسجيل المعلقة</h1>
                    <p class="mt-2 text-medical-gray-600">مراجعة والموافقة على طلبات تسجيل الموردين والمشترين</p>
                </div>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
            {{-- Total Pending --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي الطلبات المعلقة</p>
                        <p class="text-3xl font-bold text-medical-blue-600 mt-2">{{ $stats['total_pending'] }}</p>
                        <p class="text-xs text-medical-gray-500 mt-1">بانتظار المراجعة</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Suppliers --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">موردين معلقين</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">
                            {{ $stats['pending_suppliers'] }}
                        </p>
                        <p class="text-xs text-medical-gray-500 mt-1">طلبات موردين</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-gray-100 to-medical-gray-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Pending Buyers --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">مشترين معلقين</p>
                        <p class="text-3xl font-bold text-medical-gray-900 mt-2">
                            {{ $stats['pending_buyers'] }}
                        </p>
                        <p class="text-xs text-medical-gray-500 mt-1">طلبات مشترين</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-gray-100 to-medical-gray-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-gray-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Total Rejected --}}
            <div class="bg-white rounded-2xl p-6 shadow-medical">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-medical-gray-600">إجمالي المرفوضة</p>
                        <p class="text-3xl font-bold text-medical-red-600 mt-2">
                            {{ $stats['total_rejected'] }}
                        </p>
                        <p class="text-xs text-medical-gray-500 mt-1">طلبات مرفوضة</p>
                    </div>
                    <div
                        class="w-12 h-12 bg-gradient-to-br from-medical-red-100 to-medical-red-200 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tabs Navigation --}}
        <div class="bg-white rounded-2xl shadow-medical mb-6">
            <div class="border-b border-medical-gray-200">
                <nav class="flex space-x-8 space-x-reverse px-6" aria-label="Tabs">
                    <button @click="activeTab = 'suppliers'"
                        :class="{
                            'border-medical-blue-600 text-medical-blue-600': activeTab === 'suppliers',
                            'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300': activeTab !== 'suppliers'
                        }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                        طلبات الموردين
                        @if ($stats['pending_suppliers'] > 0)
                            <span
                                class="mr-2 bg-medical-blue-100 text-medical-blue-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $stats['pending_suppliers'] }}
                            </span>
                        @endif
                    </button>
                    <button @click="activeTab = 'buyers'"
                        :class="{
                            'border-medical-blue-600 text-medical-blue-600': activeTab === 'buyers',
                            'border-transparent text-medical-gray-500 hover:text-medical-gray-700 hover:border-medical-gray-300': activeTab !== 'buyers'
                        }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200">
                        طلبات المشترين
                        @if ($stats['pending_buyers'] > 0)
                            <span
                                class="mr-2 bg-medical-blue-100 text-medical-blue-600 py-0.5 px-2 rounded-full text-xs">
                                {{ $stats['pending_buyers'] }}
                            </span>
                        @endif
                    </button>
                </nav>
            </div>
        </div>

        {{-- Suppliers Tab Content --}}
        <div x-show="activeTab === 'suppliers'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-medical-gray-200">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    اسم الشركة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    البريد الإلكتروني
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الهاتف
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    المدينة / الدولة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    تاريخ التقديم
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-medical-gray-200">
                            @forelse($pendingSuppliers as $supplier)
                                <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-medical-gray-900">
                                            {{ $supplier->company_name }}
                                        </div>
                                        @if ($supplier->tax_number)
                                            <div class="text-xs text-medical-gray-500">
                                                رقم ضريبي: {{ $supplier->tax_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">{{ $supplier->contact_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">{{ $supplier->contact_phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">
                                            {{ $supplier->city ?? '-' }} / {{ $supplier->country ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">
                                            {{ $supplier->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="text-xs text-medical-gray-500">
                                            {{ $supplier->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-medical-yellow-100 text-medical-yellow-800">
                                            معلق
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            {{-- Approve Button --}}
                                            <form method="POST"
                                                action="{{ route('admin.registrations.approve', ['type' => 'supplier', 'id' => $supplier->id]) }}"
                                                onsubmit="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-medical-green-600 text-white text-xs rounded-lg hover:bg-medical-green-700 transition-all duration-200">
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    موافقة
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <button type="button"
                                                @click="showRejectModal = true; rejectType = 'supplier'; rejectId = {{ $supplier->id }}; rejectName = '{{ $supplier->company_name }}'"
                                                class="inline-flex items-center px-3 py-1.5 bg-medical-red-600 text-white text-xs rounded-lg hover:bg-medical-red-700 transition-all duration-200">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                رفض
                                            </button>

                                            {{-- View Details Button --}}
                                            <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-medical-blue-600 text-white text-xs rounded-lg hover:bg-medical-blue-700 transition-all duration-200">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                عرض
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-medical-gray-500 text-lg font-medium">لا توجد طلبات موردين
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Buyers Tab Content --}}
        <div x-show="activeTab === 'buyers'" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-medical-gray-200">
                        <thead class="bg-medical-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    اسم المنظمة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    البريد الإلكتروني
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الهاتف
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    المدينة / الدولة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    تاريخ التقديم
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الحالة
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase tracking-wider">
                                    الإجراءات
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-medical-gray-200">
                            @forelse($pendingBuyers as $buyer)
                                <tr class="hover:bg-medical-gray-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-medical-gray-900">
                                            {{ $buyer->organization_name }}
                                        </div>
                                        @if ($buyer->license_number)
                                            <div class="text-xs text-medical-gray-500">
                                                رقم الترخيص: {{ $buyer->license_number }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">{{ $buyer->contact_email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">{{ $buyer->contact_phone }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">
                                            {{ $buyer->city ?? '-' }} / {{ $buyer->country ?? '-' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-medical-gray-900">
                                            {{ $buyer->created_at->format('Y-m-d') }}
                                        </div>
                                        <div class="text-xs text-medical-gray-500">
                                            {{ $buyer->created_at->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-medical-yellow-100 text-medical-yellow-800">
                                            معلق
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2 space-x-reverse">
                                            {{-- Approve Button --}}
                                            <form method="POST"
                                                action="{{ route('admin.registrations.approve', ['type' => 'buyer', 'id' => $buyer->id]) }}"
                                                onsubmit="return confirm('هل أنت متأكد من الموافقة على هذا الطلب؟')">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-medical-green-600 text-white text-xs rounded-lg hover:bg-medical-green-700 transition-all duration-200">
                                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    موافقة
                                                </button>
                                            </form>

                                            {{-- Reject Button --}}
                                            <button type="button"
                                                @click="showRejectModal = true; rejectType = 'buyer'; rejectId = {{ $buyer->id }}; rejectName = '{{ $buyer->organization_name }}'"
                                                class="inline-flex items-center px-3 py-1.5 bg-medical-red-600 text-white text-xs rounded-lg hover:bg-medical-red-700 transition-all duration-200">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                                رفض
                                            </button>

                                            {{-- View Details Button --}}
                                            <a href="{{ route('admin.buyers.show', $buyer) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-medical-blue-600 text-white text-xs rounded-lg hover:bg-medical-blue-700 transition-all duration-200">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                عرض
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-16 h-16 text-medical-gray-400 mb-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                            </svg>
                                            <p class="text-medical-gray-500 text-lg font-medium">لا توجد طلبات مشترين
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Rejection Modal --}}
        <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                {{-- Background overlay --}}
                <div x-show="showRejectModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-medical-gray-500 bg-opacity-75 transition-opacity"
                    @click="showRejectModal = false" aria-hidden="true"></div>

                {{-- Center modal --}}
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                {{-- Modal panel --}}
                <div x-show="showRejectModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                    <form
                        :action="`{{ route('admin.registrations.reject', ['type' => '__TYPE__', 'id' => '__ID__']) }}`.replace(
                            '__TYPE__', rejectType).replace('__ID__', rejectId)"
                        method="POST">
                        @csrf

                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div
                                    class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-medical-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-medical-red-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div class="mt-3 text-center sm:mt-0 sm:mr-4 sm:text-right flex-1">
                                    <h3 class="text-lg leading-6 font-medium text-medical-gray-900" id="modal-title">
                                        رفض طلب التسجيل
                                    </h3>
                                    <div class="mt-2">
                                        <p class="text-sm text-medical-gray-500">
                                            أنت على وشك رفض طلب التسجيل لـ: <span class="font-semibold"
                                                x-text="rejectName"></span>
                                        </p>
                                        <p class="text-sm text-medical-gray-500 mt-2">
                                            يرجى إدخال سبب الرفض (سيتم إرساله للمستخدم):
                                        </p>
                                    </div>
                                    <div class="mt-4">
                                        <textarea name="rejection_reason" rows="4" required
                                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-red-500 focus:border-medical-red-500 transition-all duration-200"
                                            placeholder="مثال: المستندات المقدمة غير مكتملة أو غير واضحة..."></textarea>
                                        @error('rejection_reason')
                                            <p class="mt-1 text-sm text-medical-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-medical-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-medical-red-600 text-base font-medium text-white hover:bg-medical-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-medical-red-500 sm:mr-3 sm:w-auto sm:text-sm transition-all duration-200">
                                تأكيد الرفض
                            </button>
                            <button type="button" @click="showRejectModal = false"
                                class="mt-3 w-full inline-flex justify-center rounded-xl border border-medical-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-medical-gray-700 hover:bg-medical-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-medical-blue-500 sm:mt-0 sm:w-auto sm:text-sm transition-all duration-200">
                                إلغاء
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

</x-dashboard.layout>
