{{-- Admin Permissions Management - Modern Card-Based Design --}}
<x-dashboard.layout title="إدارة الصلاحيات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header with Search --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display mb-2">إدارة الصلاحيات</h1>
                <p class="text-medical-gray-600">عرض وإدارة جميع الصلاحيات المتاحة في النظام</p>
            </div>
            <div class="relative flex-1 md:flex-initial md:w-80">
                <input type="text" id="searchInput" placeholder="بحث في الصلاحيات..." 
                    class="w-full pl-10 pr-4 py-2.5 border-2 border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500 transition-all"
                    onkeyup="filterPermissions()">
                <svg class="absolute right-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-gradient-to-br from-medical-blue-500 to-medical-blue-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-blue-100 text-sm font-medium">إجمالي الصلاحيات</p>
                <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <p class="text-3xl font-bold">{{ $permissions->flatten()->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-medical-green-500 to-medical-green-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-green-100 text-sm font-medium">عدد الوحدات</p>
                <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
            </div>
            <p class="text-3xl font-bold">{{ $permissions->count() }}</p>
        </div>

        <div class="bg-gradient-to-br from-medical-purple-500 to-medical-purple-600 rounded-2xl p-6 text-white shadow-lg">
            <div class="flex items-center justify-between mb-2">
                <p class="text-purple-100 text-sm font-medium">الصلاحيات المستخدمة</p>
                <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-3xl font-bold">{{ collect($permissionRoleCounts)->filter(fn($count) => $count > 0)->count() }}</p>
        </div>
    </div>

    {{-- Permissions by Module (Accordion Style) --}}
    <div class="space-y-4" id="permissionsContainer">
        @foreach ($permissions as $module => $modulePermissions)
            <div class="permission-module bg-white rounded-2xl shadow-lg overflow-hidden border-2 border-medical-gray-200 hover:border-medical-blue-300 transition-all">
                {{-- Module Header --}}
                <div class="bg-gradient-to-r from-medical-gray-50 to-medical-gray-100 px-6 py-5 flex items-center justify-between cursor-pointer"
                    onclick="toggleModule('{{ $module }}')">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gradient-to-br from-medical-blue-500 to-medical-green-500 rounded-xl flex items-center justify-center text-white font-bold text-lg">
                            {{ mb_substr($module, 0, 1) }}
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-medical-gray-900 capitalize">{{ $module }}</h2>
                            <p class="text-sm text-medical-gray-600">{{ $modulePermissions->count() }} صلاحية</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <span class="px-4 py-2 bg-white rounded-full text-sm font-semibold text-medical-gray-700">
                            {{ $modulePermissions->count() }} صلاحية
                        </span>
                        <svg class="w-6 h-6 text-medical-gray-600 module-icon transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                {{-- Module Permissions (Collapsible) --}}
                <div class="module-content hidden px-6 py-6 bg-white">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($modulePermissions as $permission)
                            <a href="{{ route('admin.permissions.show', $permission) }}"
                                class="permission-item group block p-4 border-2 border-medical-gray-200 rounded-xl hover:border-medical-blue-400 hover:bg-medical-blue-50 transition-all duration-200 hover:shadow-md">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-medical-gray-900 group-hover:text-medical-blue-600 mb-1">
                                            {{ $permission->ar_name ?? $permission->name }}
                                        </h3>
                                        <p class="text-xs text-medical-gray-500 capitalize">
                                            {{ $permission->name }}
                                        </p>
                                    </div>
                                    <svg class="w-5 h-5 text-medical-gray-400 group-hover:text-medical-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                
                                {{-- Usage Badge --}}
                                <div class="flex items-center gap-2 mt-3">
                                    @if (($permissionRoleCounts[$permission->id] ?? 0) > 0)
                                        <span class="px-3 py-1 bg-medical-green-100 text-medical-green-800 rounded-full text-xs font-semibold">
                                            {{ $permissionRoleCounts[$permission->id] }} دور
                                        </span>
                                    @else
                                        <span class="px-3 py-1 bg-medical-gray-100 text-medical-gray-600 rounded-full text-xs font-medium">
                                            غير مستخدم
                                        </span>
                                    @endif
                                    <span class="px-2 py-1 bg-medical-blue-100 text-medical-blue-700 rounded text-xs">
                                        {{ $permission->guard_name }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        // Toggle module expand/collapse
        function toggleModule(module) {
            const moduleEl = event.currentTarget.closest('.permission-module');
            const content = moduleEl.querySelector('.module-content');
            const icon = moduleEl.querySelector('.module-icon');
            
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }

        // Filter permissions by search
        function filterPermissions() {
            const search = document.getElementById('searchInput').value.toLowerCase();
            const items = document.querySelectorAll('.permission-item');
            const modules = document.querySelectorAll('.permission-module');
            
            items.forEach(item => {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(search) ? 'block' : 'none';
            });
            
            // Show/hide modules and expand if has matches
            modules.forEach(module => {
                const visibleItems = module.querySelectorAll('.permission-item[style*="block"], .permission-item:not([style*="none"])');
                if (visibleItems.length > 0) {
                    module.style.display = 'block';
                    if (search) {
                        module.querySelector('.module-content').classList.remove('hidden');
                        module.querySelector('.module-icon').classList.add('rotate-180');
                    }
                } else {
                    module.style.display = 'none';
                }
            });
        }

        // Expand first module by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstModule = document.querySelector('.permission-module');
            if (firstModule) {
                firstModule.querySelector('.module-content').classList.remove('hidden');
                firstModule.querySelector('.module-icon').classList.add('rotate-180');
            }
        });
    </script>

</x-dashboard.layout>
