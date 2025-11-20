{{-- Dashboard Header Component - Matches Landing Page Design Quality --}}
@props(['title' => 'لوحة التحكم', 'userName' => ''])

<header class="sticky top-0 z-40 bg-white border-b border-medical-gray-200 shadow-medical">
    <div class="flex items-center justify-between h-20 px-4 sm:px-6 lg:px-8">

        {{-- Left Side: Mobile Menu Toggle + Page Title --}}
        <div class="flex items-center space-x-4 space-x-reverse">
            {{-- Mobile Menu Toggle --}}
            <button @click="mobileMenuOpen = !mobileMenuOpen"
                class="lg:hidden p-2 text-medical-gray-600 hover:text-medical-gray-900 hover:bg-medical-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Desktop Sidebar Toggle --}}
            <button @click="sidebarOpen = !sidebarOpen"
                class="hidden lg:block p-2 text-medical-gray-600 hover:text-medical-gray-900 hover:bg-medical-gray-100 rounded-lg transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>

            {{-- Page Title --}}
            <h1 class="text-xl sm:text-2xl font-bold text-medical-gray-900 font-display">{{ $title }}</h1>
        </div>

        {{-- Right Side: Search + Notifications + User Menu --}}
        <div class="flex items-center space-x-4 space-x-reverse">

            {{-- Search Bar (Desktop Only) --}}
            <div class="hidden md:block relative" x-data="{ searchFocused: false }">
                <input type="text" placeholder="بحث..." @focus="searchFocused = true" @blur="searchFocused = false"
                    class="w-64 pl-10 pr-4 py-2 bg-medical-gray-50 border border-medical-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200"
                    :class="searchFocused ? 'bg-white shadow-medical' : ''">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-medical-gray-400"
                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>

            {{-- Notifications --}}
            <div class="relative" x-data="{ notificationsOpen: false }">
                <button @click="notificationsOpen = !notificationsOpen"
                    class="relative p-2 text-medical-gray-600 hover:text-medical-gray-900 hover:bg-medical-gray-100 rounded-lg transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    {{-- Notification Badge --}}
                    <span
                        class="absolute top-1 right-1 w-2 h-2 bg-medical-red-500 rounded-full ring-2 ring-white"></span>
                </button>

                {{-- Notifications Dropdown --}}
                <div x-show="notificationsOpen" @click.away="notificationsOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute left-0 mt-2 w-80 bg-white rounded-xl shadow-medical-xl border border-medical-gray-200 overflow-hidden">

                    {{-- Header --}}
                    <div class="px-4 py-3 bg-medical-gray-50 border-b border-medical-gray-200">
                        <h3 class="text-sm font-semibold text-medical-gray-900">الإشعارات</h3>
                    </div>

                    {{-- Notifications List --}}
                    <div class="max-h-96 overflow-y-auto">
                        {{-- Empty State --}}
                        <div class="px-4 py-8 text-center">
                            <svg class="w-12 h-12 mx-auto text-medical-gray-300 mb-3" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-sm text-medical-gray-600">لا توجد إشعارات جديدة</p>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="px-4 py-3 bg-medical-gray-50 border-t border-medical-gray-200">
                        <a href="#"
                            class="text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">عرض جميع
                            الإشعارات</a>
                    </div>
                </div>
            </div>

            {{-- User Menu --}}
            <div class="relative" x-data="{ userMenuOpen: false }">
                <button @click="userMenuOpen = !userMenuOpen"
                    class="flex items-center space-x-2 space-x-reverse p-2 hover:bg-medical-gray-100 rounded-lg transition-colors duration-200">
                    <div
                        class="w-8 h-8 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-full flex items-center justify-center text-white font-bold text-sm">
                        {{ mb_substr($userName, 0, 1) }}
                    </div>
                    <span class="hidden sm:block text-sm font-medium text-medical-gray-900">{{ $userName }}</span>
                    <svg class="w-4 h-4 text-medical-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- User Dropdown --}}
                <div x-show="userMenuOpen" @click.away="userMenuOpen = false" x-cloak
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95"
                    class="absolute left-0 mt-2 w-56 bg-white rounded-xl shadow-medical-xl border border-medical-gray-200 overflow-hidden">

                    <div class="px-4 py-3 bg-medical-gray-50 border-b border-medical-gray-200">
                        <p class="text-sm font-semibold text-medical-gray-900">{{ $userName }}</p>
                        <p class="text-xs text-medical-gray-600">{{ auth()->user()->email }}</p>
                    </div>

                    <div class="py-2">
                        <a href="{{ route('profile.edit') }}"
                            class="flex items-center space-x-3 space-x-reverse px-4 py-2.5 text-sm text-medical-gray-700 hover:bg-medical-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>الملف الشخصي</span>
                        </a>
                        <a href="#"
                            class="flex items-center space-x-3 space-x-reverse px-4 py-2.5 text-sm text-medical-gray-700 hover:bg-medical-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>الإعدادات</span>
                        </a>
                    </div>

                    <div class="border-t border-medical-gray-200">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full flex items-center space-x-3 space-x-reverse px-4 py-2.5 text-sm text-medical-red-600 hover:bg-medical-red-50 transition-colors duration-200">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                                <span>تسجيل الخروج</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
