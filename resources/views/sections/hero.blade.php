{{-- Hero Section - Professional Medical B2B Platform --}}
<section id="home"
    class="relative min-h-screen flex items-center overflow-hidden bg-gradient-to-br from-medical-gray-50 via-white to-medical-blue-50 pt-20">

    {{-- Background Decorative Elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        {{-- Animated Gradient Orbs --}}
        <div class="absolute top-20 -right-20 w-96 h-96 bg-medical-blue-500/10 rounded-full blur-3xl animate-pulse-slow">
        </div>
        <div class="absolute bottom-20 -left-20 w-96 h-96 bg-medical-green-500/10 rounded-full blur-3xl animate-pulse-slow"
            style="animation-delay: 1s;"></div>

        {{-- Medical Pattern SVG --}}
        <svg class="absolute inset-0 w-full h-full opacity-5"
            xmlns="https://images.pexels.com/photos/7089017/pexels-photo-7089017.jpeg">
            <defs>
                <pattern id="medical-pattern" x="0" y="0" width="40" height="40" patternUnits="userSpaceOnUse">
                    <path d="M20 15v10M15 20h10" stroke="currentColor" stroke-width="1" fill="none"
                        class="text-medical-blue-600" />
                </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#medical-pattern)" />
        </svg>
    </div>

    <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Content Column --}}
            <div class="text-center lg:text-right space-y-8 animate-fade-in-up">

                {{-- Trust Badge --}}
                <div
                    class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="text-sm font-semibold text-medical-blue-700">المنصة الرائدة في العالم العربي</span>
                </div>

                {{-- Main Heading --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-medical-gray-900 leading-tight font-display">
                    منصة
                    <span class="relative inline-block">
                        <span
                            class="relative z-10 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">
                            MediEquip
                        </span>
                        <svg class="absolute -bottom-2 right-0 w-full h-3 text-medical-blue-300" viewBox="0 0 200 12"
                            fill="none" preserveAspectRatio="none">
                            <path d="M2 10C20 3 40 1 60 2C80 3 100 6 120 4C140 2 160 1 180 4C185 5 190 7 198 10"
                                stroke="currentColor" stroke-width="3" stroke-linecap="round" />
                        </svg>
                    </span>
                    <br />
                    لتجارة المعدات الطبية
                </h1>

                {{-- Subtitle --}}
                <p class="text-lg sm:text-xl text-medical-gray-600 leading-relaxed max-w-2xl mx-auto lg:mx-0">
                    نربط موردي المعدات الطبية بالمؤسسات الصحية في العالم العربي. منصة شاملة توفر حلولاً مبتكرة، أسعاراً
                    تنافسية، وخدمة احترافية.
                </p>

                {{-- CTA Buttons --}}
                <div
                    class="flex flex-col sm:flex-row items-center justify-center lg:justify-start space-y-4 sm:space-y-0 sm:space-x-4 sm:space-x-reverse">
                    <a href="{{ route('register') }}"
                        class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300 group">
                        <span class="flex items-center justify-center space-x-2 space-x-reverse">
                            <span>ابدأ الآن </span>
                            <svg class="w-5 h-5 transform group-hover:-translate-x-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    </a>
                    <a href="#about"
                        class="w-full sm:w-auto px-8 py-4 bg-white text-medical-blue-600 font-bold rounded-xl border-2 border-medical-blue-600 hover:bg-medical-blue-50 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300 group">
                        <span class="flex items-center justify-center space-x-2 space-x-reverse">
                            <span>اكتشف المزيد</span>
                            <svg class="w-5 h-5 transform group-hover:translate-y-1 transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </a>
                </div>

                {{-- Trust Indicators --}}
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-medical-gray-200">
                    <div class="text-center">
                        <div class="text-3xl font-bold text-medical-blue-600 font-display">500+</div>
                        <div class="text-sm text-medical-gray-600 mt-1">مورد معتمد</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-medical-green-600 font-display">1000+</div>
                        <div class="text-sm text-medical-gray-600 mt-1">مؤسسة صحية</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold text-medical-blue-600 font-display">15+</div>
                        <div class="text-sm text-medical-gray-600 mt-1">دولة عربية</div>
                    </div>
                </div>
            </div>

            {{-- Image/Illustration Column --}}
            <div class="relative animate-fade-in-up" style="animation-delay: 0.2s;">
                {{-- Main Image Container --}}
                {{-- <div class="relative"> --}}
                {{-- Professional Medical Equipment Image --}}
                <div class="relative rounded-3xl shadow-medical-2xl overflow-hidden bg-white">
                    {{-- Image Container with fixed aspect ratio --}}
                    <div class="w-full" style="padding-bottom: 800px; position: relative;">
                        <img src="https://images.pexels.com/photos/7089013/pexels-photo-7089013.jpeg"
                            alt="معدات طبية حديثة" class="absolute inset-0 w-full h-full object-cover rounded-3xl"
                            loading="eager" onload="console.log('Hero image loaded successfully');"
                            onerror="console.error('Hero image failed to load');
                                this.style.display='none';
                    document.getElementById('hero-fallback-svg').style.display='flex';" />


                        {{-- Fallback SVG if image fails to load --}}
                        <div id="hero-fallback-svg"
                            style="display: none; position: absolute; top: 0; left: 0; width: 100%; height: 100%; align-items: center; justify-content: center; padding: 2rem; background: linear-gradient(135deg, #EBF5FF 0%, #E8F5E9 100%);">
                            <svg style="width: 100%; height: 100%; max-width: 400px; max-height: 400px;"
                                viewBox="0 0 400 400" fill="none" xmlns="http://www.w3.org/2000/svg">
                                {{-- Medical Cross --}}
                                <rect x="160" y="80" width="80" height="240" rx="12" fill="#2563eb"
                                    opacity="0.2" />
                                <rect x="80" y="160" width="240" height="80" rx="12" fill="#10b981"
                                    opacity="0.2" />
                                {{-- Stethoscope Icon --}}
                                <circle cx="200" cy="200" r="60" stroke="#2563eb" stroke-width="8"
                                    fill="none" opacity="0.3" />
                                <text x="200" y="350" text-anchor="middle" fill="#666" font-size="16">Medical
                                    Equipment</text>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Alternative: Animated Medical Equipment Illustration (Backup) --}}
                <div class="hidden">
                    <div class="aspect-square flex items-center justify-center">
                        <svg class="w-full h-full" viewBox="0 0 400 400" fill="none"
                            xmlns="http://www.w3.org/2000/svg">

                            {{-- Animated Stethoscope --}}
                            <g class="animate-fade-in-up" style="animation-delay: 0.2s;">
                                <path
                                    d="M120 80 Q140 60 160 80 L160 180 Q160 220 200 220 Q240 220 240 180 L240 80 Q260 60 280 80"
                                    stroke="#2563eb" stroke-width="8" stroke-linecap="round" fill="none"
                                    opacity="0.4" class="animate-pulse-slow" />
                                <circle cx="120" cy="80" r="15" fill="#2563eb" opacity="0.4"
                                    class="animate-pulse" />
                                <circle cx="280" cy="80" r="15" fill="#2563eb" opacity="0.4"
                                    class="animate-pulse" style="animation-delay: 0.5s;" />
                                <circle cx="200" cy="240" r="25" fill="#2563eb" opacity="0.4"
                                    class="animate-pulse" style="animation-delay: 1s;" />
                            </g>

                            {{-- Animated Medical Cross --}}
                            <g class="animate-fade-in-up" style="animation-delay: 0.4s;">
                                <rect x="180" y="100" width="40" height="120" rx="8" fill="#10b981"
                                    opacity="0.3" class="animate-pulse-slow" />
                                <rect x="140" y="140" width="120" height="40" rx="8" fill="#10b981"
                                    opacity="0.3" class="animate-pulse-slow" style="animation-delay: 0.3s;" />
                            </g>

                            {{-- Animated Heart Monitor Line --}}
                            <g class="animate-fade-in-up" style="animation-delay: 0.6s;">
                                <path d="M50 300 L100 300 L120 280 L140 320 L160 260 L180 300 L350 300"
                                    stroke="#2563eb" stroke-width="4" stroke-linecap="round" fill="none"
                                    opacity="0.35" class="animate-pulse">
                                    <animate attributeName="stroke-dasharray" from="0,1000" to="1000,0"
                                        dur="2s" repeatCount="indefinite" />
                                </path>
                            </g>

                            {{-- Animated Medical Pills --}}
                            <g class="animate-fade-in-up" style="animation-delay: 0.8s;">
                                <ellipse cx="300" cy="150" rx="20" ry="30" fill="#10b981"
                                    opacity="0.3" transform="rotate(45 300 150)" class="animate-bounce"
                                    style="animation-duration: 2s;" />
                                <ellipse cx="330" cy="180" rx="20" ry="30" fill="#2563eb"
                                    opacity="0.3" transform="rotate(45 330 180)" class="animate-bounce"
                                    style="animation-duration: 2s; animation-delay: 0.5s;" />
                            </g>

                            {{-- Animated Syringe --}}
                            <g class="animate-fade-in-up" style="animation-delay: 1s;">
                                <rect x="80" y="200" width="60" height="12" rx="6" fill="#2563eb"
                                    opacity="0.3" class="animate-pulse-slow" />
                                <rect x="70" y="197" width="10" height="18" rx="2" fill="#2563eb"
                                    opacity="0.3" />
                                <circle cx="145" cy="206" r="8" fill="#10b981" opacity="0.3"
                                    class="animate-ping" style="animation-duration: 3s;" />
                            </g>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    {{-- Scroll Indicator --}}
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
        <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>
