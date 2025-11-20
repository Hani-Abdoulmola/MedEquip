{{-- Team/Trust Indicators Section --}}
<section id="team" class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="container">
        
        {{-- Section Header --}}
        <div class="text-center mb-16 fade-up">
            <div class="inline-flex items-center gap-2 bg-secondary/10 text-secondary px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                </svg>
                <span>فريق الخبراء</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                خبراء متخصصون في 
                <span class="text-primary">المعدات الطبية</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                فريق من المتخصصين والخبراء في مجال المعدات الطبية يعملون على مدار الساعة لضمان حصولك على أفضل الحلول
            </p>
        </div>

        {{-- Team Grid --}}
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8 mb-20">
            
            {{-- Team Member 1 --}}
            <div class="text-center group slide-in-left">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <img src="{{ asset('assets/img/doctors/doctors-1.jpg') }}" 
                             alt="د. أحمد محمد - مدير المبيعات الطبية" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                             width="128" 
                             height="128">
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">د. أحمد محمد</h3>
                <p class="text-primary font-medium mb-3">مدير المبيعات الطبية</p>
                <p class="text-gray-600 text-sm mb-4">خبرة 15 عاماً في مجال المعدات الطبية والتشخيصية</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Team Member 2 --}}
            <div class="text-center group slide-in-up" style="animation-delay: 0.1s;">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <img src="{{ asset('assets/img/doctors/doctors-2.jpg') }}" 
                             alt="د. فاطمة علي - مديرة الجودة والاعتماد" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                             width="128" 
                             height="128">
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">د. فاطمة علي</h3>
                <p class="text-secondary font-medium mb-3">مديرة الجودة والاعتماد</p>
                <p class="text-gray-600 text-sm mb-4">متخصصة في معايير الجودة الطبية والاعتماد الدولي</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-secondary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-secondary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Team Member 3 --}}
            <div class="text-center group slide-in-up" style="animation-delay: 0.2s;">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <img src="{{ asset('assets/img/doctors/doctors-3.jpg') }}" 
                             alt="م. خالد حسن - مدير التقنية والدعم" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                             width="128" 
                             height="128">
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">م. خالد حسن</h3>
                <p class="text-accent font-medium mb-3">مدير التقنية والدعم</p>
                <p class="text-gray-600 text-sm mb-4">مهندس طبي متخصص في صيانة وتشغيل المعدات الطبية</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-accent hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-accent hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Team Member 4 --}}
            <div class="text-center group slide-in-right">
                <div class="relative mb-6">
                    <div class="w-32 h-32 mx-auto rounded-full overflow-hidden shadow-lg group-hover:shadow-xl transition-all duration-300">
                        <img src="{{ asset('assets/img/doctors/doctors-4.jpg') }}" 
                             alt="د. سارة أحمد - مديرة خدمة العملاء" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                             width="128" 
                             height="128">
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">د. سارة أحمد</h3>
                <p class="text-primary font-medium mb-3">مديرة خدمة العملاء</p>
                <p class="text-gray-600 text-sm mb-4">متخصصة في إدارة العلاقات مع العملاء والدعم الفني</p>
                <div class="flex justify-center gap-3">
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-6-3a2 2 0 11-4 0 2 2 0 014 0zm-2 4a5 5 0 00-4.546 2.916A5.986 5.986 0 0010 16a5.986 5.986 0 004.546-2.084A5 5 0 0010 11z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                    <a href="#" class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center hover:bg-primary hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        {{-- Trust Indicators --}}
        <div class="bg-white rounded-3xl p-12 shadow-lg fade-up">
            <h3 class="text-3xl font-bold text-center text-gray-900 mb-12">مؤشرات الثقة والجودة</h3>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                {{-- Certification --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">معتمد ISO 13485</h4>
                    <p class="text-gray-600">شهادة الجودة العالمية للمعدات الطبية</p>
                </div>

                {{-- Security --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-secondary/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">أمان البيانات</h4>
                    <p class="text-gray-600">حماية متقدمة لبيانات العملاء والمعاملات</p>
                </div>

                {{-- Support --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-accent/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">دعم 24/7</h4>
                    <p class="text-gray-600">فريق دعم متاح على مدار الساعة</p>
                </div>

                {{-- Warranty --}}
                <div class="text-center">
                    <div class="w-20 h-20 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                        </svg>
                    </div>
                    <h4 class="text-xl font-semibold text-gray-900 mb-2">ضمان شامل</h4>
                    <p class="text-gray-600">ضمان على جميع المعدات وخدمات ما بعد البيع</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Team Section Styles --}}
<style>
    .bg-secondary\/10 {
        background-color: rgba(5, 150, 105, 0.1);
    }
    
    .text-secondary {
        color: var(--secondary-color);
    }
    
    .text-accent {
        color: var(--accent-color);
    }
    
    .w-32 {
        width: 8rem;
    }
    
    .h-32 {
        height: 8rem;
    }
    
    .w-20 {
        width: 5rem;
    }
    
    .h-20 {
        height: 5rem;
    }
    
    .w-10 {
        width: 2.5rem;
    }
    
    .h-10 {
        height: 2.5rem;
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .group:hover .group-hover\:shadow-xl {
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 10px 10px -5px rgb(0 0 0 / 0.04);
    }
    
    .hover\:bg-secondary:hover {
        background-color: var(--secondary-color);
    }
    
    .hover\:bg-accent:hover {
        background-color: var(--accent-color);
    }
    
    .bg-yellow-100 {
        background-color: #fef3c7;
    }
    
    .text-yellow-600 {
        color: #d97706;
    }
    
    .transition-colors {
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }
</style>
