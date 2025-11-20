<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- SEO Meta Tags --}}
    <title>@yield('title', 'MediEquip - منصة المعدات الطبية الرائدة في العالم العربي')</title>
    <meta name="description" content="@yield('description', 'منصة MediEquip الرائدة لربط موردي المعدات الطبية بالمؤسسات الصحية في العالم العربي. حلول متكاملة، أسعار تنافسية، وخدمة احترافية.')">
    <meta name="keywords" content="معدات طبية, مستلزمات طبية, موردين طبيين, مؤسسات صحية, تجارة طبية, MediEquip">
    <meta name="author" content="MediEquip">

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="@yield('og_title', 'MediEquip - منصة المعدات الطبية الرائدة')">
    <meta property="og:description" content="@yield('og_description', 'منصة MediEquip الرائدة لربط موردي المعدات الطبية بالمؤسسات الصحية')">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="@yield('og_image', asset('images/og-image.jpg'))">

    {{-- Twitter Card Meta Tags --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'MediEquip - منصة المعدات الطبية الرائدة')">
    <meta name="twitter:description" content="@yield('twitter_description', 'منصة MediEquip الرائدة لربط موردي المعدات الطبية بالمؤسسات الصحية')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/twitter-image.jpg'))">

    {{-- Favicon --}}
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Google Fonts - Professional Arabic & English Typography --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&family=Tajawal:wght@300;400;500;700;800&display=swap"
        rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Additional Styles --}}
    @stack('styles')
</head>

<body class="antialiased bg-white text-medical-gray-900 font-arabic" x-data="{ mobileMenuOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.pageYOffset > 50 })"
    :class="{ 'overflow-hidden': mobileMenuOpen }">

    {{-- Skip to Content (Accessibility) --}}
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:right-4 focus:z-50 focus:px-6 focus:py-3 focus:bg-medical-blue-600 focus:text-white focus:rounded-lg focus:shadow-lg">
        تخطي إلى المحتوى الرئيسي
    </a>

    {{-- Navigation --}}
    @include('partials.navbar')

    {{-- Main Content --}}
    <main id="main-content" class="min-h-screen">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('partials.footer')

    {{-- Back to Top Button --}}
    <button x-data="{ show: false }" x-init="window.addEventListener('scroll', () => { show = window.pageYOffset > 300 })" x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 transform translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform translate-y-4"
        @click="window.scrollTo({ top: 0, behavior: 'smooth' })"
        class="fixed bottom-8 left-8 z-40 p-3 bg-medical-blue-600 text-white rounded-full shadow-medical-lg hover:bg-medical-blue-700 hover:shadow-medical-xl transition-all duration-300 hover:scale-110 focus:outline-none focus:ring-4 focus:ring-medical-blue-300"
        aria-label="العودة إلى الأعلى" style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
        </svg>
    </button>

    {{-- Additional Scripts --}}
    @stack('scripts')

    {{-- Structured Data (JSON-LD) for SEO --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@@type": "Organization",
        "name": "MediEquip",
        "description": "منصة المعدات الطبية الرائدة في العالم العربي",
        "url": "{{ url('/') }}",
        "logo": "{{ asset('images/logo.png') }}",
        "contactPoint": {
            "@@type": "ContactPoint",
            "telephone": "+966-XX-XXX-XXXX",
            "contactType": "Customer Service",
            "areaServed": "SA",
            "availableLanguage": ["ar", "en"]
        },
        "sameAs": [
            "https://facebook.com/MediEquip",
            "https://twitter.com/MediEquip",
            "https://linkedin.com/company/MediEquip"
        ]
    }
    </script>
</body>

</html>
