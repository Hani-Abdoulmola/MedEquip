<x-dashboard.layout>

    {{-- <div class="max-w-4xl mx-auto px-6 py-8" x-data="{ showPassword: false, showPasswordConfirm: false }"> --}}

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-4 mb-4">
            <a href="{{ route('admin.users') }}"
                class="inline-flex items-center justify-center w-10 h-10 bg-gray-100 hover:bg-gray-200 rounded-lg active:scale-95 transition-all duration-200 cursor-pointer">
                <i class="fas fa-arrow-right text-gray-600"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">تعديل بيانات المستخدم</h1>
                <p class="text-gray-600 mt-1">تحديث معلومات المستخدم في النظام</p>
            </div>
            <a href="{{ route('admin.users') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة للقائمة</span>
            </a>
        </div>
    </div>

    {{-- Edit User Form --}}
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Full Name --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الاسم الكامل *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">البريد الإلكتروني *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">رقم الهاتف *</label>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                </div>

                {{-- User Type --}}
                <div class="flex flex-col items-end">
                    <label for="user_type_id" class="mb-2 text-sm font-medium text-medical-gray-700 w-full text-right">
                        نوع المستخدم <span class="text-red-500">*</span>
                    </label>
                    <div class="relative w-full" dir="rtl">
                        <select id="user_type_id" name="user_type_id" x-model="userType"
                            class="w-full px-4 py-3 pr-10 pl-4 border border-medical-gray-300 rounded-xl appearance-none focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all duration-200 @error('user_type_id') border-red-500 @enderror text-right">
                            <option value="">اختر نوع المستخدم</option>
                            <option value="1">مدير النظام</option>
                            {{-- <option value="2">مورد</option> --}}
                            {{-- <option value="3">مشتري</option> --}}
                        </select>
                    </div>
                    @error('user_type_id')
                        <p class="mt-2 text-sm text-red-600 w-full text-right">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة *</label>
                    <select name="status" required
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>نشط
                        </option>
                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>غير
                            نشط</option>
                        <option value="pending" {{ old('status', $user->status) == 'pending' ? 'selected' : '' }}>قيد
                            المراجعة</option>
                    </select>
                </div>

                {{-- Email Verified --}}
                <div class="flex items-center">
                    <input type="checkbox" name="email_verified" id="email_verified"
                        {{ old('email_verified', $user->email_verified_at ? 'checked' : null) ? 'checked' : '' }}
                        class="w-5 h-5 text-medical-blue-500 border-medical-gray-300 rounded focus:ring-medical-blue-500">
                    <label for="email_verified" class="mr-3 text-sm font-medium text-medical-gray-700">
                        البريد الإلكتروني موثق
                    </label>
                </div>
            </div>

            {{-- Role Selection Section --}}
            <div class="mt-8 pt-8 border-t border-medical-gray-200">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">الدور</h3>

                <div class="max-w-md">
                    <label for="role" class="block text-sm font-medium text-medical-gray-700 mb-2">
                        تعيين دور
                    </label>
                    <select id="role" name="role"
                        class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                        <option value="">بدون دور</option>
                        @foreach ($roles as $roleName => $roleLabel)
                            <option value="{{ $roleName }}"
                                {{ old('role', $user->roles->first()?->name) === $roleName ? 'selected' : '' }}>
                                {{ $roleLabel }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-medical-gray-500">الدور اختياري - يمكن تعيين الصلاحيات مباشرة أدناه</p>
                </div>
            </div>


            {{-- Password Change Section --}}
            <div class="mt-8 pt-8 border-t border-medical-gray-200">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">تغيير كلمة المرور (اختياري)</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">كلمة المرور
                            الجديدة</label>
                        <input type="password" name="password"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="اتركه فارغاً إذا لم ترغب في التغيير">
                    </div>

                    {{-- Confirm New Password --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">تأكيد كلمة
                            المرور</label>
                        <input type="password" name="password_confirmation"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200"
                            placeholder="تأكيد كلمة المرور الجديدة">
                    </div>
                </div>
            </div>

            {{-- Additional Information Section --}}
            <div class="mt-8 pt-8 border-t border-medical-gray-200">
                <h3 class="text-lg font-semibold text-medical-gray-900 mb-4">معلومات إضافية</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Address --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">العنوان</label>
                        <textarea name="address" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">{{ old('address', $user->address) }}</textarea>
                    </div>

                    {{-- City --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">المدينة</label>
                        <input type="text" name="city" value="{{ old('city', $user->city) }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    {{-- Country --}}
                    <div>
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">الدولة</label>
                        <input type="text" name="country" value="{{ old('country', $user->country) }}"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">
                    </div>

                    {{-- Notes --}}
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-medical-gray-700 mb-2">ملاحظات</label>
                        <textarea name="notes" rows="3"
                            class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all duration-200">{{ old('notes', $user->notes) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="mt-8 flex items-center justify-between">
                <button type="button"
                    class="px-6 py-3 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 transition-colors duration-200 font-medium"
                    onclick="if(confirm('هل أنت متأكد من حذف المستخدم؟')) { document.getElementById('delete-user-form').submit(); }">
                    حذف المستخدم
                </button>
                <div class="flex items-center space-x-4 space-x-reverse">
                    <a href="{{ route('admin.users') }}"
                        class="px-6 py-3 border border-medical-gray-300 text-medical-gray-700 rounded-xl hover:bg-medical-gray-50 transition-colors duration-200 font-medium">
                        إلغاء
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-medical-blue-500 to-medical-green-500 text-white rounded-xl hover:shadow-medical-lg transition-all duration-200 font-medium">
                        حفظ التغييرات
                    </button>
                </div>
            </div>
        </form>

        {{-- Hidden Delete Form --}}
        <form id="delete-user-form" action="{{ route('admin.users.destroy', $user) }}" method="POST"
            style="display: none;">
            @csrf
            @method('DELETE')
        </form>

        {{-- Permission Update Form (Separate) --}}
        @can('users.manage_permissions')
            <form method="POST" action="{{ route('admin.users.update-permissions', $user) }}" class="mt-8 pt-8 border-t border-medical-gray-200">
                @csrf
                @method('PUT')
                
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-medical-gray-900">إدارة الصلاحيات</h3>
                            <p class="text-sm text-medical-gray-600 mt-1">تعيين الصلاحيات مباشرة لهذا المستخدم</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <button type="button" onclick="selectAllPermissions()"
                                class="text-sm text-medical-blue-600 hover:text-medical-blue-700 font-medium">
                                تحديد الكل
                            </button>
                            <button type="button" onclick="deselectAllPermissions()"
                                class="text-sm text-medical-gray-600 hover:text-medical-gray-700 font-medium">
                                إلغاء التحديد
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div class="border border-medical-gray-200 rounded-xl p-6">
                                <h4 class="text-lg font-semibold text-medical-gray-900 mb-4 capitalize">
                                    {{ $module }}
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    @foreach ($modulePermissions as $permission)
                                        <label
                                            class="flex items-center space-x-3 space-x-reverse p-3 rounded-lg hover:bg-medical-gray-50 cursor-pointer transition-colors duration-200">
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                                {{ in_array($permission->id, old('permissions', $userPermissions ?? [])) ? 'checked' : '' }}
                                                class="w-5 h-5 text-medical-blue-600 border-medical-gray-300 rounded focus:ring-2 focus:ring-medical-blue-500">
                                            <span class="text-sm font-medium text-medical-gray-700">{{ $permission->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 border-t border-medical-gray-200">
                    <button type="submit"
                        class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-purple-600 text-white rounded-xl hover:bg-medical-purple-700 transition-all duration-200 font-medium shadow-medical">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        <span>حفظ الصلاحيات</span>
                    </button>
                </div>
            </form>
        @endcan
    </div>

    <script>
        function selectAllPermissions() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = true;
            });
        }

        function deselectAllPermissions() {
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });
        }
    </script>

</x-dashboard.layout>
