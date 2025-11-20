{{-- Footer Component --}}
<footer class="bg-gray-900 text-white">
    
    {{-- Main Footer Content --}}
    <div class="container py-16">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            
            {{-- Company Info --}}
            <div class="lg:col-span-2">
                <div class="flex items-center gap-3 mb-6">
                    <img src="{{ asset('assets/img/logo.webp') }}" alt="MediTrust Logo" class="h-10 w-auto" width="120" height="40">
                    <span class="text-2xl font-bold">MediTrust</span>
                </div>
                <p class="text-gray-300 mb-6 leading-relaxed max-w-md">
                    منصة رائدة في تجارة المعدات والأجهزة الطبية، نربط الموردين بالمؤسسات الطبية في العالم العربي من خلال حلول رقمية مبتكرة وآمنة.
                </p>
                
                {{-- Social Media Links --}}
                <div class="flex gap-4">
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors" aria-label="فيسبوك">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors" aria-label="تويتر">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors" aria-label="لينكد إن">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                        </svg>
                    </a>
                    <a href="#" class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-primary transition-colors" aria-label="إنستغرام">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C3.85 14.81 3.85 12.455 3.85 12.017s0-2.793 1.276-3.674c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c1.276.881 1.276 3.236 1.276 3.674s0 2.793-1.276 3.674c-.875.807-2.026 1.297-3.323 1.297zm7.119 0c-1.297 0-2.448-.49-3.323-1.297-1.276-.881-1.276-3.236-1.276-3.674s0-2.793 1.276-3.674c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c1.276.881 1.276 3.236 1.276 3.674s0 2.793-1.276 3.674c-.875.807-2.026 1.297-3.323 1.297z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Quick Links --}}
            <div>
                <h3 class="text-lg font-semibold mb-6">روابط سريعة</h3>
                <ul class="space-y-3">
                    <li><a href="#hero" class="text-gray-300 hover:text-white transition-colors">الرئيسية</a></li>
                    <li><a href="#about" class="text-gray-300 hover:text-white transition-colors">من نحن</a></li>
                    <li><a href="#services" class="text-gray-300 hover:text-white transition-colors">الخدمات</a></li>
                    <li><a href="#categories" class="text-gray-300 hover:text-white transition-colors">الأقسام</a></li>
                    <li><a href="#team" class="text-gray-300 hover:text-white transition-colors">الفريق</a></li>
                    <li><a href="#gallery" class="text-gray-300 hover:text-white transition-colors">المعرض</a></li>
                    <li><a href="#faq" class="text-gray-300 hover:text-white transition-colors">الأسئلة الشائعة</a></li>
                    <li><a href="#contact" class="text-gray-300 hover:text-white transition-colors">تواصل معنا</a></li>
                </ul>
            </div>

            {{-- Services --}}
            <div>
                <h3 class="text-lg font-semibold mb-6">خدماتنا</h3>
                <ul class="space-y-3">
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">أجهزة التشخيص</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">معدات الجراحة</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">معدات المختبرات</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">أجهزة المراقبة</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">معدات التأهيل</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">معدات الطوارئ</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">الدعم الفني</a></li>
                    <li><a href="#" class="text-gray-300 hover:text-white transition-colors">التدريب</a></li>
                </ul>
            </div>
        </div>

        {{-- Newsletter Subscription --}}
        <div class="border-t border-gray-800 mt-12 pt-12">
            <div class="grid md:grid-cols-2 gap-8 items-center">
                <div>
                    <h3 class="text-2xl font-bold mb-4">اشترك في نشرتنا الإخبارية</h3>
                    <p class="text-gray-300">احصل على آخر الأخبار والعروض الخاصة في مجال المعدات الطبية</p>
                </div>
                <div>
                    <form class="flex gap-3">
                        <input type="email" 
                               placeholder="أدخل بريدك الإلكتروني" 
                               class="flex-1 px-4 py-3 bg-gray-800 border border-gray-700 rounded-xl text-white placeholder-gray-400 focus:ring-2 focus:ring-primary focus:border-transparent">
                        <button type="submit" class="btn btn-primary px-6 py-3 whitespace-nowrap">
                            اشترك
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom Footer --}}
    <div class="border-t border-gray-800">
        <div class="container py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-gray-400 text-sm">
                    © {{ date('Y') }} MediTrust. جميع الحقوق محفوظة.
                </div>
                <div class="flex gap-6 text-sm">
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">سياسة الخصوصية</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">شروط الاستخدام</a>
                    <a href="#" class="text-gray-400 hover:text-white transition-colors">ملفات تعريف الارتباط</a>
                </div>
            </div>
        </div>
    </div>
</footer>

{{-- Footer Styles --}}
<style>
    .bg-gray-900 {
        background-color: var(--gray-900);
    }
    
    .bg-gray-800 {
        background-color: var(--gray-800);
    }
    
    .bg-gray-700 {
        background-color: var(--gray-700);
    }
    
    .text-gray-300 {
        color: var(--gray-300);
    }
    
    .text-gray-400 {
        color: var(--gray-400);
    }
    
    .border-gray-800 {
        border-color: var(--gray-800);
    }
    
    .border-gray-700 {
        border-color: var(--gray-700);
    }
    
    .placeholder-gray-400::placeholder {
        color: var(--gray-400);
    }
    
    .space-y-3 > * + * {
        margin-top: 0.75rem;
    }
    
    .lg\:col-span-2 {
        grid-column: span 2 / span 2;
    }
    
    .max-w-md {
        max-width: 28rem;
    }
    
    .w-auto {
        width: auto;
    }
    
    .h-10 {
        height: 2.5rem;
    }
    
    .w-10 {
        width: 2.5rem;
    }
    
    .rounded-lg {
        border-radius: 0.5rem;
    }
    
    .rounded-xl {
        border-radius: 0.75rem;
    }
    
    .border-t {
        border-top-width: 1px;
    }
    
    .pt-12 {
        padding-top: 3rem;
    }
    
    .mt-12 {
        margin-top: 3rem;
    }
    
    .flex-1 {
        flex: 1 1 0%;
    }
    
    .whitespace-nowrap {
        white-space: nowrap;
    }
    
    .transition-colors {
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }
    
    .hover\:bg-primary:hover {
        background-color: var(--primary-color);
    }
    
    .hover\:text-white:hover {
        color: white;
    }
    
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--primary-color);
    }
    
    .focus\:border-transparent:focus {
        border-color: transparent;
    }
    
    @media (min-width: 768px) {
        .md\:grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        
        .md\:flex-row {
            flex-direction: row;
        }
    }
    
    @media (min-width: 1024px) {
        .lg\:grid-cols-4 {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }
    }
</style>
