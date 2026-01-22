{{-- Permission Audit Log --}}
<x-dashboard.layout title="سجل تدقيق الصلاحيات" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display mb-2">سجل تدقيق الصلاحيات</h1>
                <p class="text-medical-gray-600">سجل كامل لجميع التغييرات على الصلاحيات (من قام بالتغيير، متى، وماذا تم تغييره)</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.role-permissions.usage-report') }}"
                    class="px-4 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        تقرير الاستخدام
                    </span>
                </a>
                <a href="{{ route('admin.role-permissions.index') }}"
                    class="px-4 py-2 bg-medical-gray-200 text-medical-gray-700 rounded-xl hover:bg-medical-gray-300 transition">
                    رجوع
                </a>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600 mb-1">إجمالي التغييرات</p>
                    <p class="text-3xl font-bold text-medical-blue-600">{{ $stats['total_changes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
            </div>
            <p class="text-xs text-medical-gray-500 mt-2">آخر {{ $stats['period_days'] }} يوم</p>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600 mb-1">تغييرات المستخدمين</p>
                    <p class="text-3xl font-bold text-medical-green-600">{{ $stats['user_changes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-full flex items-center justify-between">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600 mb-1">تغييرات الأدوار</p>
                    <p class="text-3xl font-bold text-medical-purple-600">{{ $stats['role_changes'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600 mb-1">أكثر المدراء نشاطاً</p>
                    <p class="text-lg font-bold text-medical-gray-900">
                        {{ $stats['top_admins']->first()->adminUser->name ?? 'لا يوجد' }}
                    </p>
                </div>
            </div>
            @if($stats['top_admins']->first())
                <p class="text-xs text-medical-gray-500 mt-2">
                    {{ $stats['top_admins']->first()->changes_count }} تغيير
                </p>
            @endif
        </div>
    </div>

    {{-- Audit Log Table --}}
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-medical-gray-200">
                <thead class="bg-medical-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase">التاريخ</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase">المدير</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase">الإجراء</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase">الهدف</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-medical-gray-500 uppercase">التفاصيل</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-medical-gray-200">
                    @forelse($audits as $audit)
                        <tr class="hover:bg-medical-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-medical-gray-900">
                                {{ $audit->created_at->format('Y-m-d H:i') }}
                                <br>
                                <span class="text-xs text-medical-gray-500">{{ $audit->created_at->diffForHumans() }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div
                                        class="w-8 h-8 bg-medical-blue-100 rounded-full flex items-center justify-center text-medical-blue-600 font-bold text-sm">
                                        {{ mb_substr($audit->adminUser->name ?? 'U', 0, 1) }}
                                    </div>
                                    <div class="mr-3">
                                        <div class="text-sm font-medium text-medical-gray-900">
                                            {{ $audit->adminUser->name ?? 'غير معروف' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $audit->action === 'synced' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $audit->action === 'template_applied' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $audit->action === 'role_updated' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $audit->action === 'bulk_assigned' ? 'bg-yellow-100 text-yellow-800' : '' }}">
                                    {{ $audit->action_name }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <span class="font-medium">{{ $audit->entity_name }}</span>
                                <br>
                                <span class="text-xs text-medical-gray-500">{{ $audit->entity_type === 'user' ? 'مستخدم' : 'دور' }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-medical-gray-900">
                                {{ $audit->summary }}
                                @if($audit->template_name)
                                    <br>
                                    <span class="text-xs text-medical-purple-600">📋 {{ $audit->template_name }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-medical-gray-500">
                                لا توجد سجلات تدقيق
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-medical-gray-200">
            {{ $audits->links() }}
        </div>
    </div>

</x-dashboard.layout>
