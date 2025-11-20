{{-- Main Dashboard View with Role Detection - Matches Landing Page Design Quality --}}
@php
    $user = auth()->user();
    $userTypeId = $user->user_type_id ?? null;

    // Determine which dashboard to show based on user type
    $dashboardView = match ($userTypeId) {
        1 => 'dashboards.admin',
        2 => 'dashboards.supplier',
        3 => 'dashboards.buyer',
        default => null,
    };

    // Get user type name in Arabic
    $userTypeName = match ($userTypeId) {
        1 => 'مدير النظام',
        2 => 'مورد',
        3 => 'مشتري',
        default => 'مستخدم',
    };

    // Get user role for components
    $userRole = match ($userTypeId) {
        1 => 'admin',
        2 => 'supplier',
        3 => 'buyer',
        default => 'admin',
    };
@endphp

<x-dashboard.layout :title="'لوحة التحكم'" :userRole="$userRole" :userName="$user->name" :userType="$userTypeName">
    @if ($dashboardView)
        @include($dashboardView)
    @else
        {{-- Error State for Unknown User Type --}}
        <div class="flex items-center justify-center min-h-[60vh]">
            <div class="text-center max-w-md mx-auto animate-fade-in-up">
                <div class="w-24 h-24 bg-medical-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-medical-gray-900 mb-3 font-display">نوع المستخدم غير معروف</h3>
                <p class="text-medical-gray-600 mb-6 leading-relaxed">
                    عذراً، لا يمكننا تحديد نوع حسابك. يرجى الاتصال بمدير النظام لحل هذه المشكلة.
                </p>
                <div class="flex items-center justify-center space-x-4 space-x-reverse">
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white font-semibold rounded-xl shadow-medical hover:bg-medical-blue-700 hover:shadow-medical-lg transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        <span>العودة للرئيسية</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 font-semibold rounded-xl hover:bg-medical-gray-200 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-dashboard.layout>
