{{-- Contact Section --}}
<section id="contact" class="py-20 lg:py-28 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up">
            <div
                class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                </svg>
                <span class="text-sm font-semibold text-medical-blue-700">تواصل معنا</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-medical-gray-900 mb-6 font-display">
                نحن هنا
                <span
                    class="bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">لمساعدتك</span>
            </h2>
            <p class="text-lg text-medical-gray-600 leading-relaxed">
                تواصل معنا في أي وقت وسنكون سعداء بالرد على استفساراتك ومساعدتك
            </p>
        </div>

        <div class="grid lg:grid-cols-2 gap-12">

            {{-- Contact Form --}}
            <div class="animate-fade-in-up">
                <form class="space-y-6" x-data="{ submitting: false }" @submit.prevent="submitting = true">
                    <div>
                        <label for="name" class="block text-sm font-bold text-medical-gray-900 mb-2">الاسم
                            الكامل</label>
                        <input type="text" id="name" name="name" required
                            class="w-full px-4 py-3 bg-white border-2 border-medical-gray-200 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-200 outline-none"
                            placeholder="أدخل اسمك الكامل">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-medical-gray-900 mb-2">البريد
                            الإلكتروني</label>
                        <input type="email" id="email" name="email" required
                            class="w-full px-4 py-3 bg-white border-2 border-medical-gray-200 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-200 outline-none"
                            placeholder="example@email.com">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-bold text-medical-gray-900 mb-2">رقم
                            الهاتف</label>
                        <input type="tel" id="phone" name="phone" required
                            class="w-full px-4 py-3 bg-white border-2 border-medical-gray-200 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-200 outline-none"
                            placeholder="+218 XX XXX XXXX">
                    </div>

                    <div>
                        <label for="subject" class="block text-sm font-bold text-medical-gray-900 mb-2">الموضوع</label>
                        <select id="subject" name="subject" required
                            class="w-full px-4 py-3 bg-white border-2 border-medical-gray-200 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-200 outline-none">
                            <option value="">اختر الموضوع</option>
                            <option value="general">استفسار عام</option>
                            <option value="supplier">استفسار للموردين</option>
                            <option value="institution">استفسار للمؤسسات الصحية</option>
                            <option value="technical">دعم فني</option>
                            <option value="partnership">فرص الشراكة</option>
                        </select>
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-bold text-medical-gray-900 mb-2">الرسالة</label>
                        <textarea id="message" name="message" rows="5" required
                            class="w-full px-4 py-3 bg-white border-2 border-medical-gray-200 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-200 outline-none resize-none"
                            placeholder="اكتب رسالتك هنا..."></textarea>
                    </div>

                    <button type="submit" :disabled="submitting"
                        class="w-full px-8 py-4 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!submitting">إرسال الرسالة</span>
                        <span x-show="submitting" class="flex items-center justify-center space-x-2 space-x-reverse"
                            style="display: none;">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>جاري الإرسال...</span>
                        </span>
                    </button>
                </form>
            </div>

            {{-- Contact Info --}}
            <div class="space-y-8 animate-fade-in-up" style="animation-delay: 0.2s;">

                {{-- Contact Cards --}}
                <div class="space-y-6">
                    <div
                        class="bg-gradient-to-br from-medical-blue-50 to-white rounded-2xl p-6 border border-medical-blue-200 hover:shadow-medical-lg transition-all duration-300">
                        <div class="flex items-start space-x-4 space-x-reverse">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-xl flex items-center justify-center shadow-medical">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-medical-gray-900 mb-2 font-display">اتصل بنا</h3>
                                <p class="text-medical-gray-600" dir="ltr">+218 XX XXX XXXX</p>
                                <p class="text-sm text-medical-gray-500 mt-1">متاح من السبت إلى الخميس، 9 صباحاً - 6
                                    مساءً</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-medical-green-50 to-white rounded-2xl p-6 border border-medical-green-200 hover:shadow-medical-lg transition-all duration-300">
                        <div class="flex items-start space-x-4 space-x-reverse">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-xl flex items-center justify-center shadow-medical">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-medical-gray-900 mb-2 font-display">راسلنا</h3>
                                <p class="text-medical-gray-600">info@mediequip.com</p>
                                <p class="text-sm text-medical-gray-500 mt-1">سنرد عليك خلال 24 ساعة</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-medical-blue-50 via-white to-medical-green-50 rounded-2xl p-6 border border-medical-blue-200 hover:shadow-medical-lg hover:border-medical-blue-300 transition-all duration-300 group">
                        <div class="flex items-start space-x-4 space-x-reverse">
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center shadow-medical group-hover:scale-110 transition-transform duration-300">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-lg font-bold text-medical-gray-900 mb-2 font-display">موقعنا</h3>
                                <p class="text-medical-gray-600 font-semibold">طرابلس، ليبيا</p>
                                <p class="text-sm text-medical-gray-500 mt-1">حي الصناعي ، طريق الدريبي</p>
                                <a href="https://maps.google.com" target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center space-x-2 space-x-reverse text-sm text-medical-blue-600 hover:text-medical-blue-700 font-semibold mt-3 group-hover:underline transition-colors duration-200">
                                    <span>عرض على الخريطة</span>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
