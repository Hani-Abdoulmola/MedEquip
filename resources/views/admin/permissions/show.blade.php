{{-- Admin Permissions Management - Show Permission Details (Modern Design) --}}
<x-dashboard.layout title="تفاصيل الصلاحية: {{ $permission->name }}" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display mb-2">تفاصيل الصلاحية</h1>
                <p class="text-medical-gray-600">عرض جميع المعلومات المرتبطة بهذه الصلاحية</p>
            </div>
            <a href="{{ route('admin.permissions.index') }}"
                class="inline-flex items-center gap-2 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة</span>
            </a>
        </div>
    </div>

    @php
        $module = explode('.', $permission->name)[0];
        $action = explode('.', $permission->name)[1] ?? '';
    @endphp

    {{-- Permission Overview Card --}}
    <div class="bg-gradient-to-r from-medical-purple-500 to-medical-pink-500 rounded-2xl shadow-xl p-8 mb-6 text-white">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-3xl font-bold mb-2">{{ $permission->ar_name ?? $permission->name }}</h2>
                    <div class="flex items-center gap-4 text-white/90">
                        <span class="flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            الوحدة: {{ ucfirst($module) }}
                        </span>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur-sm rounded-full text-sm font-semibold">
                            {{ ucfirst($action) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <p class="text-white/80 text-sm mb-1">Guard</p>
                <p class="text-xl font-bold">{{ $permission->guard_name }}</p>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-medical-blue-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-medical-gray-600">الأدوار</p>
                <svg class="w-8 h-8 text-medical-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-medical-gray-900">{{ $permission->roles->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-medical-green-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-medical-gray-600">المستخدمون</p>
                <svg class="w-8 h-8 text-medical-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold text-medical-gray-900">{{ $permission->users->count() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-medical-purple-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-medical-gray-600">تاريخ الإنشاء</p>
                <svg class="w-8 h-8 text-medical-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <p class="text-lg font-bold text-medical-gray-900">{{ $permission->created_at->format('Y-m-d') }}</p>
            <p class="text-xs text-medical-gray-500 mt-1">{{ $permission->created_at->diffForHumans() }}</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-medical-orange-500">
            <div class="flex items-center justify-between mb-2">
                <p class="text-sm text-medical-gray-600">آخر تحديث</p>
                <svg class="w-8 h-8 text-medical-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
            </div>
            <p class="text-lg font-bold text-medical-gray-900">{{ $permission->updated_at->format('Y-m-d') }}</p>
            <p class="text-xs text-medical-gray-500 mt-1">{{ $permission->updated_at->diffForHumans() }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Roles with this Permission --}}
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-lg p-6">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-medical-gray-200">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-medical-gray-900">الأدوار</h2>
                        <p class="text-sm text-medical-gray-600">{{ $permission->roles->count() }} دور يحتوي على هذه الصلاحية</p>
                    </div>
                </div>
            </div>

            @if ($permission->roles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach ($permission->roles as $role)
                        <a href="{{ route('admin.roles.show', $role) }}"
                            class="group block p-4 border-2 border-medical-gray-200 rounded-xl hover:border-medical-blue-400 hover:bg-medical-blue-50 transition-all hover:shadow-md">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                                    {{ mb_substr($role->name, 0, 1) }}
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-medical-gray-900 group-hover:text-medical-blue-600 mb-1">{{ $role->name }}</h3>
                                    <div class="flex items-center gap-3 text-sm text-medical-gray-600">
                                        <span>{{ $role->users->count() }} مستخدم</span>
                                        <span>•</span>
                                        <span>{{ $role->permissions->count() }} صلاحية</span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-medical-gray-400 group-hover:text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-medical-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <p class="text-medical-gray-600 font-medium">لا توجد أدوار مرتبطة بهذه الصلاحية</p>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Permission Info --}}
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-medical-gray-900 mb-4">معلومات الصلاحية</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-medical-gray-500 mb-1">الاسم الكامل</p>
                        <p class="font-semibold text-medical-gray-900">{{ $permission->ar_name ?? $permission->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-medical-gray-500 mb-1">الوحدة</p>
                        <p class="font-semibold text-medical-gray-900 capitalize">{{ $module }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-medical-gray-500 mb-1">الإجراء</p>
                        <p class="font-semibold text-medical-gray-900 capitalize">{{ $action }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-medical-gray-500 mb-1">Guard</p>
                        <p class="font-semibold text-medical-gray-900">{{ $permission->guard_name }}</p>
                    </div>
                </div>
            </div>

            {{-- Users with this Permission --}}
            @if ($permission->users->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-bold text-medical-gray-900">المستخدمون</h3>
                        <span class="px-3 py-1 bg-medical-green-100 text-medical-green-800 rounded-full text-sm font-semibold">
                            {{ $permission->users->count() }}
                        </span>
                    </div>
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach ($permission->users->take(10) as $user)
                            <a href="{{ route('admin.users.show', $user) }}"
                                class="flex items-center gap-3 p-3 rounded-xl hover:bg-medical-gray-50 transition-colors group">
                                <div class="w-10 h-10 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                                    {{ mb_substr($user->name, 0, 1) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-medical-gray-900 group-hover:text-medical-blue-600 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-medical-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                                <svg class="w-5 h-5 text-medical-gray-400 group-hover:text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                        @if ($permission->users->count() > 10)
                            <p class="text-center text-sm text-medical-gray-500 pt-2">
                                و {{ $permission->users->count() - 10 }} مستخدم آخر
                            </p>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

</x-dashboard.layout>
