{{-- Dashboard Layout Component - Matches Landing Page Design Quality --}}
@props(['title' => 'لوحة التحكم', 'userRole' => 'admin', 'userName' => '', 'userType' => 'مستخدم'])

<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title }} - MediEquip</title>

    {{-- Google Fonts - Professional Arabic & English Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>

<body class="antialiased bg-medical-gray-50 text-medical-gray-900 font-arabic" x-data="{ sidebarOpen: true, mobileMenuOpen: false }"
    :class="{ 'overflow-hidden': mobileMenuOpen }">

    {{-- Skip to Content (Accessibility) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:right-4 focus:z-50 focus:px-6 focus:py-3 focus:bg-medical-blue-600 focus:text-white focus:rounded-lg focus:shadow-lg">
        تخطي إلى المحتوى الرئيسي
    </a>

    <div class="min-h-screen flex">
        {{-- Sidebar --}}
        <x-dashboard.sidebar :userRole="$userRole" :userName="$userName" :userType="$userType" />

        {{-- Main Content Area --}}
        <div class="flex-1 flex flex-col min-w-0">
            {{-- Header --}}
            <x-dashboard.header :title="$title" :userName="$userName" />

            {{-- Main Content --}}
            <main id="main-content" class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{-- Flash Messages --}}
                @if (session('success'))
                    <div class="mb-6 bg-medical-green-50 border-r-4 border-medical-green-500 rounded-lg p-4 shadow-medical animate-fade-in-down"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-medical-green-800 font-medium">{{ session('success') }}</p>
                            </div>
                            <button @click="show = false" class="text-medical-green-600 hover:text-medical-green-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-6 bg-medical-red-50 border-r-4 border-medical-red-500 rounded-lg p-4 shadow-medical animate-fade-in-down"
                        x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3 space-x-reverse">
                                <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p class="text-medical-red-800 font-medium">{{ session('error') }}</p>
                            </div>
                            <button @click="show = false" class="text-medical-red-600 hover:text-medical-red-800">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Page Content --}}
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="bg-white border-t border-medical-gray-200 px-4 sm:px-6 lg:px-8 py-4">
                <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-medical-gray-600 space-y-2 sm:space-y-0">
                    <p>&copy; 2025 MediEquip. جميع الحقوق محفوظة.</p>
                    <div class="flex items-center space-x-4 space-x-reverse">
                        <a href="#" class="hover:text-medical-blue-600 transition-colors duration-200">الدعم الفني</a>
                        <a href="#" class="hover:text-medical-blue-600 transition-colors duration-200">سياسة الخصوصية</a>
                        <a href="#" class="hover:text-medical-blue-600 transition-colors duration-200">الشروط والأحكام</a>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>

</html>

