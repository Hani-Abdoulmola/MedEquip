{{-- Dashboard Layout - Wrapper for @extends compatibility --}}
@php
    $user = auth()->user();
    $userRole = 'admin'; // default
    $userType = 'مستخدم';
    
    if ($user->hasRole('Buyer') && $user->buyerProfile) {
        $userRole = 'buyer';
        $userType = 'مشتري';
    } elseif ($user->hasRole('Supplier') && $user->supplierProfile) {
        $userRole = 'supplier';
        $userType = 'مورد';
    } elseif ($user->hasRole('Admin') || $user->hasRole('Staff')) {
        $userRole = 'admin';
        $userType = 'مدير';
    }
@endphp

<x-dashboard.layout 
    :title="$__env->yieldContent('title', 'لوحة التحكم')" 
    :userRole="$userRole" 
    :userName="$user->name" 
    :userType="$userType"
>
    @yield('content')
</x-dashboard.layout>

