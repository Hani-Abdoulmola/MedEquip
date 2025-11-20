{{-- Product Categories Showcase Section --}}
<section id="categories" class="py-20 bg-white">
    <div class="container">
        
        {{-- Section Header --}}
        <div class="text-center mb-16 fade-up">
            <div class="inline-flex items-center gap-2 bg-accent/10 text-accent px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                </svg>
                <span>أقسام المنتجات</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                تشكيلة شاملة من 
                <span class="text-primary">المعدات الطبية</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                نوفر جميع أنواع المعدات والأجهزة الطبية من أفضل الموردين العالميين والمحليين لتلبية احتياجات جميع المؤسسات الطبية
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            
            {{-- Diagnostic Equipment --}}
            <div class="group bg-gradient-to-br from-blue-50 to-blue-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-left">
                <div class="w-16 h-16 bg-blue-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">أجهزة التشخيص</h3>
                <p class="text-gray-600 mb-6">أحدث أجهزة التشخيص والفحص الطبي من الماركات العالمية المعتمدة</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أشعة سينية</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">الموجات فوق الصوتية</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">التصوير المقطعي</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">200+ منتج</span>
                    <svg class="w-5 h-5 text-blue-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>

            {{-- Surgical Equipment --}}
            <div class="group bg-gradient-to-br from-green-50 to-green-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-up">
                <div class="w-16 h-16 bg-green-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">معدات الجراحة</h3>
                <p class="text-gray-600 mb-6">أدوات ومعدات جراحية متطورة لجميع التخصصات الطبية</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أدوات جراحية</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">مناظير</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">ليزر طبي</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">350+ منتج</span>
                    <svg class="w-5 h-5 text-green-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>

            {{-- Laboratory Equipment --}}
            <div class="group bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-right">
                <div class="w-16 h-16 bg-purple-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">معدات المختبرات</h3>
                <p class="text-gray-600 mb-6">أجهزة ومعدات مختبرية دقيقة للتحاليل والفحوصات المخبرية</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أجهزة تحليل</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">مجاهر</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أجهزة قياس</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">180+ منتج</span>
                    <svg class="w-5 h-5 text-purple-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>

            {{-- Patient Monitoring --}}
            <div class="group bg-gradient-to-br from-red-50 to-red-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-left">
                <div class="w-16 h-16 bg-red-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">أجهزة المراقبة</h3>
                <p class="text-gray-600 mb-6">أنظمة مراقبة المرضى والعلامات الحيوية المتطورة</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">مراقبة القلب</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">قياس الضغط</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أجهزة إنذار</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">120+ منتج</span>
                    <svg class="w-5 h-5 text-red-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>

            {{-- Rehabilitation Equipment --}}
            <div class="group bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-up">
                <div class="w-16 h-16 bg-yellow-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">معدات التأهيل</h3>
                <p class="text-gray-600 mb-6">أجهزة العلاج الطبيعي وإعادة التأهيل الحديثة</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">علاج طبيعي</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">تمارين</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">تحفيز كهربائي</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">90+ منتج</span>
                    <svg class="w-5 h-5 text-yellow-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>

            {{-- Emergency Equipment --}}
            <div class="group bg-gradient-to-br from-orange-50 to-orange-100 rounded-3xl p-8 hover:shadow-xl transition-all duration-300 cursor-pointer slide-in-right">
                <div class="w-16 h-16 bg-orange-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">معدات الطوارئ</h3>
                <p class="text-gray-600 mb-6">أجهزة الإسعاف والطوارئ الطبية للحالات الحرجة</p>
                <div class="flex flex-wrap gap-2 mb-6">
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أجهزة إنعاش</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">مزيل رجفان</span>
                    <span class="px-3 py-1 bg-white/70 rounded-full text-sm text-gray-700">أجهزة تنفس</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-500">75+ منتج</span>
                    <svg class="w-5 h-5 text-orange-500 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </div>
            </div>
        </div>

        {{-- CTA Section --}}
        <div class="text-center bg-gradient-to-r from-primary to-secondary rounded-3xl p-12 text-white fade-up">
            <h3 class="text-3xl font-bold mb-4">لم تجد ما تبحث عنه؟</h3>
            <p class="text-xl mb-8 opacity-90">تواصل معنا وسنساعدك في العثور على المعدات المناسبة لاحتياجاتك</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="#contact" class="btn bg-white text-primary hover:bg-gray-100 px-8 py-3">
                    طلب استشارة مجانية
                </a>
                <a href="#contact" class="btn btn-outline border-white text-white hover:bg-white hover:text-primary px-8 py-3">
                    تصفح الكتالوج الكامل
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Categories Section Styles --}}
<style>
    .bg-accent\/10 {
        background-color: rgba(220, 38, 38, 0.1);
    }
    
    .text-accent {
        color: var(--accent-color);
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(0.25rem);
    }
    
    .bg-white\/70 {
        background-color: rgba(255, 255, 255, 0.7);
    }
    
    .opacity-90 {
        opacity: 0.9;
    }
    
    .hover\:bg-gray-100:hover {
        background-color: var(--gray-100);
    }
    
    .border-white {
        border-color: white;
    }
    
    .hover\:bg-white:hover {
        background-color: white;
    }
    
    .hover\:text-primary:hover {
        color: var(--primary-color);
    }
</style>
