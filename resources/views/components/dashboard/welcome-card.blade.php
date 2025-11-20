{{-- Dashboard Welcome Card Component - Matches Landing Page Design Quality --}}
@props([
    'userName' => '',
    'userType' => 'مستخدم',
    'message' => 'مرحباً بك في لوحة التحكم',
    'gradient' => 'from-medical-blue-500 to-medical-green-500'
])

<div class="relative bg-gradient-to-br {{ $gradient }} rounded-2xl p-8 shadow-medical-xl overflow-hidden animate-fade-in-up">
    {{-- Background Decorative Elements --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        {{-- Animated Gradient Orbs --}}
        <div class="absolute top-0 -right-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse-slow"></div>
        <div class="absolute bottom-0 -left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl animate-pulse-slow" style="animation-delay: 1s;"></div>
        
        {{-- Medical Pattern SVG --}}
        <svg class="absolute bottom-0 left-0 w-full h-full opacity-10" viewBox="0 0 100 100" preserveAspectRatio="none">
            <defs>
                <pattern id="medical-pattern" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse">
                    <path d="M10 5 L10 15 M5 10 L15 10" stroke="white" stroke-width="0.5" fill="none" />
                </pattern>
            </defs>
            <rect width="100" height="100" fill="url(#medical-pattern)" />
        </svg>
    </div>

    {{-- Content --}}
    <div class="relative z-10">
        <div class="flex items-start justify-between mb-6">
            <div>
                {{-- Greeting Badge --}}
                <div class="inline-flex items-center space-x-2 space-x-reverse bg-white/20 backdrop-blur-sm border border-white/30 px-4 py-2 rounded-full mb-4">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm font-semibold text-white">{{ $userType }}</span>
                </div>

                {{-- Welcome Message --}}
                <h2 class="text-3xl sm:text-4xl font-bold text-white mb-2 font-display">
                    {{ $message }}
                </h2>
                <p class="text-xl text-white/90 font-medium">
                    {{ $userName }}
                </p>
            </div>

            {{-- Icon --}}
            <div class="hidden sm:block w-16 h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                </svg>
            </div>
        </div>

        {{-- Additional Content Slot --}}
        @if ($slot->isNotEmpty())
            <div class="mt-6 pt-6 border-t border-white/20">
                {{ $slot }}
            </div>
        @endif
    </div>
</div>

