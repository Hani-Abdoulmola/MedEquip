{{-- Modern Responsive Navigation Component --}}
<nav class="navbar" role="navigation" aria-label="الملاحة الرئيسية">
    <div class="container">
        <div class="flex items-center justify-between py-4">
            
            {{-- Brand Logo --}}
            <div class="navbar-brand-container">
                <a href="{{ url('/') }}" class="navbar-brand flex items-center gap-3" aria-label="MediTrust - الصفحة الرئيسية">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="MediTrust Logo" class="h-10 w-auto" width="120" height="40">
                    <span class="font-bold text-xl text-gray-900 hidden sm:block">MediTrust</span>
                </a>
            </div>

            {{-- Desktop Navigation --}}
            <ul class="navbar-nav hidden md:flex" role="menubar">
                <li role="none">
                    <a href="#hero" class="nav-link active" role="menuitem" aria-current="page">
                        الرئيسية
                    </a>
                </li>
                <li role="none">
                    <a href="#about" class="nav-link" role="menuitem">
                        من نحن
                    </a>
                </li>
                <li role="none">
                    <a href="#services" class="nav-link" role="menuitem">
                        الخدمات
                    </a>
                </li>
                <li role="none">
                    <a href="#categories" class="nav-link" role="menuitem">
                        الأقسام
                    </a>
                </li>
                <li role="none">
                    <a href="#team" class="nav-link" role="menuitem">
                        الفريق
                    </a>
                </li>
                <li role="none">
                    <a href="#gallery" class="nav-link" role="menuitem">
                        المعرض
                    </a>
                </li>
                <li role="none">
                    <a href="#faq" class="nav-link" role="menuitem">
                        الأسئلة الشائعة
                    </a>
                </li>
                <li role="none">
                    <a href="#contact" class="nav-link" role="menuitem">
                        تواصل معنا
                    </a>
                </li>
            </ul>

            {{-- CTA Button (Desktop) --}}
            <div class="hidden md:flex items-center gap-4">
                <a href="#contact" class="btn btn-outline btn-sm">
                    طلب عرض سعر
                </a>
                <a href="#contact" class="btn btn-primary btn-sm">
                    ابدأ الآن
                </a>
            </div>

            {{-- Mobile Menu Toggle --}}
            <button class="mobile-menu-toggle md:hidden" 
                    type="button" 
                    aria-label="فتح القائمة الرئيسية"
                    aria-expanded="false"
                    aria-controls="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        {{-- Mobile Navigation Menu --}}
        <div id="mobile-menu" class="navbar-nav md:hidden" role="menu" aria-labelledby="mobile-menu-toggle">
            <div class="py-4 border-t border-gray-200">
                <ul class="flex flex-col gap-2" role="none">
                    <li role="none">
                        <a href="#hero" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50 active" role="menuitem">
                            الرئيسية
                        </a>
                    </li>
                    <li role="none">
                        <a href="#about" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            من نحن
                        </a>
                    </li>
                    <li role="none">
                        <a href="#services" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            الخدمات
                        </a>
                    </li>
                    <li role="none">
                        <a href="#categories" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            الأقسام
                        </a>
                    </li>
                    <li role="none">
                        <a href="#team" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            الفريق
                        </a>
                    </li>
                    <li role="none">
                        <a href="#gallery" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            المعرض
                        </a>
                    </li>
                    <li role="none">
                        <a href="#faq" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            الأسئلة الشائعة
                        </a>
                    </li>
                    <li role="none">
                        <a href="#contact" class="nav-link block py-3 px-4 rounded-lg hover:bg-gray-50" role="menuitem">
                            تواصل معنا
                        </a>
                    </li>
                </ul>
                
                {{-- Mobile CTA Buttons --}}
                <div class="flex flex-col gap-3 mt-6 px-4">
                    <a href="#contact" class="btn btn-outline w-full justify-center">
                        طلب عرض سعر
                    </a>
                    <a href="#contact" class="btn btn-primary w-full justify-center">
                        ابدأ الآن
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>

{{-- Additional Navbar Styles --}}
<style>
    /* Navbar specific styles */
    .navbar-brand-container {
        flex-shrink: 0;
    }
    
    .navbar-brand:hover {
        text-decoration: none;
        color: var(--primary-color);
    }
    
    /* Desktop navigation spacing */
    .navbar-nav.hidden.md\:flex {
        gap: 2rem;
    }
    
    /* Mobile menu styles */
    .mobile-menu-toggle {
        width: 32px;
        height: 32px;
        position: relative;
        z-index: 1001;
    }
    
    .mobile-menu-toggle span {
        display: block;
        width: 20px;
        height: 2px;
        background-color: var(--gray-700);
        margin: 4px 0;
        transition: all 0.3s ease;
        transform-origin: center;
    }
    
    /* Mobile menu animation */
    .navbar-nav.md\:hidden {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
    }
    
    .navbar-nav.md\:hidden.active {
        max-height: 500px;
    }
    
    /* Responsive utilities */
    @media (max-width: 768px) {
        .hidden.md\:flex {
            display: none !important;
        }
        
        .md\:hidden {
            display: block;
        }
        
        .navbar-nav.md\:hidden {
            display: block;
        }
    }
    
    @media (min-width: 769px) {
        .hidden.md\:flex {
            display: flex !important;
        }
        
        .md\:hidden {
            display: none !important;
        }
    }
    
    /* Hover effects */
    .hover\:bg-gray-50:hover {
        background-color: var(--gray-50);
    }
    
    .w-auto {
        width: auto;
    }
    
    .h-10 {
        height: 2.5rem;
    }
    
    .gap-3 {
        gap: 0.75rem;
    }
    
    .gap-2 {
        gap: 0.5rem;
    }
    
    .border-t {
        border-top-width: 1px;
    }
    
    .justify-center {
        justify-content: center;
    }
    
    .w-full {
        width: 100%;
    }
    
    .mt-6 {
        margin-top: 1.5rem;
    }
</style>
