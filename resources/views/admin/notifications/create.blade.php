{{-- Admin Create Notification - Professional Design --}}
<x-dashboard.layout title="إنشاء إشعار جديد" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Success/Error Messages --}}
    @if (session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
            <h3 class="font-bold mb-2">يرجى تصحيح الأخطاء التالية:</h3>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Premium Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-black text-medical-gray-900 font-display">إنشاء إشعار جديد</h1>
                <p class="mt-3 text-base text-medical-gray-600">أرسل إشعارات للموردين والمشترين</p>
            </div>
            <a href="{{ route('admin.notifications.index') }}"
                class="px-5 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 transition-all duration-200 font-semibold shadow-sm flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>رجوع</span>
            </a>
        </div>
    </div>

    {{-- Create Notification Form --}}
    <div class="bg-white rounded-2xl shadow-lg border border-medical-gray-200 p-8">
        <form method="POST" action="{{ route('admin.notifications.store') }}" id="notification-form">
            @csrf

            {{-- Recipients Selection --}}
            <div class="mb-8">
                <label class="block text-lg font-bold text-medical-gray-900 mb-4">
                    <span class="flex items-center gap-2">
                        <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        المستلمون
                    </span>
                </label>
                <p class="text-sm text-medical-gray-600 mb-4">اختر من سيستلم هذا الإشعار</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Suppliers Option --}}
                    <label id="recipient-suppliers"
                        class="relative flex items-center p-5 border-2 border-medical-gray-300 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-lg group hover:border-medical-blue-300">
                        <input type="checkbox" name="recipients[]" value="suppliers"
                            class="sr-only peer recipient-checkbox" onchange="updateRecipients()">
                        <div class="flex items-center gap-4 w-full">
                            <div id="icon-suppliers"
                                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-all bg-medical-gray-100 group-hover:bg-medical-blue-100">
                                <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-medical-gray-900">الموردين</h3>
                                <p class="text-sm text-medical-gray-600">{{ $supplierCount }} مورد</p>
                            </div>
                            <svg id="check-suppliers" class="w-6 h-6 flex-shrink-0 text-medical-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </label>

                    {{-- Buyers Option --}}
                    <label id="recipient-buyers"
                        class="relative flex items-center p-5 border-2 border-medical-gray-300 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-lg group hover:border-medical-blue-300">
                        <input type="checkbox" name="recipients[]" value="buyers"
                            class="sr-only peer recipient-checkbox" onchange="updateRecipients()">
                        <div class="flex items-center gap-4 w-full">
                            <div id="icon-buyers"
                                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-all bg-medical-gray-100 group-hover:bg-medical-blue-100">
                                <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-medical-gray-900">المشترين</h3>
                                <p class="text-sm text-medical-gray-600">{{ $buyerCount }} مشتري</p>
                            </div>
                            <svg id="check-buyers" class="w-6 h-6 flex-shrink-0 text-medical-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </label>

                    {{-- Both Option --}}
                    <label id="recipient-both"
                        class="relative flex items-center p-5 border-2 border-medical-gray-300 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-lg group hover:border-medical-blue-300">
                        <input type="checkbox" name="recipients[]" value="both"
                            class="sr-only peer recipient-checkbox" onchange="updateRecipients()">
                        <div class="flex items-center gap-4 w-full">
                            <div id="icon-both"
                                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-all bg-medical-gray-100 group-hover:bg-medical-blue-100">
                                <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-medical-gray-900">الجميع</h3>
                                <p class="text-sm text-medical-gray-600">الموردين والمشترين</p>
                            </div>
                            <svg id="check-both" class="w-6 h-6 flex-shrink-0 text-medical-gray-300" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </label>

                    {{-- Specific Recipients Option --}}
                    <label id="recipient-specific"
                        class="relative flex items-center p-5 border-2 border-medical-gray-300 rounded-xl cursor-pointer transition-all duration-200 hover:shadow-lg group hover:border-medical-blue-300">
                        <input type="checkbox" name="recipients[]" value="specific"
                            class="sr-only peer recipient-checkbox"
                            onchange="updateRecipients(); toggleSpecificSelection()">
                        <div class="flex items-center gap-4 w-full">
                            <div id="icon-specific"
                                class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 transition-all bg-medical-gray-100 group-hover:bg-medical-blue-100">
                                <svg class="w-6 h-6 text-medical-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-medical-gray-900">محدد</h3>
                                <p class="text-sm text-medical-gray-600">اختر مستلمين محددين</p>
                            </div>
                            <svg id="check-specific" class="w-6 h-6 flex-shrink-0 text-medical-gray-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </label>
                </div>

                {{-- Specific Recipients Selection (Hidden by default) --}}
                <div id="specific-recipients-section" class="mt-6 hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Suppliers Searchable Select --}}
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-900 mb-2">
                                الموردين المحددين
                            </label>
                            <div class="relative">
                                {{-- Search Input --}}
                                <input type="text" id="supplier-search" placeholder="ابحث عن مورد..."
                                    class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all"
                                    autocomplete="off">

                                {{-- Dropdown Results --}}
                                <div id="supplier-dropdown"
                                    class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-medical-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <div id="supplier-options" class="py-2">
                                        {{-- Options will be populated by JavaScript --}}
                                    </div>
                                </div>

                                {{-- Selected Chips --}}
                                <div id="supplier-selected"
                                    class="mt-3 flex flex-wrap gap-2 min-h-[50px] p-3 border-2 border-medical-gray-200 rounded-xl bg-medical-gray-50">
                                    {{-- Selected items will appear here as chips --}}
                                    <p class="text-sm text-medical-gray-400 w-full text-center"
                                        id="supplier-empty-message">لم يتم اختيار أي مورد بعد</p>
                                </div>

                                {{-- Hidden inputs for selected IDs --}}
                                <div id="supplier-hidden-inputs"></div>
                            </div>
                            <p class="mt-1 text-xs text-medical-gray-500">اكتب للبحث ثم اختر من القائمة. كرر العملية
                                لاختيار عدة موردين</p>
                        </div>

                        {{-- Buyers Searchable Select --}}
                        <div>
                            <label class="block text-sm font-bold text-medical-gray-900 mb-2">
                                المشترين المحددين
                            </label>
                            <div class="relative">
                                {{-- Search Input --}}
                                <input type="text" id="buyer-search" placeholder="ابحث عن مشتري..."
                                    class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all"
                                    autocomplete="off">

                                {{-- Dropdown Results --}}
                                <div id="buyer-dropdown"
                                    class="hidden absolute z-50 w-full mt-1 bg-white border-2 border-medical-gray-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                    <div id="buyer-options" class="py-2">
                                        {{-- Options will be populated by JavaScript --}}
                                    </div>
                                </div>

                                {{-- Selected Chips --}}
                                <div id="buyer-selected"
                                    class="mt-3 flex flex-wrap gap-2 min-h-[50px] p-3 border-2 border-medical-gray-200 rounded-xl bg-medical-gray-50">
                                    {{-- Selected items will appear here as chips --}}
                                    <p class="text-sm text-medical-gray-400 w-full text-center"
                                        id="buyer-empty-message">لم يتم اختيار أي مشتري بعد</p>
                                </div>

                                {{-- Hidden inputs for selected IDs --}}
                                <div id="buyer-hidden-inputs"></div>
                            </div>
                            <p class="mt-1 text-xs text-medical-gray-500">اكتب للبحث ثم اختر من القائمة. كرر العملية
                                لاختيار عدة مشترين</p>
                        </div>
                    </div>
                    <div class="mt-4 p-4 bg-medical-blue-50 border border-medical-blue-200 rounded-xl">
                        <p class="text-sm text-medical-blue-800">
                            <strong>ملاحظة:</strong> يمكنك اختيار موردين ومشترين معاً من القوائم أعلاه. سيتم إرسال
                            الإشعار فقط للمستخدمين المحددين.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Title Field --}}
            <div class="mb-6">
                <label for="title"
                    class="block text-base font-semibold text-medical-gray-900 mb-2 flex items-center gap-1">
                    <svg class="w-5 h-5 text-medical-blue-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 7h16M4 12h16M4 17h16" />
                    </svg>
                    عنوان الإشعار <span class="text-medical-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                    autocomplete="off" placeholder="مثال: تحديث هام بخصوص العروض"
                    class="w-full px-4 py-3 border-2 border-medical-blue-300 focus:border-medical-blue-600 rounded-xl transition shadow-sm focus:ring-2 focus:ring-medical-blue-100 focus:outline-none placeholder-medical-gray-400"
                    maxlength="255">
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs text-medical-gray-500">اكتب عنواناً موجزاً (حتى 255 حرف)</p>
                    <span id="title-char-count"
                        class="text-xs text-medical-gray-400">{{ mb_strlen(old('title')) }}/255</span>
                </div>
            </div>

            {{-- Message Field --}}
            <div class="mb-6">
                <label for="message"
                    class="block text-base font-semibold text-medical-gray-900 mb-2 flex items-center gap-1">
                    <svg class="w-5 h-5 text-medical-blue-500" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 10h.01M12 10h.01M16 10h.01M21 16.5A2.5 2.5 0 0018.5 14h-13A2.5 2.5 0 003 16.5v.5A2.5 2.5 0 005.5 19h13A2.5 2.5 0 0021 16.5v-.5z" />
                    </svg>
                    محتوى الإشعار <span class="text-medical-red-500">*</span>
                </label>
                <textarea name="message" id="message" rows="5" required autocomplete="off"
                    placeholder="اكتب محتوى الإشعار بشكل واضح ومختصر للمستلمين..."
                    class="w-full px-4 py-3 border-2 border-medical-blue-300 focus:border-medical-blue-600 rounded-xl transition shadow-sm focus:ring-2 focus:ring-medical-blue-100 focus:outline-none placeholder-medical-gray-400 resize-none"
                    maxlength="5000">{{ old('message') }}</textarea>
                <div class="flex items-center justify-between mt-1">
                    <p class="text-xs text-medical-gray-500">يرجى ألا يتجاوز محتوى الإشعار 5000 حرف</p>
                    <span id="char-count" class="text-xs text-medical-gray-400">0 / 5000</span>
                </div>
            </div>

            {{-- Notification Type (Appearance) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label for="type"
                        class="block text-base font-semibold text-medical-gray-900 mb-2 flex items-center gap-1">
                        <svg class="w-5 h-5 text-medical-blue-500" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" stroke-width="2" />
                        </svg>
                        نوع الإشعار (تصميم ولون)
                    </label>
                    <select name="type" id="type"
                        class="w-full px-4 py-3 border-2 border-medical-blue-300 focus:border-medical-blue-600 rounded-xl transition shadow-sm focus:ring-2 focus:ring-medical-blue-100 focus:outline-none bg-white">
                        <option value="info" {{ old('type', 'info') == 'info' ? 'selected' : '' }}>معلومات (أزرق)
                        </option>
                        <option value="success" {{ old('type') == 'success' ? 'selected' : '' }}>نجاح (أخضر)</option>
                        <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>تحذير (برتقالي)
                        </option>
                        <option value="error" {{ old('type') == 'error' ? 'selected' : '' }}>خطأ (أحمر)</option>
                        <option value="primary" {{ old('type') == 'primary' ? 'selected' : '' }}>أساسي (غامق)</option>
                    </select>
                    <p class="mt-1 text-xs text-medical-gray-400">يحدد شكل الإشعار ولونه للمستلم</p>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-medical-gray-200">
                <a href="{{ route('admin.notifications.index') }}"
                    class="px-6 py-3 bg-white border-2 border-medical-gray-300 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 transition-all duration-200 font-semibold">
                    إلغاء
                </a>
                <button type="submit"
                    class="px-6 py-3 bg-gradient-to-r from-medical-blue-600 to-medical-blue-700 text-white rounded-xl hover:from-medical-blue-700 hover:to-medical-blue-800 transition-all duration-200 font-semibold shadow-lg hover:shadow-xl flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                    </svg>
                    <span>إرسال الإشعار</span>
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            // Supplier and Buyer data from server
            const suppliersData = @json($suppliers);
            const buyersData = @json($buyers);

            // Character counter for message
            const messageTextarea = document.getElementById('message');
            const charCount = document.getElementById('char-count');

            if (messageTextarea && charCount) {
                messageTextarea.addEventListener('input', function() {
                    const length = this.value.length;
                    charCount.textContent = `${length} / 5000`;
                    if (length > 4500) {
                        charCount.classList.add('text-medical-red-500');
                        charCount.classList.remove('text-medical-gray-400');
                    } else {
                        charCount.classList.remove('text-medical-red-500');
                        charCount.classList.add('text-medical-gray-400');
                    }
                });

                // Initialize count
                charCount.textContent = `${messageTextarea.value.length} / 5000`;
            }

            // Toggle specific recipients section
            function toggleSpecificSelection() {
                const specificCheckbox = document.querySelector('input[value="specific"]');
                const specificSection = document.getElementById('specific-recipients-section');
                if (specificCheckbox && specificCheckbox.checked) {
                    specificSection.classList.remove('hidden');
                } else {
                    specificSection.classList.add('hidden');
                    // Clear selections
                    clearSelections('supplier');
                    clearSelections('buyer');
                }
            }

            // Searchable Select Functions
            const searchableSelects = {};

            function initSearchableSelect(type, data) {
                const searchInput = document.getElementById(`${type}-search`);
                const dropdown = document.getElementById(`${type}-dropdown`);
                const optionsContainer = document.getElementById(`${type}-options`);
                const selectedContainer = document.getElementById(`${type}-selected`);
                const hiddenInputsContainer = document.getElementById(`${type}-hidden-inputs`);

                let selectedItems = [];

                // Store functions in object for access
                searchableSelects[type] = {
                    selectItem: function(id, name) {
                        if (selectedItems.some(item => item.id === id)) {
                            return; // Already selected
                        }

                        selectedItems.push({
                            id,
                            name
                        });
                        updateSelectedDisplay();
                        searchInput.value = '';
                        dropdown.classList.add('hidden');
                        searchInput.focus();
                    },
                    removeItem: function(id) {
                        selectedItems = selectedItems.filter(item => item.id !== id);
                        updateSelectedDisplay();
                    },
                    clearSelections: function() {
                        selectedItems = [];
                        updateSelectedDisplay();
                    }
                };

                // Search function
                function performSearch(query) {
                    if (!query || query.trim() === '') {
                        dropdown.classList.add('hidden');
                        return;
                    }

                    const searchTerm = query.toLowerCase();
                    const filtered = Object.entries(data).filter(([id, name]) =>
                        name.toLowerCase().includes(searchTerm)
                    );

                    if (filtered.length === 0) {
                        optionsContainer.innerHTML =
                            '<div class="px-4 py-3 text-sm text-medical-gray-500 text-center">لا توجد نتائج</div>';
                        dropdown.classList.remove('hidden');
                        return;
                    }

                    optionsContainer.innerHTML = filtered.map(([id, name]) => {
                        const isSelected = selectedItems.some(item => item.id === id);
                        const escapedName = name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                        return `
                            <div class="px-4 py-2 hover:bg-medical-blue-50 cursor-pointer transition-colors ${isSelected ? 'bg-medical-green-50 opacity-60' : ''}"
                                 data-id="${id}"
                                 data-name="${escapedName}">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-medical-gray-900">${name}</span>
                                    ${isSelected ? '<span class="text-xs text-medical-green-600">✓ مختار</span>' : ''}
                                </div>
                            </div>
                        `;
                    }).join('');

                    // Add click listeners to options
                    optionsContainer.querySelectorAll('[data-id]').forEach(option => {
                        option.addEventListener('click', function() {
                            const id = this.getAttribute('data-id');
                            const name = this.getAttribute('data-name');
                            searchableSelects[type].selectItem(id, name);
                        });
                    });

                    dropdown.classList.remove('hidden');
                }

                // Update selected display
                function updateSelectedDisplay() {
                    if (selectedItems.length === 0) {
                        selectedContainer.innerHTML = '<p class="text-sm text-medical-gray-400 w-full text-center" id="' +
                            type + '-empty-message">لم يتم اختيار أي ' + (type === 'supplier' ? 'مورد' : 'مشتري') + ' بعد</p>';
                        hiddenInputsContainer.innerHTML = '';
                    } else {
                        const emptyMsg = document.getElementById(`${type}-empty-message`);
                        if (emptyMsg) emptyMsg.remove();

                        selectedContainer.innerHTML = selectedItems.map(item => {
                            const escapedName = item.name.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                            return `
                                <span class="inline-flex items-center gap-2 px-3 py-1.5 bg-medical-blue-100 text-medical-blue-800 rounded-lg text-sm font-medium">
                                    <span>${item.name}</span>
                                    <button type="button" class="remove-item-btn hover:text-medical-red-600 transition-colors" data-id="${item.id}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </span>
                            `;
                        }).join('');

                        // Add remove listeners
                        selectedContainer.querySelectorAll('.remove-item-btn').forEach(btn => {
                            btn.addEventListener('click', function() {
                                const id = this.getAttribute('data-id');
                                searchableSelects[type].removeItem(id);
                            });
                        });

                        hiddenInputsContainer.innerHTML = selectedItems.map(item =>
                            `<input type="hidden" name="recipient_ids[]" value="${item.id}">`
                        ).join('');
                    }
                }

                // Event listeners
                searchInput.addEventListener('input', function() {
                    performSearch(this.value);
                });

                searchInput.addEventListener('focus', function() {
                    if (this.value.trim() !== '') {
                        performSearch(this.value);
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.classList.add('hidden');
                    }
                });

                // Handle keyboard navigation
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        dropdown.classList.add('hidden');
                    }
                });
            }

            // Helper functions
            function clearSelections(type) {
                if (searchableSelects[type]) {
                    searchableSelects[type].clearSelections();
                }
            }

            // Update recipients function
            function updateRecipients() {
                const checkboxes = document.querySelectorAll('.recipient-checkbox');
                const selected = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);

                // Update each recipient option
                ['suppliers', 'buyers', 'both', 'specific'].forEach(type => {
                    const checkbox = document.querySelector(`input[value="${type}"]`);
                    const label = document.getElementById(`recipient-${type}`);
                    const icon = document.getElementById(`icon-${type}`);
                    const check = document.getElementById(`check-${type}`);

                    if (checkbox && checkbox.checked) {
                        // Selected state
                        if (type === 'both') {
                            label.classList.remove('border-medical-gray-300');
                            label.classList.add('border-medical-blue-500', 'bg-medical-blue-50');
                            icon.classList.remove('bg-medical-gray-100');
                            icon.classList.add('bg-medical-blue-500');
                            icon.querySelector('svg').classList.remove('text-medical-gray-400');
                            icon.querySelector('svg').classList.add('text-white');
                            check.classList.remove('text-medical-gray-300');
                            check.classList.add('text-medical-blue-500');
                        } else if (type === 'specific') {
                            label.classList.remove('border-medical-gray-300');
                            label.classList.add('border-medical-purple-500', 'bg-medical-purple-50');
                            icon.classList.remove('bg-medical-gray-100');
                            icon.classList.add('bg-medical-purple-500');
                            icon.querySelector('svg').classList.remove('text-medical-gray-400');
                            icon.querySelector('svg').classList.add('text-white');
                            check.classList.remove('text-medical-gray-300');
                            check.classList.add('text-medical-purple-500');
                        } else {
                            label.classList.remove('border-medical-gray-300');
                            label.classList.add('border-medical-green-500', 'bg-medical-green-50');
                            icon.classList.remove('bg-medical-gray-100');
                            icon.classList.add('bg-medical-green-500');
                            icon.querySelector('svg').classList.remove('text-medical-gray-400');
                            icon.querySelector('svg').classList.add('text-white');
                            check.classList.remove('text-medical-gray-300');
                            check.classList.add('text-medical-green-500');
                        }
                    } else {
                        // Unselected state
                        if (type === 'specific') {
                            label.classList.remove('border-medical-green-500', 'bg-medical-green-50',
                                'border-medical-blue-500', 'bg-medical-blue-50');
                            label.classList.add('border-medical-gray-300');
                            icon.classList.remove('bg-medical-purple-500');
                            icon.classList.add('bg-medical-gray-100');
                            icon.querySelector('svg').classList.remove('text-white');
                            icon.querySelector('svg').classList.add('text-medical-gray-400');
                            check.classList.remove('text-medical-purple-500');
                            check.classList.add('text-medical-gray-300');
                        } else {
                            label.classList.remove('border-medical-green-500', 'bg-medical-green-50',
                                'border-medical-blue-500', 'bg-medical-blue-50');
                            label.classList.add('border-medical-gray-300');
                            icon.classList.remove('bg-medical-green-500', 'bg-medical-blue-500');
                            icon.classList.add('bg-medical-gray-100');
                            icon.querySelector('svg').classList.remove('text-white');
                            icon.querySelector('svg').classList.add('text-medical-gray-400');
                            check.classList.remove('text-medical-green-500', 'text-medical-blue-500');
                            check.classList.add('text-medical-gray-300');
                        }
                    }
                });
            }

            // Initialize on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateRecipients();
                // Initialize searchable selects
                initSearchableSelect('supplier', suppliersData);
                initSearchableSelect('buyer', buyersData);
            });
        </script>
    @endpush

</x-dashboard.layout>
