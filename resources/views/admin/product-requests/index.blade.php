{{-- Admin Product Requests - Index --}}
<x-dashboard.layout title="طلبات المنتجات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">طلبات المنتجات</h1>
                <p class="mt-2 text-medical-gray-600">مراجعة طلبات إضافة منتجات جديدة من الموردين</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        {{-- Pending --}}
        <div class="bg-white rounded-2xl p-5 shadow-medical border-r-4 border-medical-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Duplicate --}}
        <div class="bg-white rounded-2xl p-5 shadow-medical border-r-4 border-medical-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مكرر محتمل</p>
                    <p class="text-2xl font-bold text-medical-purple-600 mt-1">{{ $stats['duplicate'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-purple-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Approved --}}
        <div class="bg-white rounded-2xl p-5 shadow-medical border-r-4 border-medical-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">تمت الموافقة</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Merged --}}
        <div class="bg-white rounded-2xl p-5 shadow-medical border-r-4 border-medical-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">تم الدمج</p>
                    <p class="text-2xl font-bold text-medical-blue-600 mt-1">{{ $stats['merged'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- Rejected --}}
        <div class="bg-white rounded-2xl p-5 shadow-medical border-r-4 border-medical-red-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مرفوض</p>
                    <p class="text-2xl font-bold text-medical-red-600 mt-1">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            {{-- Search --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="اسم المنتج، الموديل..."
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500">
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500">
                    <option value="">جميع الحالات</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                    <option value="duplicate" {{ request('status') == 'duplicate' ? 'selected' : '' }}>مكرر محتمل</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>تمت الموافقة</option>
                    <option value="merged" {{ request('status') == 'merged' ? 'selected' : '' }}>تم الدمج</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>

            {{-- Date From --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500">
            </div>

            {{-- Date To --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                    class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500">
            </div>

            {{-- Actions --}}
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 px-4 py-2.5 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">
                    بحث
                </button>
                <a href="{{ route('admin.product-requests.index') }}" class="px-4 py-2.5 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200">
                    مسح
                </a>
            </div>
        </form>
    </div>

    {{-- Requests Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase">المنتج</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase">المورد</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase">الفئة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase">الحالة</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-medical-gray-700 uppercase">التاريخ</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-medical-gray-700 uppercase">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($requests as $request)
                        <tr class="hover:bg-medical-gray-50 {{ $request->canBeReviewed() ? 'bg-medical-yellow-50/30' : '' }}">
                            {{-- Product --}}
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-medical-gray-900">{{ $request->name }}</p>
                                    @if($request->model || $request->brand)
                                        <p class="text-sm text-medical-gray-500">
                                            {{ $request->brand }}{{ $request->brand && $request->model ? ' - ' : '' }}{{ $request->model }}
                                        </p>
                                    @endif
                                    @if($request->duplicate_of)
                                        <span class="inline-flex items-center mt-1 px-2 py-0.5 text-xs bg-medical-purple-100 text-medical-purple-700 rounded-full">
                                            مشابه لـ: {{ $request->duplicateProduct?->name }}
                                            ({{ number_format($request->similarity_score, 0) }}%)
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Supplier --}}
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-medium text-medical-gray-900">{{ $request->supplier?->company_name }}</p>
                                    <p class="text-sm text-medical-gray-500">{{ $request->supplier?->user?->email }}</p>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4">
                                @if($request->category)
                                    <span class="px-3 py-1 text-xs bg-medical-blue-100 text-medical-blue-700 rounded-full">
                                        {{ $request->category->name }}
                                    </span>
                                @else
                                    <span class="text-medical-gray-400">-</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-700',
                                        'duplicate' => 'bg-purple-100 text-purple-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'merged' => 'bg-blue-100 text-blue-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                        'cancelled' => 'bg-gray-100 text-gray-700',
                                    ];
                                @endphp
                                <span class="px-3 py-1 text-xs rounded-full {{ $statusColors[$request->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $request->status_label }}
                                </span>
                            </td>

                            {{-- Date --}}
                            <td class="px-6 py-4 text-sm text-medical-gray-600">
                                {{ $request->created_at->format('Y-m-d') }}
                                <br>
                                <span class="text-xs text-medical-gray-400">{{ $request->created_at->diffForHumans() }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    @if($request->canBeReviewed())
                                        <a href="{{ route('admin.product-requests.review', $request) }}"
                                           class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition"
                                           title="مراجعة">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </a>
                                    @endif
                                    <a href="{{ route('admin.product-requests.show', $request) }}"
                                       class="p-2 text-medical-gray-600 hover:bg-medical-gray-100 rounded-lg transition"
                                       title="عرض التفاصيل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="w-16 h-16 text-medical-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                </svg>
                                <p class="text-medical-gray-600 font-semibold">لا توجد طلبات منتجات</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($requests->hasPages())
            <div class="px-6 py-4 border-t">
                {{ $requests->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

