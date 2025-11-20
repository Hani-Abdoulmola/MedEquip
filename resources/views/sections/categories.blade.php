{{-- Categories Section - Product Categories Showcase --}}
<section id="categories" class="py-20 lg:py-28 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="text-center max-w-3xl mx-auto mb-16 animate-fade-in-up">
            <div class="inline-flex items-center space-x-2 space-x-reverse bg-gradient-to-r from-medical-blue-50 to-medical-green-50 border border-medical-blue-200 px-4 py-2 rounded-full mb-6">
                <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"/>
                </svg>
                <span class="text-sm font-semibold text-medical-blue-700">فئات المنتجات</span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-medical-gray-900 mb-6 font-display">
                تصفح 
                <span class="bg-gradient-to-r from-medical-blue-600 to-medical-green-600 bg-clip-text text-transparent">فئات المعدات</span>
            </h2>
            <p class="text-lg text-medical-gray-600 leading-relaxed">
                نوفر مجموعة واسعة من المعدات والمستلزمات الطبية لتلبية جميع احتياجات المؤسسات الصحية
            </p>
        </div>

        {{-- Categories Grid --}}
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            
            @php
            $categories = [
                ['name' => 'أجهزة التشخيص', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01', 'count' => '250+'],
                ['name' => 'أجهزة المختبرات', 'icon' => 'M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z', 'count' => '180+'],
                ['name' => 'أجهزة الجراحة', 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'count' => '320+'],
                ['name' => 'أجهزة التصوير', 'icon' => 'M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z M15 13a3 3 0 11-6 0 3 3 0 016 0z', 'count' => '150+'],
                ['name' => 'أجهزة العناية المركزة', 'icon' => 'M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z', 'count' => '200+'],
                ['name' => 'المستلزمات الطبية', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'count' => '500+'],
            ];
            @endphp

            @foreach($categories as $index => $category)
            <div class="group bg-gradient-to-br from-white to-medical-gray-50 rounded-2xl p-8 border border-medical-gray-200 hover:border-medical-blue-400 hover:shadow-medical-xl transition-all duration-300 cursor-pointer animate-fade-in-up" style="animation-delay: {{ $index * 0.1 }}s;">
                <div class="w-16 h-16 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300 shadow-medical">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $category['icon'] }}"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-medical-gray-900 mb-2 font-display">{{ $category['name'] }}</h3>
                <p class="text-medical-blue-600 font-semibold">{{ $category['count'] }} منتج</p>
            </div>
            @endforeach
        </div>

        {{-- CTA --}}
        <div class="mt-16 text-center animate-fade-in-up" style="animation-delay: 0.6s;">
            <a href="{{ route('register') }}" class="inline-flex items-center space-x-2 space-x-reverse px-8 py-4 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                <span>استكشف جميع الفئات</span>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
        </div>
    </div>
</section>

