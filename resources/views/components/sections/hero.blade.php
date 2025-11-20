{{-- Hero Section - Modern Medical Equipment B2B Platform --}}
<section id="hero"
    class="hero-section min-h-screen flex items-center relative overflow-hidden gradient-medical-light">

    {{-- Background Pattern --}}
    <div class="absolute inset-0 opacity-5">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <pattern id="medical-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <circle cx="10" cy="10" r="1" fill="currentColor" />
                    <path d="M10 5v10M5 10h10" stroke="currentColor" stroke-width="0.5" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#medical-pattern)" />
        </svg>
    </div>

    <div class="container relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Content Column --}}
            <div class="hero-content fade-up">
                {{-- Badge --}}
                <div class="medical-badge mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span>المنصة الرائدة في المنطقة</span>
                </div>

                {{-- Main Heading --}}
                <h1 class="heading-primary">
                    منصة
                    <span class="text-medical relative">
                        MediTrust
                        <svg class="absolute -bottom-2 left-0 w-full h-3 text-primary/20" viewBox="0 0 200 12"
                            fill="none">
                            <path d="M2 10C20 3 40 1 60 2C80 3 100 6 120 4C140 2 160 1 180 4C185 5 190 7 198 10"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </span>
                </h1>

                {{-- Subtitle --}}
                <p class="text-xl text-gray-600 mb-8 leading-relaxed max-w-lg">
                    نربط الموردين بالمؤسسات الطبية في العالم العربي من خلال منصة رقمية آمنة وموثوقة لتجارة المعدات
                    والأجهزة الطبية
                </p>

                {{-- Key Features --}}
                <div class="flex flex-wrap gap-6 mb-8">
                    <div class="flex items-center gap-2 text-gray-700">
                        <div class="w-2 h-2 bg-secondary rounded-full"></div>
                        <span class="font-medium">أكثر من 500 مورد معتمد</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <div class="w-2 h-2 bg-secondary rounded-full"></div>
                        <span class="font-medium">تغطية شاملة للمنطقة</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-700">
                        <div class="w-2 h-2 bg-secondary rounded-full"></div>
                        <span class="font-medium">دعم فني متخصص</span>
                    </div>
                </div>

                {{-- CTA Buttons --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#contact" class="btn btn-primary btn-lg group">
                        <span>ابدأ رحلتك الآن</span>
                        <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a href="#about" class="btn btn-outline btn-lg group">
                        <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-9-4V8a3 3 0 016 0v2M5 12a7 7 0 1114 0v5a1 1 0 01-1 1H6a1 1 0 01-1-1v-5z" />
                        </svg>
                        <span>اعرف المزيد</span>
                    </a>
                </div>

                {{-- Trust Indicators --}}
                <div class="flex items-center gap-8 mt-12 pt-8 border-t border-gray-200">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">500+</div>
                        <div class="text-sm text-gray-600">مورد معتمد</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">1000+</div>
                        <div class="text-sm text-gray-600">مؤسسة طبية</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">15+</div>
                        <div class="text-sm text-gray-600">دولة عربية</div>
                    </div>
                </div>
            </div>

            {{-- Visual Column --}}
            <div class="hero-visual fade-in relative">
                {{-- Main Illustration --}}
                <div class="relative">
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-primary/20 to-secondary/20 rounded-3xl transform rotate-3">
                    </div>
                    <div
                        class="relative bg-white rounded-3xl shadow-2xl p-8 transform -rotate-1 hover:rotate-0 transition-transform duration-500">
                        <img src="{{ asset('assets/img/hero-img.svg') }}" alt="منصة MediTrust للمعدات الطبية"
                            class="w-full h-auto" width="600" height="400">
                    </div>
                </div>

                {{-- Floating Elements --}}
                <div class="absolute -top-4 -right-4 bg-white rounded-2xl shadow-lg p-4 animate-float">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                        <span class="text-sm font-medium text-gray-700">متصل الآن</span>
                    </div>
                </div>

                <div class="absolute -bottom-4 -left-4 bg-white rounded-2xl shadow-lg p-4 animate-float-delayed">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-primary" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-medium text-gray-700">معتمد وآمن</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <a href="#about" class="flex flex-col items-center text-gray-400 hover:text-primary transition-colors"
            aria-label="انتقل إلى القسم التالي">
            <span class="text-sm mb-2">اكتشف المزيد</span>
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
            </svg>
        </a>
    </div>
</section>

{{-- Hero Section Styles --}}
<style>
    .hero-section {
        padding-top: 80px;
        /* Account for fixed navbar */
    }

    .bg-primary\/10 {
        background-color: rgba(37, 99, 235, 0.1);
    }

    .bg-primary\/20 {
        background-color: rgba(37, 99, 235, 0.2);
    }

    .bg-secondary\/20 {
        background-color: rgba(5, 150, 105, 0.2);
    }

    .text-primary\/20 {
        color: rgba(37, 99, 235, 0.2);
    }

    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(0.25rem);
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes float-delayed {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-8px);
        }
    }

    .animate-float {
        animation: float 3s ease-in-out infinite;
    }

    .animate-float-delayed {
        animation: float-delayed 3s ease-in-out infinite 1.5s;
    }

    .animate-bounce {
        animation: bounce 2s infinite;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0) translateX(-50%);
            animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }

        50% {
            transform: translateY(-25%) translateX(-50%);
            animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
    }

    .animate-pulse {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.5;
        }
    }
</style>
