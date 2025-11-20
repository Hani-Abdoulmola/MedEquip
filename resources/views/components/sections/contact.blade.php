{{-- Contact Section --}}
<section id="contact" class="py-20 bg-white">
    <div class="container">
        
        {{-- Section Header --}}
        <div class="text-center mb-16 fade-up">
            <div class="inline-flex items-center gap-2 bg-secondary/10 text-secondary px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                </svg>
                <span>تواصل معنا</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                ابدأ رحلتك مع 
                <span class="text-primary">MediTrust</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                تواصل معنا اليوم واحصل على استشارة مجانية لاحتياجاتك من المعدات الطبية
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-16 items-start">
            
            {{-- Contact Form --}}
            <div class="slide-in-right">
                <div class="bg-gradient-to-br from-gray-50 to-white rounded-3xl p-8 shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-6">أرسل لنا رسالة</h3>
                    
                    <form id="contact-form" class="space-y-6">
                        {{-- Name Field --}}
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">الاسم الكامل *</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                   placeholder="أدخل اسمك الكامل">
                        </div>

                        {{-- Email Field --}}
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">البريد الإلكتروني *</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                   placeholder="example@domain.com">
                        </div>

                        {{-- Phone Field --}}
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">رقم الهاتف</label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                   placeholder="+218 91 555 1234">
                        </div>

                        {{-- Organization Field --}}
                        <div>
                            <label for="organization" class="block text-sm font-medium text-gray-700 mb-2">المؤسسة/الشركة</label>
                            <input type="text" 
                                   id="organization" 
                                   name="organization" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200"
                                   placeholder="اسم المؤسسة أو الشركة">
                        </div>

                        {{-- Subject Field --}}
                        <div>
                            <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">الموضوع *</label>
                            <select id="subject" 
                                    name="subject" 
                                    required 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200">
                                <option value="">اختر الموضوع</option>
                                <option value="general">استفسار عام</option>
                                <option value="quote">طلب عرض سعر</option>
                                <option value="supplier">الانضمام كمورد</option>
                                <option value="support">دعم فني</option>
                                <option value="partnership">شراكة تجارية</option>
                            </select>
                        </div>

                        {{-- Message Field --}}
                        <div>
                            <label for="message" class="block text-sm font-medium text-gray-700 mb-2">الرسالة *</label>
                            <textarea id="message" 
                                      name="message" 
                                      rows="5" 
                                      required 
                                      class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-primary focus:border-transparent transition-all duration-200 resize-none"
                                      placeholder="اكتب رسالتك هنا..."></textarea>
                        </div>

                        {{-- Privacy Checkbox --}}
                        <div class="flex items-start gap-3">
                            <input type="checkbox" 
                                   id="privacy" 
                                   name="privacy" 
                                   required 
                                   class="mt-1 w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                            <label for="privacy" class="text-sm text-gray-600">
                                أوافق على <a href="#" class="text-primary hover:underline">سياسة الخصوصية</a> و <a href="#" class="text-primary hover:underline">شروط الاستخدام</a>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <button type="submit" 
                                class="w-full btn btn-primary btn-lg group">
                            <span class="btn-text">إرسال الرسالة</span>
                            <span class="btn-loading hidden">
                                <svg class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                جاري الإرسال...
                            </span>
                            <svg class="w-5 h-5 mr-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Contact Information --}}
            <div class="slide-in-left">
                <div class="space-y-8">
                    
                    {{-- Contact Cards --}}
                    <div class="bg-primary text-white rounded-3xl p-8">
                        <h3 class="text-2xl font-bold mb-6">معلومات التواصل</h3>
                        
                        <div class="space-y-6">
                            {{-- Phone --}}
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">الهاتف</div>
                                    <div class="opacity-90">+218 91 555 1234</div>
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">البريد الإلكتروني</div>
                                    <div class="opacity-90">info@meditrust.ly</div>
                                </div>
                            </div>

                            {{-- Address --}}
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">العنوان</div>
                                    <div class="opacity-90">طرابلس، ليبيا<br>شارع الجمهورية</div>
                                </div>
                            </div>

                            {{-- Working Hours --}}
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="font-semibold">ساعات العمل</div>
                                    <div class="opacity-90">الأحد - الخميس: 8:00 - 17:00<br>دعم فني 24/7</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quick Actions --}}
                    <div class="grid grid-cols-2 gap-4">
                        <a href="tel:+218915551234" class="bg-secondary text-white rounded-2xl p-6 text-center hover:bg-secondary-dark transition-colors group">
                            <svg class="w-8 h-8 mx-auto mb-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            <div class="font-semibold">اتصل بنا</div>
                        </a>
                        
                        <a href="mailto:info@meditrust.ly" class="bg-accent text-white rounded-2xl p-6 text-center hover:bg-accent-dark transition-colors group">
                            <svg class="w-8 h-8 mx-auto mb-3 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <div class="font-semibold">راسلنا</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Contact Section Styles --}}
<style>
    .bg-secondary\/10 {
        background-color: rgba(5, 150, 105, 0.1);
    }
    
    .text-secondary {
        color: var(--secondary-color);
    }
    
    .space-y-6 > * + * {
        margin-top: 1.5rem;
    }
    
    .space-y-8 > * + * {
        margin-top: 2rem;
    }
    
    .focus\:ring-2:focus {
        box-shadow: 0 0 0 2px var(--primary-color);
    }
    
    .focus\:border-transparent:focus {
        border-color: transparent;
    }
    
    .border-gray-300 {
        border-color: var(--gray-300);
    }
    
    .bg-white\/20 {
        background-color: rgba(255, 255, 255, 0.2);
    }
    
    .opacity-90 {
        opacity: 0.9;
    }
    
    .resize-none {
        resize: none;
    }
    
    .hover\:bg-secondary-dark:hover {
        background-color: var(--secondary-dark, #047857);
    }
    
    .hover\:bg-accent-dark:hover {
        background-color: var(--accent-dark, #b91c1c);
    }
    
    .group:hover .group-hover\:scale-110 {
        transform: scale(1.1);
    }
    
    .group:hover .group-hover\:translate-x-1 {
        transform: translateX(0.25rem);
    }
    
    .animate-spin {
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .hidden {
        display: none;
    }
    
    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
</style>
