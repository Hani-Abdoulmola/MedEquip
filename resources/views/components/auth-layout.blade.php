<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?: 'تسجيل الدخول' }} - MediEquip</title>
    {{-- Google Fonts - Professional Arabic & English Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="antialiased bg-gradient-to-br from-medical-gray-50 via-white to-medical-blue-50 text-medical-gray-900 font-arabic">

    {{-- Background Decorative Elements --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        {{-- Animated Gradient Orbs --}}
        <div
            class="absolute top-20 -right-20 w-96 h-96 bg-medical-blue-500/10 rounded-full blur-3xl animate-pulse-slow">
        </div>
        <div class="absolute bottom-20 -left-20 w-96 h-96 bg-medical-green-500/10 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1s;"></div>

        {{-- Medical Pattern SVG --}}
        <svg class="absolute inset-0 w-full h-full opacity-5" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="medical-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M20 15v10M15 20h10" stroke="currentColor" stroke-width="1" fill="none"
                        class="text-medical-blue-600" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#medical-pattern)" />
        </svg>
    </div>

    {{-- Main Content --}}
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative z-10">

        {{-- Logo --}}
        <div class="mb-8 animate-fade-in-down">
            <a href="/" class="flex flex-col items-center space-y-2">
                {{-- Logo Icon --}}
                <div
                    class="w-16 h-16 bg-gradient-to-br from-medical-blue-600 to-medical-green-600 rounded-2xl flex items-center justify-center shadow-medical-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                {{-- Logo Text --}}
                <div class="text-center">
                    <h1
                        class="text-2xl font-bold bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent font-display">
                        MediEquip
                    </h1>
                    <p class="text-sm text-medical-gray-600">منصة المعدات الطبية</p>
                </div>
            </a>
        </div>

        {{-- Auth Card --}}
        <div
            class="w-full sm:max-w-2xl px-6 py-8 bg-white/80 backdrop-blur-sm shadow-medical-2xl overflow-hidden sm:rounded-3xl border border-medical-gray-200 animate-fade-in-up">
            {{ $slot }}
        </div>

        {{-- Footer Links --}}
        <div class="mt-6 text-center text-sm text-medical-gray-600 animate-fade-in" style="animation-delay: 0.2s;">
            <p>
                &copy; {{ date('Y') }} MediEquip. جميع الحقوق محفوظة.
            </p>
            <div class="mt-2 space-x-4 space-x-reverse">
                <a href="#" class="hover:text-medical-blue-600 transition-colors duration-300">سياسة الخصوصية</a>
                <span class="text-medical-gray-400">•</span>
                <a href="#" class="hover:text-medical-blue-600 transition-colors duration-300">شروط الاستخدام</a>
                <span class="text-medical-gray-400">•</span>
                <a href="#" class="hover:text-medical-blue-600 transition-colors duration-300">اتصل بنا</a>
            </div>
        </div>
    </div>

    {{-- Medical Floating Icons (Decorative) --}}
    <div class="fixed bottom-8 right-8 opacity-10 pointer-events-none animate-pulse-slow">
        <svg class="w-32 h-32 text-medical-blue-600" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H7v-7h2v7zm4 0h-2V7h2v10zm4 0h-2v-4h2v4z" />
        </svg>
    </div>

    <div class="fixed top-8 left-8 opacity-10 pointer-events-none animate-pulse-slow" style="animation-delay: 1.5s;">
        <svg class="w-24 h-24 text-medical-green-600" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd"
                d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd" />
        </svg>
    </div>

</body>

</html>
