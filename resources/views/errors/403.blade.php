{{-- 403 Access Denied Error Page --}}
@php
    $user = auth()->user();
    $userRole = $user->roles->first()?->name ?? 'staff';
    $userType = match($user->user_type_id) {
        1 => 'مدير النظام',
        2 => 'مورد',
        3 => 'مشتري',
        default => 'مستخدم',
    };
@endphp

<x-dashboard.layout title="غير مصرح" userRole="{{ strtolower($userRole) }}" :userName="$user->name" userType="{{ $userType }}">
    
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center px-4">
        <div class="max-w-2xl w-full">
            {{-- Error Icon --}}
            <div class="mb-6 flex justify-center">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            
            {{-- Error Message --}}
            <h1 class="text-4xl font-bold text-medical-gray-900 mb-4 font-display">غير مصرح لك بالوصول</h1>
            <p class="text-xl text-medical-gray-600 mb-2">
                ليس لديك الصلاحية المطلوبة للوصول إلى هذه الصفحة.
            </p>
            <p class="text-medical-gray-500 mb-8">
                إذا كنت تعتقد أن هذا خطأ، يرجى الاتصال بمدير النظام.
            </p>
            
            {{-- Action Buttons --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('dashboard') }}" 
                    class="inline-flex items-center gap-2 px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all font-semibold shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span>العودة للوحة التحكم</span>
                </a>
                
                @can('users.view')
                    <a href="{{ route('admin.users') }}" 
                        class="inline-flex items-center gap-2 px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <span>إدارة المستخدمين</span>
                    </a>
                @endcan
            </div>
            
            {{-- Helpful Information for Staff Users --}}
            @if($user->hasRole('Staff'))
                <div class="mt-8 p-6 bg-blue-50 border border-blue-200 rounded-xl text-right">
                    <h3 class="font-semibold text-medical-gray-900 mb-2">معلومات للموظفين</h3>
                    <p class="text-sm text-medical-gray-700 mb-3">
                        لديك حالياً <span class="font-semibold">{{ $user->permissions->count() }}</span> صلاحية نشطة.
                    </p>
                    <p class="text-sm text-medical-gray-600">
                        إذا كنت تحتاج إلى صلاحيات إضافية، يرجى الاتصال بمدير النظام.
                    </p>
                </div>
            @endif
        </div>
    </div>
</x-dashboard.layout>
