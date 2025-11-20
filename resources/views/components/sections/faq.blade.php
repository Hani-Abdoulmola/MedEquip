{{-- FAQ Section --}}
<section id="faq" class="py-20 bg-gradient-to-br from-gray-50 to-white">
    <div class="container">
        
        {{-- Section Header --}}
        <div class="text-center mb-16 fade-up">
            <div class="inline-flex items-center gap-2 bg-accent/10 text-accent px-4 py-2 rounded-full text-sm font-medium mb-4">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"/>
                </svg>
                <span>الأسئلة الشائعة</span>
            </div>
            <h2 class="text-4xl lg:text-5xl font-bold text-gray-900 mb-6">
                أسئلة يطرحها العملاء
                <span class="text-primary">بكثرة</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto leading-relaxed">
                إجابات شاملة على أهم الأسئلة حول منصة MediTrust وخدماتنا في مجال المعدات الطبية
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            {{-- FAQ Items --}}
            <div class="space-y-4">
                
                {{-- FAQ Item 1 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">ما هي منصة MediTrust وكيف تعمل؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>منصة MediTrust هي منصة رقمية متخصصة تربط بين موردي المعدات الطبية والمؤسسات الصحية في العالم العربي. نوفر كتالوجاً شاملاً يضم آلاف المنتجات الطبية من موردين معتمدين، مع إمكانية مقارنة الأسعار وطلب عروض الأسعار والحصول على الدعم الفني المتخصص.</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ Item 2 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up" style="animation-delay: 0.1s;">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">كيف يمكنني التأكد من جودة المعدات المعروضة؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>جميع الموردين على منصتنا معتمدون ومراجعون بعناية. نطلب شهادات الجودة الدولية مثل ISO 13485 وشهادات CE وFDA حسب نوع المعدات. كما نوفر تقييمات العملاء السابقين وضمانات شاملة على جميع المنتجات مع خدمات ما بعد البيع.</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ Item 3 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up" style="animation-delay: 0.2s;">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">ما هي تكلفة استخدام المنصة؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>التسجيل واستخدام المنصة مجاني تماماً للمؤسسات الطبية. يمكنك تصفح الكتالوج وطلب عروض الأسعار والتواصل مع الموردين دون أي رسوم. نحصل على عمولة بسيطة من الموردين عند إتمام الصفقات، مما يضمن استمرارية الخدمة وتطويرها.</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ Item 4 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up" style="animation-delay: 0.3s;">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">كم يستغرق تسليم المعدات بعد الطلب؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>مدة التسليم تختلف حسب نوع المعدات وموقع المورد. المعدات المتوفرة محلياً تستغرق 3-7 أيام عمل، بينما المعدات المستوردة قد تستغرق 2-6 أسابيع. نوفر تتبعاً مباشراً للطلبات ونبقيك على اطلاع بكل التطورات من لحظة الطلب حتى التسليم والتركيب.</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ Item 5 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up" style="animation-delay: 0.4s;">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">هل تقدمون خدمات التدريب والصيانة؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>نعم، نوفر خدمات شاملة تشمل التركيب والتدريب والصيانة الدورية. فريقنا من المهندسين المتخصصين يقوم بتدريب طاقمكم على استخدام المعدات بكفاءة وأمان. كما نوفر عقود صيانة مرنة تضمن الأداء الأمثل للمعدات على المدى الطويل.</p>
                        </div>
                    </div>
                </div>

                {{-- FAQ Item 6 --}}
                <div class="faq-item bg-white rounded-2xl shadow-sm border border-gray-100 fade-up" style="animation-delay: 0.5s;">
                    <button class="faq-question w-full text-left p-6 flex items-center justify-between hover:bg-gray-50 transition-colors rounded-2xl" 
                            type="button" 
                            aria-expanded="false">
                        <span class="text-lg font-semibold text-gray-900 pr-4">كيف يمكنني الانضمام كمورد للمنصة؟</span>
                        <svg class="faq-icon w-6 h-6 text-primary transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>
                    <div class="faq-answer max-h-0 overflow-hidden transition-all duration-300">
                        <div class="p-6 pt-0 text-gray-600 leading-relaxed">
                            <p>للانضمام كمورد، يرجى تعبئة نموذج التسجيل وتقديم المستندات المطلوبة مثل السجل التجاري وشهادات الجودة وقائمة المنتجات. فريقنا سيراجع طلبك خلال 3-5 أيام عمل وسيتواصل معك لإكمال عملية التفعيل والبدء في عرض منتجاتك.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact CTA --}}
            <div class="text-center mt-16 fade-up">
                <div class="bg-white rounded-3xl p-8 shadow-lg border border-gray-100">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">لم تجد إجابة لسؤالك؟</h3>
                    <p class="text-gray-600 mb-6">فريق الدعم متاح على مدار الساعة للإجابة على جميع استفساراتك</p>
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="#contact" class="btn btn-primary">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            تواصل معنا
                        </a>
                        <a href="tel:+218915551234" class="btn btn-outline">
                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            اتصل بنا
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ Section Styles --}}
<style>
    .bg-accent\/10 {
        background-color: rgba(220, 38, 38, 0.1);
    }
    
    .text-accent {
        color: var(--accent-color);
    }
    
    .space-y-4 > * + * {
        margin-top: 1rem;
    }
    
    .max-w-4xl {
        max-width: 56rem;
    }
    
    .border-gray-100 {
        border-color: var(--gray-100);
    }
    
    .hover\:bg-gray-50:hover {
        background-color: var(--gray-50);
    }
    
    .pr-4 {
        padding-right: 1rem;
    }
    
    .pt-0 {
        padding-top: 0;
    }
    
    .max-h-0 {
        max-height: 0;
    }
    
    .faq-item.active .faq-answer {
        max-height: 200px;
    }
    
    .faq-item.active .faq-icon {
        transform: rotate(180deg);
    }
    
    .transition-transform {
        transition: transform 0.3s ease;
    }
    
    .transition-colors {
        transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out;
    }
    
    .leading-relaxed {
        line-height: 1.625;
    }
</style>
