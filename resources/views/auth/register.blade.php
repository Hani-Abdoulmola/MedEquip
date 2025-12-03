<br><x-auth-layout><br>
    <x-slot name="title">إنشاء حساب جديد</x-slot>

    {{-- Page Title --}}
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-medical-gray-900 font-display">إنشاء حساب جديد</h2>
        <p class="mt-2 text-medical-gray-600">انضم إلى منصة MediEquip الرائدة في تجارة المعدات الطبية</p>
    </div>

    {{-- User Type Toggle --}}
    <div class="mb-8" x-data="{ userType: '{{ old('user_type', 'buyer') }}' }">

        {{-- Toggle Buttons --}}
        <div class="flex gap-4 p-2 bg-medical-gray-100 rounded-2xl">
            <button type="button" @click="userType = 'buyer'"
                :class="userType === 'buyer' ?
                    'bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white shadow-medical-lg' :
                    'bg-transparent text-medical-gray-600 hover:text-medical-gray-900'"
                class="flex-1 py-4 px-6 rounded-xl font-bold transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>مشتري (مؤسسة صحية)</span>
                </div>
            </button>

            <button type="button" @click="userType = 'supplier'"
                :class="userType === 'supplier' ?
                    'bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white shadow-medical-lg' :
                    'bg-transparent text-medical-gray-600 hover:text-medical-gray-900'"
                class="flex-1 py-4 px-6 rounded-xl font-bold transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                <div class="flex items-center justify-center space-x-2 space-x-reverse">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <span>مورد (شركة معدات طبية)</span>
                </div>
            </button>
        </div>

        {{-- Info Box --}}
        <div class="mt-4 p-4 rounded-xl border-2 transition-all duration-300"
            :class="userType === 'buyer' ? 'bg-medical-blue-50 border-medical-blue-200' :
                'bg-medical-green-50 border-medical-green-200'">
            <div class="flex items-start space-x-3 space-x-reverse">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5"
                    :class="userType === 'buyer' ? 'text-medical-blue-600' : 'text-medical-green-600'"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
                <div>
                    <p class="text-sm font-semibold"
                        :class="userType === 'buyer' ? 'text-medical-blue-900' : 'text-medical-green-900'"
                        x-text="userType === 'buyer' ? 'حساب المشتري' : 'حساب المورد'"></p>
                    <p class="text-sm mt-1"
                        :class="userType === 'buyer' ? 'text-medical-blue-700' : 'text-medical-green-700'"
                        x-text="userType === 'buyer' ? 'للمستشفيات والعيادات والمختبرات والمراكز الطبية التي ترغب في شراء المعدات الطبية.' : 'للشركات والموردين الذين يرغبون في بيع المعدات الطبية للمؤسسات الصحية.'">
                    </p>
                </div>
            </div>
        </div>

        {{-- Buyer Registration Form --}}
        <div x-show="userType === 'buyer'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-8">
            <form method="POST" action="{{ route('register.buyer') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="buyer">

                {{-- Section: User Account --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-medical-gray-900 flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>بيانات الحساب</span>
                    </h3>

                    {{-- Name --}}
                    <div>
                        <label for="buyer_name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الاسم الكامل <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="buyer_name" type="text" name="name" value="{{ old('name') }}" required
                            autofocus
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('name') border-medical-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="buyer_email" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="buyer_email" type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('email') border-medical-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="buyer_phone" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            رقم الهاتف
                        </label>
                        <input id="buyer_phone" type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('phone') border-medical-red-500 @enderror">
                        @error('phone')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="buyer_password" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                كلمة المرور <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="buyer_password" type="password" name="password" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('password') border-medical-red-500 @enderror">
                            @error('password')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_password_confirmation"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                تأكيد كلمة المرور <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="buyer_password_confirmation" type="password" name="password_confirmation"
                                required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300">
                        </div>
                    </div>
                </div>

                {{-- Section: Organization Info --}}
                <div class="space-y-4 pt-6 border-t-2 border-medical-gray-200">
                    <h3 class="text-lg font-bold text-medical-gray-900 flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5 text-medical-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>بيانات المؤسسة الصحية</span>
                    </h3>

                    {{-- Organization Name --}}
                    <div>
                        <label for="organization_name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            اسم المؤسسة <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="organization_name" type="text" name="organization_name"
                            value="{{ old('organization_name') }}" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('organization_name') border-medical-red-500 @enderror"
                            placeholder="مثال: مستشفى السلام الطبي">
                        @error('organization_name')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Organization Type --}}
                    <div>
                        <label for="organization_type" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            نوع المؤسسة <span class="text-medical-red-500">*</span>
                        </label>
                        <select id="organization_type" name="organization_type" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('organization_type') border-medical-red-500 @enderror">
                            <option value="">اختر نوع المؤسسة</option>
                            <option value="مستشفى" {{ old('organization_type') == 'مستشفى' ? 'selected' : '' }}>مستشفى
                            </option>
                            <option value="عيادة" {{ old('organization_type') == 'عيادة' ? 'selected' : '' }}>عيادة
                            </option>
                            <option value="مختبر" {{ old('organization_type') == 'مختبر' ? 'selected' : '' }}>مختبر
                            </option>
                            <option value="مركز طبي" {{ old('organization_type') == 'مركز طبي' ? 'selected' : '' }}>
                                مركز طبي</option>
                            <option value="صيدلية" {{ old('organization_type') == 'صيدلية' ? 'selected' : '' }}>صيدلية
                            </option>
                            <option value="أخرى" {{ old('organization_type') == 'أخرى' ? 'selected' : '' }}>أخرى
                            </option>
                        </select>
                        @error('organization_type')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- License Number --}}
                    <div>
                        <label for="license_number" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            رقم الترخيص الصحي
                        </label>
                        <input id="license_number" type="text" name="license_number"
                            value="{{ old('license_number') }}"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('license_number') border-medical-red-500 @enderror">
                        @error('license_number')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Country & City --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="buyer_country" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الدولة <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="buyer_country" type="text" name="country"
                                value="{{ old('country', 'ليبيا') }}" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('country') border-medical-red-500 @enderror">
                            @error('country')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_city" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                المدينة
                            </label>
                            <input id="buyer_city" type="text" name="city" value="{{ old('city') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('city') border-medical-red-500 @enderror">
                            @error('city')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="buyer_address" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            العنوان
                        </label>
                        <textarea id="buyer_address" name="address" rows="2"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('address') border-medical-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Email & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="buyer_contact_email"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                البريد الإلكتروني للتواصل
                            </label>
                            <input id="buyer_contact_email" type="email" name="contact_email"
                                value="{{ old('contact_email') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('contact_email') border-medical-red-500 @enderror">
                            @error('contact_email')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="buyer_contact_phone"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                رقم الهاتف للتواصل
                            </label>
                            <input id="buyer_contact_phone" type="text" name="contact_phone"
                                value="{{ old('contact_phone') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-blue-500 focus:ring-4 focus:ring-medical-blue-100 transition-all duration-300 @error('contact_phone') border-medical-red-500 @enderror">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-6">
                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-medical-blue-600 to-medical-green-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-blue-300">
                        <span class="flex items-center justify-center space-x-2 space-x-reverse">
                            <span>إنشاء حساب مشتري</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Supplier Registration Form --}}
        <div x-show="userType === 'supplier'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100" class="mt-8">
            <form method="POST" action="{{ route('register.supplier') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="user_type" value="supplier">

                {{-- Section: User Account --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-medical-gray-900 flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5 text-medical-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>بيانات الحساب</span>
                    </h3>

                    {{-- Name --}}
                    <div>
                        <label for="supplier_name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            الاسم الكامل <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="supplier_name" type="text" name="name" value="{{ old('name') }}"
                            required autofocus
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('name') border-medical-red-500 @enderror">
                        @error('name')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="supplier_email" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            البريد الإلكتروني <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="supplier_email" type="email" name="email" value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('email') border-medical-red-500 @enderror">
                        @error('email')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="supplier_phone" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            رقم الهاتف
                        </label>
                        <input id="supplier_phone" type="text" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('phone') border-medical-red-500 @enderror">
                        @error('phone')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_password"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                كلمة المرور <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="supplier_password" type="password" name="password" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('password') border-medical-red-500 @enderror">
                            @error('password')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="supplier_password_confirmation"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                تأكيد كلمة المرور <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="supplier_password_confirmation" type="password" name="password_confirmation"
                                required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300">
                        </div>
                    </div>
                </div>

                {{-- Section: Company Info --}}
                <div class="space-y-4 pt-6 border-t-2 border-medical-gray-200">
                    <h3 class="text-lg font-bold text-medical-gray-900 flex items-center space-x-2 space-x-reverse">
                        <svg class="w-5 h-5 text-medical-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>بيانات الشركة</span>
                    </h3>

                    {{-- Company Name --}}
                    <div>
                        <label for="company_name" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            اسم الشركة <span class="text-medical-red-500">*</span>
                        </label>
                        <input id="company_name" type="text" name="company_name"
                            value="{{ old('company_name') }}" required
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('company_name') border-medical-red-500 @enderror"
                            placeholder="مثال: شركة المعدات الطبية المتقدمة">
                        @error('company_name')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Commercial Register & Tax Number --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="commercial_register"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                رقم السجل التجاري
                            </label>
                            <input id="commercial_register" type="text" name="commercial_register"
                                value="{{ old('commercial_register') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('commercial_register') border-medical-red-500 @enderror">
                            @error('commercial_register')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="tax_number" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الرقم الضريبي
                            </label>
                            <input id="tax_number" type="text" name="tax_number" value="{{ old('tax_number') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('tax_number') border-medical-red-500 @enderror">
                            @error('tax_number')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Country & City --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_country"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                الدولة <span class="text-medical-red-500">*</span>
                            </label>
                            <input id="supplier_country" type="text" name="country"
                                value="{{ old('country', 'ليبيا') }}" required
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('country') border-medical-red-500 @enderror">
                            @error('country')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="supplier_city" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                المدينة
                            </label>
                            <input id="supplier_city" type="text" name="city" value="{{ old('city') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('city') border-medical-red-500 @enderror">
                            @error('city')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label for="supplier_address" class="block text-sm font-semibold text-medical-gray-700 mb-2">
                            العنوان
                        </label>
                        <textarea id="supplier_address" name="address" rows="2"
                            class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('address') border-medical-red-500 @enderror">{{ old('address') }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Contact Email & Phone --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="supplier_contact_email"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                البريد الإلكتروني للتواصل
                            </label>
                            <input id="supplier_contact_email" type="email" name="contact_email"
                                value="{{ old('contact_email') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('contact_email') border-medical-red-500 @enderror">
                            @error('contact_email')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="supplier_contact_phone"
                                class="block text-sm font-semibold text-medical-gray-700 mb-2">
                                رقم الهاتف للتواصل
                            </label>
                            <input id="supplier_contact_phone" type="text" name="contact_phone"
                                value="{{ old('contact_phone') }}"
                                class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:border-medical-green-500 focus:ring-4 focus:ring-medical-green-100 transition-all duration-300 @error('contact_phone') border-medical-red-500 @enderror">
                            @error('contact_phone')
                                <p class="mt-2 text-sm text-medical-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <div class="pt-6">
                    <button type="submit"
                        class="w-full py-4 px-6 bg-gradient-to-r from-medical-green-600 to-medical-blue-600 text-white font-bold rounded-xl shadow-medical-lg hover:shadow-medical-xl hover:scale-105 transition-all duration-300 focus:outline-none focus:ring-4 focus:ring-medical-green-300">
                        <span class="flex items-center justify-center space-x-2 space-x-reverse">
                            <span>إنشاء حساب مورد</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7" />
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Already have an account? --}}
    <div class="mt-8 text-center">
        <p class="text-medical-gray-600">
            لديك حساب بالفعل؟
            <a href="{{ route('login') }}"
                class="font-bold text-medical-blue-600 hover:text-medical-green-600 transition-colors duration-300">
                تسجيل الدخول
            </a>
        </p>
    </div>
</x-auth-layout>
