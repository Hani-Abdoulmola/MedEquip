{{-- Professional Medical B2B Navigation Bar --}}
<nav class="fixed top-0 right-0 left-0 z-50 transition-all duration-300"
    :class="scrolled ? 'bg-white shadow-medical-lg' : 'bg-white/95 backdrop-blur-sm'" x-data="{ activeSection: 'home' }">

    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">

            {{-- Logo --}}
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 space-x-reverse group">
                    <x-application-logo class="h-12 w-12 shadow-medical" />
                    <div class="flex flex-col">
                        <span
                            class="text-2xl font-bold bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent font-display">
                            MediEquip
                        </span>
                        <span class="text-xs text-medical-gray-600 font-medium -mt-1">منصة المعدات الطبية</span>
                    </div>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <div class="hidden lg:flex lg:items-center lg:space-x-8 lg:space-x-reverse">
                <a href="#home"
                    @click.prevent="activeSection = 'home'; document.getElementById('home').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'home' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    الرئيسية
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'home' ? 'w-full' : ''"></span>
                </a>
                <a href="#about"
                    @click.prevent="activeSection = 'about'; document.getElementById('about').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'about' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    من نحن
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'about' ? 'w-full' : ''"></span>
                </a>
                <a href="#services"
                    @click.prevent="activeSection = 'services'; document.getElementById('services').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'services' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    الخدمات
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'services' ? 'w-full' : ''"></span>
                </a>
                <a href="#categories"
                    @click.prevent="activeSection = 'categories'; document.getElementById('categories').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'categories' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    الفئات
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'categories' ? 'w-full' : ''"></span>
                </a>
                <a href="#partners"
                    @click.prevent="activeSection = 'partners'; document.getElementById('partners').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'partners' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    الشركاء
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'team' ? 'w-full' : ''"></span>
                </a>
                <a href="#contact"
                    @click.prevent="activeSection = 'contact'; document.getElementById('contact').scrollIntoView({ behavior: 'smooth' })"
                    :class="activeSection === 'contact' ? 'text-medical-blue-600 font-semibold' :
                        'text-medical-gray-700 hover:text-medical-blue-600'"
                    class="text-base font-medium transition-colors duration-200 relative group">
                    اتصل بنا
                    <span
                        class="absolute bottom-0 right-0 w-0 h-0.5 bg-medical-blue-600 group-hover:w-full transition-all duration-300"
                        :class="activeSection === 'contact' ? 'w-full' : ''"></span>
                </a>
            </div>

            {{-- CTA Buttons --}}
            <div class="hidden lg:flex lg:items-center lg:space-x-4 lg:space-x-reverse">
                <a href="{{ route('login') }}"
                    class="px-5 py-2.5 text-medical-blue-600 font-semibold hover:text-medical-blue-700 transition-colors duration-200">
                    تسجيل الدخول
                </a>
                <a href="{{ route('register') }}"
                    class="px-6 py-2.5 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-semibold rounded-xl shadow-medical hover:shadow-medical-lg hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                    ابدأ الآن
                </a>
            </div>

            {{-- Mobile Menu Button --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden p-2 rounded-lg text-medical-gray-700 hover:bg-medical-gray-100 focus:outline-none focus:ring-2 focus:ring-medical-blue-500 transition-colors duration-200"
                aria-label="القائمة">
                <svg x-show="!mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="mobileMenuOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                    style="display: none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu --}}
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform -translate-y-4"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform translate-y-0"
        x-transition:leave-end="opacity-0 transform -translate-y-4"
        class="lg:hidden bg-white border-t border-medical-gray-200 shadow-medical-xl" style="display: none;">
        <div class="container mx-auto px-4 py-6 space-y-4">
            <a href="#home"
                @click="mobileMenuOpen = false; document.getElementById('home').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                الرئيسية
            </a>
            <a href="#about"
                @click="mobileMenuOpen = false; document.getElementById('about').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                من نحن
            </a>
            <a href="#services"
                @click="mobileMenuOpen = false; document.getElementById('services').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                الخدمات
            </a>
            <a href="#categories"
                @click="mobileMenuOpen = false; document.getElementById('categories').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                الفئات
            </a>
            <a href="#team"
                @click="mobileMenuOpen = false; document.getElementById('team').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                الفريق
            </a>
            <a href="#contact"
                @click="mobileMenuOpen = false; document.getElementById('contact').scrollIntoView({ behavior: 'smooth' })"
                class="block px-4 py-3 text-base font-medium text-medical-gray-700 hover:bg-medical-blue-50 hover:text-medical-blue-600 rounded-lg transition-colors duration-200">
                اتصل بنا
            </a>
            <div class="pt-4 border-t border-medical-gray-200 space-y-3">
                <a href="{{ route('login') }}"
                    class="block px-4 py-3 text-center text-medical-blue-600 font-semibold hover:bg-medical-blue-50 rounded-lg transition-colors duration-200">
                    تسجيل الدخول
                </a>
                <a href="{{ route('register') }}"
                    class="block px-4 py-3 text-center bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-semibold rounded-xl shadow-medical">
                    ابدأ الآن
                </a>
            </div>
        </div>
    </div>
</nav>
