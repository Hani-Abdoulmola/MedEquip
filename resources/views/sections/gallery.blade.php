{{-- Gallery Section --}}
<section id="gallery" class="py-20 lg:py-28 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up">
            <div class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                </svg>
                <span class="text-sm font-semibold text-medical-blue-700">معرض الأعمال</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-medical-gray-900 mb-6 font-display">
                نماذج من 
                <span class="bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">مشاريعنا</span>
            </h2>
            <p class="text-lg text-medical-gray-600 leading-relaxed">
                اطلع على بعض المشاريع الناجحة التي أنجزناها مع شركائنا في القطاع الصحي
            </p>
        </div>

        {{-- Gallery Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @for($i = 1; $i <= 6; $i++)
            <div class="group relative bg-gradient-to-br from-medical-blue-100 to-medical-green-100 rounded-2xl overflow-hidden shadow-medical-lg hover:shadow-medical-2xl transition-all duration-300 cursor-pointer animate-fade-in-up" style="animation-delay: {{ ($i - 1) * 0.1 }}s;">
                <div class="aspect-[4/3] flex items-center justify-center">
                    <svg class="w-24 h-24 text-medical-blue-600/20" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-medical-gray-900/90 via-medical-gray-900/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-6">
                    <div class="text-white">
                        <h3 class="text-lg font-bold mb-2 font-display">مشروع {{ $i }}</h3>
                        <p class="text-sm text-white/90">توريد معدات طبية متطورة</p>
                    </div>
                </div>
            </div>
            @endfor
        </div>
    </div>
</section>

