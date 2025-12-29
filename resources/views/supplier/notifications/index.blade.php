{{-- Supplier Notifications - Index --}}
<x-dashboard.layout title="الإشعارات" userRole="supplier" :userName="auth()->user()->name" userType="مورد">

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الإشعارات</h1>
                <p class="mt-2 text-medical-gray-600">جميع الإشعارات والتنبيهات الخاصة بك</p>
            </div>
            <div class="flex items-center gap-3">
                @if($stats['unread'] > 0)
                    <form action="{{ route('supplier.notifications.read-all') }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <span>تحديد الكل كمقروء</span>
                        </button>
                    </form>
                @endif
                @if($stats['total'] > 0)
                    <form action="{{ route('supplier.notifications.destroy-all') }}" method="POST"
                        onsubmit="return confirm('هل أنت متأكد من حذف جميع الإشعارات؟ هذا الإجراء لا يمكن التراجع عنه.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-red-600 text-white rounded-xl hover:bg-medical-red-700 transition-all duration-200 font-medium shadow-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>حذف الكل</span>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Flash Messages --}}
    @if (session('success'))
        <div class="bg-medical-green-50 border border-medical-green-200 text-medical-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error') || $errors->any())
        <div class="bg-medical-red-50 border border-medical-red-200 text-medical-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                {{ session('error') }}
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">إجمالي الإشعارات</p>
                    <p class="text-3xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">غير مقروءة</p>
                    <p class="text-3xl font-bold text-medical-red-600 mt-1">{{ $stats['unread'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-red-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-medical p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-medical-gray-600">مقروءة</p>
                    <p class="text-3xl font-bold text-medical-green-600 mt-1">{{ $stats['read'] }}</p>
                </div>
                <div class="w-12 h-12 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" action="{{ route('supplier.notifications.index') }}" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-medical-gray-700 mb-2">بحث</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}"
                    placeholder="ابحث في العنوان أو الرسالة..."
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="status" class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" id="status"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
                    <option value="">الكل</option>
                    <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>

            <div class="w-48">
                <label for="from_date" class="block text-sm font-medium text-medical-gray-700 mb-2">من تاريخ</label>
                <input type="date" name="from_date" id="from_date" value="{{ request('from_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="w-48">
                <label for="to_date" class="block text-sm font-medium text-medical-gray-700 mb-2">إلى تاريخ</label>
                <input type="date" name="to_date" id="to_date" value="{{ request('to_date') }}"
                    class="w-full px-4 py-3 border border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-medical-blue-500">
            </div>

            <div class="flex gap-2">
                <button type="submit"
                    class="px-6 py-3 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700 transition-all duration-200 font-medium">
                    تصفية
                </button>
                <a href="{{ route('supplier.notifications.index') }}"
                    class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if ($notifications->isEmpty())
            <div class="text-center py-16">
                <svg class="mx-auto h-16 w-16 text-medical-gray-400" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-medical-gray-900">لا توجد إشعارات</h3>
                <p class="mt-2 text-medical-gray-600">سيتم عرض الإشعارات الجديدة هنا.</p>
            </div>
        @else
            <div class="divide-y divide-medical-gray-200">
                @foreach ($notifications as $notification)
                    <div class="p-6 hover:bg-medical-gray-50 transition-colors duration-150 {{ !$notification->read_at ? 'bg-medical-blue-50/50' : '' }}">
                        <div class="flex items-start gap-4">
                            {{-- Icon --}}
                            <div class="flex-shrink-0">
                                @php
                                    $type = $notification->data['type'] ?? 'info';
                                    $iconColors = [
                                        'info' => 'bg-medical-blue-100 text-medical-blue-600',
                                        'success' => 'bg-medical-green-100 text-medical-green-600',
                                        'warning' => 'bg-medical-yellow-100 text-medical-yellow-600',
                                        'danger' => 'bg-medical-red-100 text-medical-red-600',
                                        'primary' => 'bg-medical-purple-100 text-medical-purple-600',
                                    ];
                                @endphp
                                <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $iconColors[$type] ?? $iconColors['info'] }}">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="font-semibold text-medical-gray-900 {{ !$notification->read_at ? 'text-medical-blue-700' : '' }}">
                                            {{ $notification->data['title'] ?? 'إشعار' }}
                                        </p>
                                        <p class="mt-1 text-medical-gray-600">
                                            {{ $notification->data['message'] ?? '' }}
                                        </p>
                                        <p class="mt-2 text-xs text-medical-gray-500">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>

                                    {{-- Unread indicator --}}
                                    @if(!$notification->read_at)
                                        <span class="flex-shrink-0 w-3 h-3 bg-medical-blue-600 rounded-full"></span>
                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="mt-4 flex items-center gap-3">
                                    @if(isset($notification->data['url']) && $notification->data['url'])
                                        <a href="{{ $notification->data['url'] }}"
                                            class="inline-flex items-center px-4 py-2 bg-medical-blue-50 text-medical-blue-700 rounded-lg hover:bg-medical-blue-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            عرض التفاصيل
                                        </a>
                                    @endif

                                    @if(!$notification->read_at)
                                        <form action="{{ route('supplier.notifications.read', $notification->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                class="inline-flex items-center px-4 py-2 bg-medical-green-50 text-medical-green-700 rounded-lg hover:bg-medical-green-100 transition-colors duration-150 text-sm font-medium">
                                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                تحديد كمقروء
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('supplier.notifications.destroy', $notification->id) }}" method="POST" class="inline"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 bg-medical-red-50 text-medical-red-700 rounded-lg hover:bg-medical-red-100 transition-colors duration-150 text-sm font-medium">
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            حذف
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-medical-gray-200">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>

</x-dashboard.layout>

