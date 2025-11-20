{{-- Gallery/Showcase Section --}}
<section id="gallery" class="py-20 bg-white">
    <div class="container">
        
        {{-- Section Header --}}
        <div class="text-center mb-16 fade-up">
            <div class="inline-flex items-center gap-2 bg-primary/10 text-primary px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
                <span>معرض الأعمال</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                مشاريعنا وإنجازاتنا
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                استعرض مجموعة من أفضل المشاريع والمعدات الطبية التي تم توريدها وتركيبها بنجاح في مختلف المؤسسات الطبية
            </p>
        </div>

        {{-- Gallery Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            
            {{-- Gallery Item 1 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-1.webp') }}">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-1.webp') }}" 
                         alt="مستشفى الملك فيصل - أجهزة التصوير المقطعي" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">مستشفى الملك فيصل</h3>
                        <p class="text-sm opacity-90">أجهزة التصوير المقطعي المتطورة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Gallery Item 2 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-2.webp') }}" style="animation-delay: 0.1s;">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-2.webp') }}" 
                         alt="مركز الأورام - معدات العلاج الإشعاعي" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">مركز الأورام</h3>
                        <p class="text-sm opacity-90">معدات العلاج الإشعاعي الحديثة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Gallery Item 3 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-3.webp') }}" style="animation-delay: 0.2s;">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-3.webp') }}" 
                         alt="مختبرات التحاليل - أجهزة التحليل الآلي" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">مختبرات التحاليل</h3>
                        <p class="text-sm opacity-90">أجهزة التحليل الآلي المتطورة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Gallery Item 4 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-4.webp') }}" style="animation-delay: 0.3s;">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-4.webp') }}" 
                         alt="غرف العمليات - أنظمة التهوية والإضاءة" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">غرف العمليات</h3>
                        <p class="text-sm opacity-90">أنظمة التهوية والإضاءة المتخصصة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Gallery Item 5 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-5.webp') }}" style="animation-delay: 0.4s;">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-5.webp') }}" 
                         alt="وحدة العناية المركزة - أجهزة المراقبة" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">وحدة العناية المركزة</h3>
                        <p class="text-sm opacity-90">أجهزة المراقبة والإنعاش المتطورة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Gallery Item 6 --}}
            <div class="gallery-item group cursor-pointer fade-up" data-src="{{ asset('assets/img/gallery/gallery-6.webp') }}" style="animation-delay: 0.5s;">
                <div class="relative overflow-hidden rounded-2xl shadow-lg group-hover:shadow-xl transition-all duration-300">
                    <img src="{{ asset('assets/img/gallery/gallery-6.webp') }}" 
                         alt="قسم الطوارئ - معدات الإسعاف والإنقاذ" 
                         class="w-full h-64 object-cover group-hover:scale-110 transition-transform duration-500"
                         width="400" 
                         height="256">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    <div class="absolute bottom-4 left-4 right-4 text-white transform translate-y-4 group-hover:translate-y-0 transition-transform duration-300 opacity-0 group-hover:opacity-100">
                        <h3 class="text-lg font-semibold mb-1">قسم الطوارئ</h3>
                        <p class="text-sm opacity-90">معدات الإسعاف والإنقاذ المتخصصة</p>
                    </div>
                    <div class="absolute top-4 right-4 w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistics --}}
        <div class="grid md:grid-cols-4 gap-8 bg-gradient-to-r from-primary to-secondary rounded-3xl p-12 text-white fade-up">
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">500+</div>
                <div class="text-lg opacity-90">مشروع مكتمل</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">1000+</div>
                <div class="text-lg opacity-90">جهاز مركب</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">200+</div>
                <div class="text-lg opacity-90">مؤسسة طبية</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold mb-2">99.8%</div>
                <div class="text-lg opacity-90">معدل رضا العملاء</div>
            </div>
        </div>
    </div>
</section>

{{-- Lightbox Modal --}}
<div id="lightbox" class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center opacity-0 invisible transition-all duration-300">
    <div class="relative max-w-4xl max-h-full p-4">
        <img id="lightbox-image" src="" alt="" class="max-w-full max-h-full object-contain rounded-lg">
        <button id="lightbox-close" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
        <div id="lightbox-caption" class="absolute -bottom-12 left-0 right-0 text-white text-center"></div>
    </div>
</div>

{{-- Gallery Section Styles --}}
<style>
    .bg-primary\/10 {
        background-color: rgba(37, 99, 235, 0.1);
    }
    
    .h-64 {
        height: 16rem;
    }
    
    .bg-black\/60 {
        background-color: rgba(0, 0, 0, 0.6);
    }
    
    .bg-white\/20 {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .backdrop-blur-sm {
        backdrop-filter: blur(4px);
    }
    
    .translate-y-4 {
        transform: translateY(1rem);
    }
    
    .group:hover .group-hover\:translate-y-0 {
        transform: translateY(0);
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .group:hover .group-hover\:opacity-100 {
        opacity: 1;
    }
    
    .group:hover .group-hover\:shadow-xl {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
    }
    
    .bg-black\/90 {
        background-color: rgba(0, 0, 0, 0.9);
    }
    
    .max-w-4xl {
        max-width: 56rem;
    }
    
    .max-h-full {
        max-height: 100%;
    }
    
    .object-contain {
        object-fit: contain;
    }
    
    .-top-12 {
        top: -3rem;
    }
    
    .-bottom-12 {
        bottom: -3rem;
    }
    
    .hover\:text-gray-300:hover {
        color: #d1d5db;
    }
</style>
