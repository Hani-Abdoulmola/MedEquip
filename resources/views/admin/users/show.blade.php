{{-- Admin User Details View --}}
<x-dashboard.layout title="تفاصيل المستخدم" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.users') }}"
                class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg active:scale-95 transition-all duration-200 cursor-pointer">
                <i class="fas fa-arrow-right text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">تفاصيل المستخدم</h1>
                <p class="text-gray-600 mt-1">عرض معلومات المستخدم الكاملة</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.edit', $user) }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span>تعديل</span>
                </a>
                <a href="{{ route('admin.users') }}"
                    class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    <span>العودة للقائمة</span>
                </a>
            </div>
        </div>
    </div>

    {{-- User Details Card --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column: User Info --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Basic Information --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-4">المعلومات الأساسية</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">الاسم الكامل</label>
                            <p class="text-lg font-semibold text-medical-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">البريد الإلكتروني</label>
                            <p class="text-lg font-semibold text-medical-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">رقم الهاتف</label>
                            <p class="text-lg font-semibold text-medical-gray-900">{{ $user->phone ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">نوع المستخدم</label>
                            <p class="text-lg font-semibold text-medical-gray-900">{{ $user->type->name ?? 'غير محدد' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">الحالة</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->status === 'active' ? 'نشط' : 'غير نشط' }}
                            </span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-medical-gray-500 mb-1">البريد موثق</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                {{ $user->email_verified_at ? 'موثق' : 'غير موثق' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Roles --}}
                <div class="border-b border-medical-gray-200 pb-6">
                    <h2 class="text-xl font-bold text-medical-gray-900 mb-4">الأدوار</h2>
                    @if($user->roles->count() > 0)
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->roles as $role)
                                <span class="inline-flex items-center px-4 py-2 rounded-lg bg-medical-blue-50 text-medical-blue-700 font-medium">
                                    {{ $role->ar_name ?? $role->name }}
                                </span>
                            @endforeach
                        </div>
                    @else
                        <p class="text-medical-gray-500">لا يوجد أدوار معينة</p>
                    @endif
                </div>

                {{-- Direct Permissions --}}
                @if(auth()->user()->can('users.manage_permissions'))
                    <div class="border-b border-medical-gray-200 pb-6">
                        <h2 class="text-xl font-bold text-medical-gray-900 mb-4">الصلاحيات المباشرة</h2>
                        @if($user->permissions->count() > 0)
                            <div class="space-y-2">
                                @foreach($user->permissions->groupBy(function($p) { return explode('.', $p->name)[0]; }) as $module => $permissions)
                                    <div>
                                        <h3 class="text-sm font-semibold text-medical-gray-700 mb-2">{{ $module }}</h3>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($permissions as $permission)
                                                <span class="inline-flex items-center px-3 py-1 rounded-lg bg-medical-green-50 text-medical-green-700 text-sm">
                                                    {{ $permission->ar_name ?? $permission->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-medical-gray-500">لا توجد صلاحيات مباشرة (يستخدم صلاحيات الأدوار فقط)</p>
                        @endif
                    </div>
                @endif
            </div>

            {{-- Right Column: Stats & Actions --}}
            <div class="space-y-6">
                {{-- User Avatar --}}
                <div class="text-center">
                    <div class="w-32 h-32 mx-auto bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white text-4xl font-bold">
                        {{ mb_substr($user->name, 0, 1) }}
                    </div>
                    <h3 class="mt-4 text-xl font-bold text-medical-gray-900">{{ $user->name }}</h3>
                    <p class="text-sm text-medical-gray-600">{{ $user->email }}</p>
                </div>

                {{-- Quick Stats --}}
                <div class="bg-medical-gray-50 rounded-xl p-6">
                    <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">إحصائيات سريعة</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">عدد الأدوار</span>
                            <span class="font-semibold text-medical-gray-900">{{ $user->roles->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">الصلاحيات المباشرة</span>
                            <span class="font-semibold text-medical-gray-900">{{ $user->permissions->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-medical-gray-600">تاريخ الإنشاء</span>
                            <span class="font-semibold text-medical-gray-900">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="space-y-3">
                    <a href="{{ route('admin.users.edit', $user) }}"
                        class="w-full inline-flex items-center justify-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <span>تعديل المستخدم</span>
                    </a>
                    @can('users.manage_permissions')
                        <a href="{{ route('admin.users.edit', $user) }}#permissions"
                            class="w-full inline-flex items-center justify-center space-x-2 space-x-reverse px-6 py-3 bg-medical-purple-600 text-white rounded-xl hover:bg-medical-purple-700 transition-all duration-200 font-medium">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            <span>إدارة الصلاحيات</span>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>

</x-dashboard.layout>

