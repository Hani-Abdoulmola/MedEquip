{{-- Admin Users Management - List All Users --}}
<x-dashboard.layout title="إدارة المستخدمين" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">

            {{-- Title & Subtitle --}}
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">إدارة المستخدمين</h1>
                <p class="mt-2 text-medical-gray-600">عرض وإدارة جميع مستخدمي النظام</p>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3">

                {{-- Filters Toggle Button (Outline Primary) --}}
                <button @click="showFilters = !showFilters"
                    class="inline-flex items-center space-x-2 space-x-reverse
                        px-6 py-3 rounded-xl font-medium shadow-sm transition-all duration-200
                        border border-medical-blue-600 text-medical-blue-600 
                        hover:bg-medical-blue-50">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4h18M6 8h12M9 12h6" />
                    </svg>

                    <span>الفلاتر والبحث</span>
                </button>

                {{-- Add New User Button --}}
                <a href="{{ route('admin.users.create') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse
                        px-6 py-3 rounded-xl font-medium shadow-medical transition-all duration-200
                        bg-medical-blue-600 text-white hover:bg-medical-blue-700">

                    <svg class="w-5 h-5" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>

                    <span>إضافة مستخدم جديد</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

        {{-- Total Users --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي المستخدمين</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-2">
                        {{ number_format($stats['total_users']) }}
                    </p>
                    <p class="text-xs text-medical-gray-500 mt-1">
                        مجموع الحسابات المسجلة في النظام
                    </p>
                </div>

                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 
                        rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>

            </div>
        </div>

        {{-- Active Users --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-medical-gray-600">المستخدمون النشطون</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-2">
                        {{ number_format($stats['active_users']) }}
                    </p>
                    <p class="text-xs text-medical-gray-500 mt-1">
                        {{ round(($stats['active_users'] / max($stats['total_users'], 1)) * 100) }}% من الإجمالي
                    </p>
                </div>

                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-green-100 to-medical-green-200 
                        rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

            </div>
        </div>

        {{-- Suppliers Count --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-medical-gray-600">الموردون</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">
                        {{ number_format($stats['suppliers_count']) }}
                    </p>
                    <p class="text-xs text-medical-gray-500 mt-1">
                        عدد الشركات المزودة
                    </p>
                </div>

                <div
                    class="w-12 h-12 bg-gradient-to-br from-purple-100 to-purple-200 
                        rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5" />
                    </svg>
                </div>

            </div>
        </div>

        {{-- Buyers Count --}}
        <div class="bg-white rounded-2xl p-6 shadow-medical">
            <div class="flex items-center justify-between">

                <div>
                    <p class="text-sm text-medical-gray-600">المشترون</p>
                    <p class="text-3xl font-bold text-medical-blue-600 mt-2">
                        {{ number_format($stats['buyers_count']) }}
                    </p>
                    <p class="text-xs text-medical-gray-500 mt-1">
                        المؤسسات الصحية المسجلة
                    </p>
                </div>

                <div
                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-100 to-medical-blue-200 
                        rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4" />
                    </svg>
                </div>

            </div>
        </div>

    </div>

    {{-- Filters Section --}}
    <div class="bg-white rounded-2xl shadow-medical mb-6" x-data="{ showFilters: false }">

        {{-- Filters Toggle Header --}}
        <div class="p-6 border-b border-medical-gray-200">
            <button @click="showFilters = !showFilters" class="flex items-center justify-between w-full text-right">

                <div class="flex items-center space-x-3 space-x-reverse">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 4h18M6 8h12M9 12h6" />
                    </svg>
                    <h3 class="text-lg font-bold text-medical-gray-900">الفلاتر والبحث</h3>
                </div>

                <svg class="w-5 h-5 text-medical-gray-600 transition-transform duration-200"
                    :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>

            </button>
        </div>

        {{-- Filters Form --}}
        <div x-show="showFilters" x-transition class="p-6">

            <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- User Type --}}
                <div>
                    <label class="text-sm font-medium text-medical-gray-700 mb-2 block">نوع المستخدم</label>
                    <select name="user_type"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl 
                           focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        <option value="">الكل</option>
                        <option value="1" @selected(request('user_type') == 1)>مدير النظام</option>
                        <option value="2" @selected(request('user_type') == 2)>مورد</option>
                        <option value="3" @selected(request('user_type') == 3)>مشتري</option>
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="text-sm font-medium text-medical-gray-700 mb-2 block">الحالة</label>
                    <select name="status"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl 
                           focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        <option value="">الكل</option>
                        <option value="active" @selected(request('status') == 'active')>نشط</option>
                        <option value="inactive" @selected(request('status') == 'inactive')>غير نشط</option>
                        <option value="suspended" @selected(request('status') == 'suspended')>موقوف</option>
                    </select>
                </div>

                {{-- Search --}}
                <div>
                    <label class="text-sm font-medium text-medical-gray-700 mb-2 block">البحث</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="ابحث بالاسم أو البريد الإلكتروني..."
                            class="w-full px-4 py-3 pl-12 border border-medical-gray-300 rounded-xl 
                               focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                        <svg class="w-5 h-5 text-medical-gray-400 absolute left-4 top-1/2 -translate-y-1/2"
                            fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="md:col-span-3 flex items-center justify-end gap-3 pt-4 border-t border-medical-gray-200">

                    {{-- Apply --}}
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-medical-blue-600 text-white rounded-xl 
                           hover:bg-medical-blue-700 transition-all font-medium shadow-medical">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 4h18M6 8h12M9 12h6" />
                        </svg>
                        <span>تطبيق الفلاتر</span>
                    </button>

                    {{-- Reset --}}
                    <a href="{{ route('admin.users') }}"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 
                           rounded-xl hover:bg-medical-gray-200 transition-all font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582M20 11a8 8 0 11-15.418-2M9 9h6" />
                        </svg>
                        <span>إعادة تعيين</span>
                    </a>

                </div>

            </form>

        </div>
    </div>



    {{-- Data Table --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">

        <div class="overflow-x-auto">
            <table class="w-full">

                {{-- Table Head --}}
                <thead class="bg-medical-gray-50 border-b border-medical-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-right text-sm font-medium text-medical-gray-700">الاسم</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-medical-gray-700">البريد الإلكتروني
                        </th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-medical-gray-700">نوع المستخدم</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-medical-gray-700">الحالة</th>
                        <th class="px-6 py-4 text-right text-sm font-medium text-medical-gray-700">تاريخ الإنشاء</th>
                        <th class="px-6 py-4 text-center text-sm font-medium text-medical-gray-700">الإجراءات</th>
                    </tr>
                </thead>

                {{-- TABLE BODY START --}}
                <tbody class="divide-y divide-medical-gray-200">

                    {{-- LOOP EACH USER --}}
                    @forelse ($users as $user)
                        <tr class="hover:bg-medical-gray-50 transition-all duration-200">

                            {{-- Name + Avatar --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">

                                    {{-- Avatar --}}
                                    @php
                                        $initials = mb_substr($user->name, 0, 2);
                                        $avatarColor = match ($user->user_type) {
                                            1
                                                => 'from-medical-blue-100 to-medical-blue-200 text-medical-blue-600', // Admin
                                            2 => 'from-purple-100 to-purple-200 text-purple-700', // Supplier
                                            3
                                                => 'from-medical-green-100 to-medical-green-200 text-medical-green-700', // Buyer
                                            default
                                                => 'from-medical-gray-100 to-medical-gray-200 text-medical-gray-600',
                                        };
                                    @endphp

                                    <div
                                        class="w-10 h-10 bg-gradient-to-br {{ $avatarColor }} rounded-full flex items-center justify-center">
                                        <span class="font-bold text-sm">{{ $initials }}</span>
                                    </div>

                                    <div>
                                        <p class="text-sm font-medium text-medical-gray-900">{{ $user->name }}</p>
                                        <p class="text-xs text-medical-gray-500">ID: {{ $user->id }}</p>
                                    </div>

                                </div>
                            </td>

                            {{-- Email --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-medical-gray-900">{{ $user->email }}</p>
                            </td>

                            {{-- Type Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $typeLabel = match ($user->user_type) {
                                        1 => ['مدير النظام', 'bg-medical-blue-100 text-medical-blue-700'],
                                        2 => ['مورد', 'bg-purple-100 text-purple-700'],
                                        3 => ['مشتري', 'bg-medical-green-100 text-medical-green-700'],
                                        default => ['غير محدد', 'bg-medical-gray-100 text-medical-gray-700'],
                                    };
                                @endphp

                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $typeLabel[1] }}">
                                    {{ $typeLabel[0] }}
                                </span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'active' => 'bg-medical-green-100 text-medical-green-700',
                                        'inactive' => 'bg-medical-gray-100 text-medical-gray-700',
                                        'suspended' => 'bg-medical-red-100 text-medical-red-700',
                                    ];
                                    $status = $user->status ?? 'inactive';
                                @endphp

                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-medium {{ $statusColors[$status] ?? '' }}">
                                    <svg class="w-3 h-3 ml-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    {{ $status === 'active' ? 'نشط' : ($status === 'suspended' ? 'موقوف' : 'غير نشط') }}
                                </span>
                            </td>

                            {{-- Created At --}}
                            <td class="px-6 py-4">
                                <p class="text-sm text-medical-gray-900">{{ $user->created_at->format('Y-m-d') }}</p>
                                <p class="text-xs text-medical-gray-500">{{ $user->created_at->diffForHumans() }}</p>
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">

                                    {{-- Edit --}}
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                        class="p-2 text-medical-blue-600 hover:bg-medical-blue-50 rounded-lg transition"
                                        title="تعديل">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414
                                 a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    {{-- Delete --}}
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="حذف">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862
                                     a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6
                                     m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3
                                     M4 7h16" />
                                            </svg>
                                        </button>

                                    </form>

                                </div>
                            </td>

                        </tr>

                    @empty

                        {{-- EMPTY STATE --}}
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-medical-gray-500">
                                لا توجد بيانات مطابقة للبحث حالياً.
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
                    عرض من {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من {{ $users->total() }} نتيجة
                </div>

                <div>
                    {{ $users->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        </div>

    </div> {{-- END TABLE WRAPPER --}}

</x-dashboard.layout>
