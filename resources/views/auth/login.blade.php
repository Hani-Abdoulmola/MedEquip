<x-auth-layout>
    <x-slot name="title">تسجيل الدخول</x-slot>

    {{-- Page Title --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-medical-gray-900 font-display">تسجيل الدخول</h2>
        <p class="mt-2 text-medical-gray-600">مرحبًا بعودتك إلى منصة MediEquip</p>
    </div>

    {{-- Session Status --}}
    @if (session('status'))
        <div class="mb-6 p-4 bg-medical-green-50 border-2 border-medical-green-200 rounded-xl">
            <div class="flex items-center space-x-3 space-x-reverse">
                <svg class="w-6 h-6 text-medical-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd" />
                </svg>
                <p class="text-sm font-semibold text-medical-green-900">{{ session('status') }}</p>
            </div>
        </div>
    @endif

    {{-- Login Form --}}
    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        {{-- Email Address --}}
        <div>
            <label for="email" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                البريد الإلكتروني <span class="text-medical-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    autocomplete="username"
                    class="w-full pl-4 pr-12 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('email') border-medical-red-500 @enderror"
                    placeholder="example@mediequip.com">
            </div>
            @error('email')
                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div>
            <label for="password" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                كلمة المرور <span class="text-medical-red-500">*</span>
            </label>
            <div class="relative">
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password"
                    class="w-full pl-4 pr-12 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('password') border-medical-red-500 @enderror"
                    placeholder="••••••••">
            </div>
            @error('password')
                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" name="remember"
                    class="w-4 h-4 rounded border-2 border-medical-gray-300 text-medical-blue-600 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300">
                <span class="mr-2 text-sm text-medical-gray-700 font-semibold">تذكرني</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                    class="text-sm font-semibold text-medical-blue-600 hover:text-medical-green-600 transition-colors duration-300">
                    نسيت كلمة المرور؟
                </a>
            @endif
        </div>

        {{-- Submit Button --}}
        <div class="pt-2">
            <button type="submit"
                class="w-full py-4 px-6 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                <span class="flex items-center justify-center space-x-2 space-x-reverse">
                    <span>تسجيل الدخول</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </span>
            </button>
        </div>

        {{-- Register Link --}}
        <div class="text-center pt-4 border-t-2 border-medical-gray-200">
            <p class="text-medical-gray-600">
                ليس لديك حساب؟
                <a href="{{ route('register') }}"
                    class="font-bold text-medical-blue-600 hover:text-medical-green-600 transition-colors duration-300">
                    إنشاء حساب جديد
                </a>
            </p>
        </div>
    </form>
</x-auth-layout>
