{{-- Admin Buyers Management - List All Buyers --}}
<x-dashboard.layout title="إدارة المشترين" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة المشترين</h1>
                <p class="mt-2 text-medical-gray-600">عرض وإدارة جميع المشترين في النظام</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.buyers.export', request()->all()) }}"
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-green-600 text-white rounded-xl hover:bg-medical-green-700 transition-all duration-200 font-semibold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>تصدير Excel</span>
                </a>
                <a href="{{ route('admin.buyers.create') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-medical">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>إضافة مشتري جديد</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المشترين</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">{{ $stats['total_buyers'] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مشترين نشطين</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ $stats['active_buyers'] ?? 0 }}
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
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">قيد المراجعة</p>
                    <p class="text-3xl font-bold text-medical-yellow-600 mt-2">
                        {{ $stats['pending_buyers'] ?? 0 }}
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
                    <p class="text-sm text-medical-gray-600">موثقين</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                        {{ $stats['verified_buyers'] ?? 0 }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl p-6 shadow-medical mb-6">
        <form method="GET" action="{{ route('admin.buyers') }}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                    <input name="search" type="text" value="{{ request('search') }}" placeholder="ابحث عن مشتري..."
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                </div>

                {{-- Active Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                    <select name="active"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">الكل</option>
                        <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>نشط</option>
                        <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>موقوف</option>
                    </select>
                </div>

                {{-- Verification --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">التوثيق</label>
                    <select name="verified"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">الكل</option>
                        <option value="1" {{ request('verified') == '1' ? 'selected' : '' }}>موثق</option>
                        <option value="0" {{ request('verified') == '0' ? 'selected' : '' }}>غير موثق</option>
                    </select>
                </div>

                {{-- Type --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">نوع المؤسسة</label>
                    <select name="type"
                        class="w-full px-4 py-2.5 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 transition-all">
                        <option value="">الكل</option>
                        <option value="hospital" {{ request('type') == 'hospital' ? 'selected' : '' }}>مستشفى</option>
                        <option value="clinic" {{ request('type') == 'clinic' ? 'selected' : '' }}>عيادة</option>
                        <option value="pharmacy" {{ request('type') == 'pharmacy' ? 'selected' : '' }}>صيدلية</option>
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

    {{-- Buyers Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المؤسسة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">المسؤول</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">البريد الإلكتروني
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الطلبات</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-semibold text-medical-gray-900">الإجراءات</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-medical-gray-200">
                    @forelse ($buyers as $buyer)
                        <tr class="hover:bg-medical-gray-50 transition-colors duration-150">

                            {{-- Institution --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <div
                                        class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold">
                                        {{ mb_substr($buyer->organization_name, 0, 1, 'UTF-8') }}
                                    </div>

                                    <div>
                                        <p class="font-medium text-medical-gray-900">{{ $buyer->organization_name }}
                                        </p>
                                        <p class="text-sm text-medical-gray-600">{{ $buyer->city ?? '—' }}</p>
                                    </div>
                                </div>
                            </td>

                            {{-- Manager / User --}}
                            <td class="px-6 py-4 text-medical-gray-900">
                                {{ $buyer->user->name ?? '—' }}
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4 text-medical-gray-600">
                                {{ $buyer->user->email ?? '—' }}
                            </td>

                            {{-- Orders Count --}}
                            <td class="px-6 py-4">
                                <span
                                    class="inline-flex px-3 py-1 text-xs font-medium rounded-full bg-medical-blue-100 text-medical-blue-700">
                                    {{ $buyer->orders_count ?? ($buyer->orders ? $buyer->orders->count() : 0) }} طلب
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @php
                                    $isActive = $buyer->is_active;
                                    $isVerified = $buyer->is_verified;

                                    if ($isActive && $isVerified) {
                                        $label = 'نشط وموثق';
                                        $class = 'bg-medical-green-100 text-medical-green-700';
                                    } elseif ($isActive) {
                                        $label = 'نشط';
                                        $class = 'bg-medical-blue-100 text-medical-blue-700';
                                    } elseif ($isVerified) {
                                        $label = 'موثق';
                                        $class = 'bg-medical-yellow-100 text-medical-yellow-700';
                                    } else {
                                        $label = 'غير نشط';
                                        $class = 'bg-medical-red-100 text-medical-red-700';
                                    }
                                @endphp

                                <span
                                    class="inline-flex px-3 py-1 text-xs font-medium rounded-full {{ $class }}">
                                    {{ $label }}
                                </span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-2 space-x-reverse">

                                    {{-- View --}}
                                    <a href="{{ route('admin.buyers.show', $buyer->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition-colors duration-200"
                                        title="عرض">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.buyers.edit', $buyer->id) }}"
                                        class="p-2 text-medical-yellow-600 hover:bg-medical-yellow-50 rounded-lg transition-colors duration-200"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536M9 13l6.293-6.293a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0L9 13zm-6 6h12" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.buyers.destroy', $buyer->id) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            onclick="return confirm('هل أنت متأكد من حذف هذا المشتري؟')"
                                            class="p-2 text-medical-red-600 hover:bg-medical-red-50 rounded-lg transition-colors duration-200"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                            </svg>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-6 text-center text-gray-500">
                                لا يوجد مشترين.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-medical-gray-200 bg-medical-gray-50">
            <div class="flex items-center justify-between">
                <div class="text-sm text-medical-gray-600">
                    عرض من {{ $buyers->firstItem() }} إلى {{ $buyers->lastItem() }} من {{ $buyers->total() }} نتيجة
                </div>
                <div>
                    {{ $buyers->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-dashboard.layout>
