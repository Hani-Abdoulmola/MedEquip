{{-- Our Partners Section --}}
<section id="partners" class="py-20 lg:py-28 bg-gradient-to-br from-medical-gray-50 to-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up">
            <div
                class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                </svg>
                <span class="text-sm font-semibold text-medical-blue-700">شركاؤنا</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-medical-gray-900 mb-6 font-display">
                شركاؤنا
                <span
                    class="bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">الموثوقون</span>
            </h2>
            <p class="text-lg text-medical-gray-600 leading-relaxed">
                نتعاون مع أفضل الموردين والمصنعين العالميين لتقديم أجود المعدات الطبية
            </p>
        </div>

        {{-- Stats Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 mb-16">
            @php
                $stats = [
                    [
                        'number' => '200+',
                        'label' => 'شريك موثوق',
                        'icon' =>
                            'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z',
                    ],
                    [
                        'number' => '50+',
                        'label' => 'دولة حول العالم',
                        'icon' =>
                            'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                    ],
                    [
                        'number' => '25+',
                        'label' => 'سنة شراكة',
                        'icon' =>
                            'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
                    ],
                    [
                        'number' => '100%',
                        'label' => 'منتجات أصلية',
                        'icon' =>
                            'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
                    ],
                ];
            @endphp

            @foreach ($stats as $index => $stat)
                <div class="text-center bg-white rounded-2xl p-8 shadow-medical-lg hover:shadow-medical-xl transition-all duration-300 animate-fade-in-up"
                    style="animation-delay: {{ $index * 0.1 }}s;">
                    <div
                        class="w-16 h-16 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-medical">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="{{ $stat['icon'] }}" />
                        </svg>
                    </div>
                    <div
                        class="text-4xl font-bold bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent mb-2 font-display">
                        {{ $stat['number'] }}
                    </div>
                    <div class="text-medical-gray-600 font-medium">{{ $stat['label'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Trust Badges --}}
        <div class="grid md:grid-cols-3 gap-8">
            @php
                $badges = [
                    ['title' => 'شراكات عالمية معتمدة', 'description' => 'نتعاون مع أكبر المصنعين العالميين المعتمدين'],
                    ['title' => 'ضمان الجودة', 'description' => 'جميع منتجاتنا أصلية ومعتمدة من الشركات المصنعة'],
                    ['title' => 'دعم فني متكامل', 'description' => 'فريق دعم متخصص من شركائنا لخدمتك'],
                ];
            @endphp

            @foreach ($badges as $index => $badge)
                <div class="bg-white rounded-2xl p-8 border-2 border-medical-blue-200 hover:border-medical-blue-400 transition-all duration-300 animate-fade-in-up"
                    style="animation-delay: {{ ($index + 4) * 0.1 }}s;">
                    <div class="flex items-start space-x-4 space-x-reverse">
                        <div class="flex-shrink-0">
                            <div
                                class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center shadow-medical">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-medical-gray-900 mb-2 font-display">{{ $badge['title'] }}
                            </h3>
                            <p class="text-medical-gray-600 text-sm leading-relaxed">{{ $badge['description'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
