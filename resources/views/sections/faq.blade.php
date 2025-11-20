{{-- FAQ Section --}}
<section id="faq" class="py-20 lg:py-28 bg-gradient-to-br from-medical-gray-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up">
            <div
                class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-sm font-semibold text-medical-blue-700">الأسئلة الشائعة</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-medical-gray-900 mb-6 font-display">
                أسئلة
                <span
                    class="bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">شائعة</span>
            </h2>
            <p class="text-lg text-medical-gray-600 leading-relaxed">
                إجابات على الأسئلة الأكثر شيوعاً حول منصة MediEquip وخدماتنا
            </p>
        </div>

        {{-- FAQ Accordion --}}
        <div class="max-w-3xl mx-auto space-y-4" x-data="{ activeIndex: null }">
            @php
                $faqs = [
                    [
                        'q' => 'ما هي منصة MediEquip؟',
                        'a' =>
                            'MediEquip هي منصة إلكترونية متخصصة في ربط موردي المعدات الطبية بالمؤسسات الصحية في العالم العربي. نوفر حلولاً متكاملة لتسهيل عمليات الشراء والبيع للمعدات والمستلزمات الطبية.',
                    ],
                    [
                        'q' => 'كيف يمكنني التسجيل في المنصة؟',
                        'a' =>
                            'يمكنك التسجيل بسهولة من خلال النقر على زر "ابدأ الآن" واختيار نوع الحساب (مورد أو مؤسسة صحية)، ثم ملء البيانات المطلوبة. سيتم مراجعة طلبك والموافقة عليه خلال 24-48 ساعة.',
                    ],
                    [
                        'q' => 'هل التسجيل في المنصة مجاني؟',
                        'a' =>
                            'نعم، التسجيل الأساسي في المنصة مجاني تماماً. نوفر أيضاً باقات مميزة بمزايا إضافية للموردين الذين يرغبون في زيادة ظهورهم وتحسين مبيعاتهم.',
                    ],
                    [
                        'q' => 'كيف يتم ضمان جودة المنتجات؟',
                        'a' =>
                            'جميع الموردين على منصتنا معتمدون ومرخصون من الجهات المختصة. نطلب شهادات الجودة والمطابقة للمعايير الدولية لجميع المنتجات. كما نوفر نظام تقييم ومراجعات من العملاء.',
                    ],
                    [
                        'q' => 'ما هي طرق الدفع المتاحة؟',
                        'a' =>
                            'نوفر عدة طرق دفع آمنة تشمل: التحويل البنكي، بطاقات الائتمان، الدفع عند الاستلام (للطلبات المؤهلة)، والدفع الإلكتروني من خلال بوابات الدفع المعتمدة.',
                    ],
                    [
                        'q' => 'كم تستغرق عملية التوصيل؟',
                        'a' =>
                            'تختلف مدة التوصيل حسب الموقع ونوع المنتج. عادةً ما تستغرق عملية التوصيل من 24-48 ساعة داخل المدن الرئيسية، و3-5 أيام للمناطق الأخرى. نوفر خدمة تتبع الشحنات لمتابعة طلبك.',
                    ],
                ];
            @endphp

            @foreach ($faqs as $index => $faq)
                <div class="bg-white rounded-2xl border border-medical-gray-200 overflow-hidden shadow-medical hover:shadow-medical-lg transition-all duration-300 animate-fade-in-up"
                    style="animation-delay: {{ $index * 0.1 }}s;">
                    <button @click="activeIndex = activeIndex === {{ $index }} ? null : {{ $index }}"
                        class="w-full px-6 py-5 text-right flex items-center justify-between hover:bg-medical-gray-50 transition-colors duration-200">
                        <span class="text-lg font-bold text-medical-gray-900 font-display">{{ $faq['q'] }}</span>
                        <svg class="w-6 h-6 text-medical-blue-600 transform transition-transform duration-300"
                            :class="{ 'rotate-180': activeIndex === {{ $index }} }" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="activeIndex === {{ $index }}"
                        x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-2"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-2" class="px-6 pb-5"
                        style="display: none;">
                        <p class="text-medical-gray-600 leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Contact CTA --}}
        <div class="mt-16 text-center animate-fade-in-up" style="animation-delay: 0.6s;">
            <p class="text-medical-gray-600 mb-6">لم تجد إجابة لسؤالك؟</p>
            <a href="#contact"
                class="inline-flex items-center space-x-2 space-x-reverse px-8 py-4 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                <span>تواصل معنا</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
        </div>
    </div>
</section>
