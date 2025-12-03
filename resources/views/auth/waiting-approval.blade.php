<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بانتظار الموافقة - MediTrust</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="bg-gradient-to-br from-medical-blue-50 to-medical-gray-100 min-h-screen flex items-center justify-center p-4">
    @php
        $user = auth()->user();
        $profile = $user->supplierProfile ?? $user->buyerProfile;
        $isPending = $profile && !$profile->is_verified && !$profile->rejection_reason;
        $isRejected = $profile && $profile->rejection_reason;
        $entityType = $user->supplierProfile ? 'مورد' : 'مشتري';
    @endphp

    <div class="max-w-2xl w-full">
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex justify-center">
                <x-application-logo class="h-16 w-16" />
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8 md:p-12">
            {{-- Session Messages --}}
            @if (session('message'))
                <div class="mb-6 bg-medical-blue-50 border-r-4 border-medical-blue-600 rounded-xl p-4 text-right">
                    <p class="text-medical-blue-800">{{ session('message') }}</p>
                </div>
            @endif

            @if ($isPending)
                <div class="text-center">
                    <div
                        class="mx-auto w-24 h-24 bg-medical-blue-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-medical-gray-900 mb-4 font-display">طلبك قيد المراجعة</h1>
                    <p class="text-medical-gray-600 text-lg mb-6">شكراً لتسجيلك في منصة MediTrust. طلب تسجيلك كـ<span
                            class="font-semibold text-medical-blue-600">{{ $entityType }}</span> قيد المراجعة من قبل
                        فريقنا.</p>
                    <div class="bg-medical-blue-50 rounded-xl p-6 mb-8">
                        <p class="text-medical-gray-700 mb-2"><span class="font-semibold">تاريخ التقديم:</span>
                            {{ $profile->created_at->format('Y-m-d H:i') }}</p>
                        <p class="text-medical-gray-700"><span class="font-semibold">الوقت المنقضي:</span>
                            {{ $profile->created_at->diffForHumans() }}</p>
                    </div>
                    <p class="text-medical-gray-500 text-sm mb-8">عادةً ما تستغرق عملية المراجعة من 1-3 أيام عمل.</p>
                </div>
            @elseif($isRejected)
                <div class="text-center">
                    <div
                        class="mx-auto w-24 h-24 bg-medical-red-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-12 h-12 text-medical-red-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold text-medical-red-600 mb-4 font-display">تم رفض طلب التسجيل</h1>
                    <p class="text-medical-gray-600 text-lg mb-6">نأسف لإبلاغك بأن طلب تسجيلك كـ<span
                            class="font-semibold text-medical-red-600">{{ $entityType }}</span> لم يتم قبوله.</p>
                    <div class="bg-medical-red-50 border-r-4 border-medical-red-600 rounded-xl p-6 mb-8 text-right">
                        <h3 class="font-semibold text-medical-red-900 mb-2">سبب الرفض:</h3>
                        <p class="text-medical-red-800">{{ $profile->rejection_reason }}</p>
                    </div>
                    <a href="mailto:support@meditrust.ly"
                        class="inline-flex items-center justify-center px-6 py-3 bg-medical-blue-600 text-white font-semibold rounded-xl hover:bg-medical-blue-700 transition-all duration-200 shadow-medical mb-4">تواصل
                        مع الدعم</a>
                </div>
            @endif

            <form method="POST" action="{{ route('logout') }}" class="mt-6">
                @csrf
                <button type="submit"
                    class="w-full inline-flex items-center justify-center px-6 py-3 bg-medical-gray-200 text-medical-gray-700 font-semibold rounded-xl hover:bg-medical-gray-300 transition-all duration-200">
                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    تسجيل الخروج
                </button>
            </form>
        </div>

        <div class="text-center mt-8">
            <p class="text-medical-gray-600 text-sm">هل تحتاج إلى مساعدة؟ <a href="mailto:support@meditrust.ly"
                    class="text-medical-blue-600 hover:text-medical-blue-700 font-semibold">تواصل معنا</a></p>
            <p class="text-medical-gray-500 text-xs mt-2">&copy; {{ date('Y') }} MediTrust. جميع الحقوق محفوظة.</p>
        </div>
    </div>
</body>

</html>
