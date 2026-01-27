{{-- Buyer Notifications - Index --}}
<x-dashboard.layout title="الإشعارات" userRole="buyer" :userName="auth()->user()->name" userType="مشتري">

    {{-- Page Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-medical-gray-900 font-display">الإشعارات</h1>
            <p class="mt-2 text-medical-gray-600">إدارة إشعاراتك والتنبيهات</p>
        </div>
        <div class="flex items-center gap-3">
            @if($stats['unread'] > 0)
                <form action="{{ route('buyer.notifications.read-all') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-medical-blue-50 text-medical-blue-600 rounded-xl hover:bg-medical-blue-100 font-medium">
                        تحديد الكل كمقروء
                    </button>
                </form>
            @endif
            @if($stats['total'] > 0)
                <form action="{{ route('buyer.notifications.destroy-all') }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف جميع الإشعارات؟')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-medical-red-50 text-medical-red-600 rounded-xl hover:bg-medical-red-100 font-medium">
                        حذف الكل
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">إجمالي الإشعارات</p>
                    <p class="text-2xl font-bold text-medical-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">غير مقروءة</p>
                    <p class="text-2xl font-bold text-medical-yellow-600 mt-1">{{ $stats['unread'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-medical p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs text-medical-gray-600">مقروءة</p>
                    <p class="text-2xl font-bold text-medical-green-600 mt-1">{{ $stats['read'] }}</p>
                </div>
                <div class="w-10 h-10 bg-medical-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-medical-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-2xl shadow-medical p-6 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">البحث</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="ابحث في الإشعارات..."
                    class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
            </div>
            <div>
                <label class="block text-sm font-medium text-medical-gray-700 mb-2">الحالة</label>
                <select name="status" class="w-full px-4 py-2 border border-medical-gray-200 rounded-xl">
                    <option value="">الكل</option>
                    <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>غير مقروءة</option>
                    <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>مقروءة</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="px-6 py-2 bg-medical-blue-600 text-white rounded-xl hover:bg-medical-blue-700">بحث</button>
                <a href="{{ route('buyer.notifications.index') }}" class="px-4 py-2 bg-medical-gray-100 rounded-xl">إعادة تعيين</a>
            </div>
        </form>
    </div>

    {{-- Notifications List --}}
    <div class="bg-white rounded-2xl shadow-medical overflow-hidden">
        @if(isset($error))
            <div class="p-6 text-center text-medical-red-600">{{ $error }}</div>
        @elseif($notifications->isEmpty())
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-medical-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-medical-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-medical-gray-900 mb-2">لا توجد إشعارات</h3>
                <p class="text-medical-gray-600">ستظهر الإشعارات هنا عند وجود تحديثات جديدة</p>
            </div>
        @else
            <div class="divide-y divide-medical-gray-100">
                @foreach($notifications as $notification)
                    <div class="p-4 hover:bg-medical-gray-50 transition-colors {{ !$notification->read_at ? 'bg-medical-blue-50' : '' }}">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 rounded-full {{ !$notification->read_at ? 'bg-medical-blue-100' : 'bg-medical-gray-100' }} flex items-center justify-center">
                                    <svg class="w-5 h-5 {{ !$notification->read_at ? 'text-medical-blue-600' : 'text-medical-gray-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-medical-gray-900">{{ $notification->data['title'] ?? 'إشعار' }}</p>
                                <p class="text-sm text-medical-gray-600 mt-1">{{ $notification->data['message'] ?? '' }}</p>
                                <p class="text-xs text-medical-gray-400 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-2 flex-wrap">
                                @if(isset($notification->data['sent_by_id']) && $notification->data['sent_by_id'])
                                    <button type="button" onclick="openReplyModal('{{ $notification->id }}', '{{ addslashes($notification->data['title'] ?? 'إشعار') }}')"
                                        class="px-3 py-1.5 bg-medical-purple-50 text-medical-purple-700 rounded-lg hover:bg-medical-purple-100 text-sm font-medium flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                        </svg>
                                        رد
                                    </button>
                                @endif
                                @if(!$notification->read_at)
                                    <form action="{{ route('buyer.notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-medical-blue-600 hover:text-medical-blue-700 text-sm font-medium">
                                            تحديد كمقروء
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('buyer.notifications.destroy', $notification->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-medical-red-600 hover:text-medical-red-700">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-6 py-4 border-t border-medical-gray-100">{{ $notifications->links() }}</div>
        @endif
    </div>

    {{-- Reply Modal --}}
    <div id="replyModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center" style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-medical-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-medical-gray-900">الرد على الإشعار</h3>
                    <button onclick="closeReplyModal()" class="text-medical-gray-400 hover:text-medical-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p id="replyNotificationTitle" class="mt-2 text-sm text-medical-gray-600"></p>
            </div>
            <form id="replyForm" method="POST" class="p-6">
                @csrf
                <div class="mb-6">
                    <label for="replyMessage" class="block text-sm font-bold text-medical-gray-900 mb-2">
                        رسالة الرد <span class="text-medical-red-500">*</span>
                    </label>
                    <textarea name="message" id="replyMessage" rows="6" required
                        placeholder="اكتب ردك هنا..."
                        class="w-full px-4 py-3 border-2 border-medical-gray-300 rounded-xl focus:ring-2 focus:ring-medical-blue-500 focus:border-transparent transition-all resize-none"
                        maxlength="5000"></textarea>
                    <p class="mt-1 text-xs text-medical-gray-500">يجب ألا يتجاوز 5000 حرف</p>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" onclick="closeReplyModal()"
                        class="px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition font-medium">
                        إلغاء
                    </button>
                    <button type="submit"
                        class="px-6 py-3 bg-gradient-to-r from-medical-purple-600 to-medical-purple-700 text-white rounded-xl hover:from-medical-purple-700 hover:to-medical-purple-800 transition font-medium">
                        إرسال الرد
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            function openReplyModal(notificationId, notificationTitle) {
                document.getElementById('replyNotificationTitle').textContent = 'الرد على: ' + notificationTitle;
                document.getElementById('replyForm').action = '{{ route("buyer.notifications.reply", ":id") }}'.replace(':id', notificationId);
                document.getElementById('replyModal').classList.remove('hidden');
                document.getElementById('replyModal').style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }

            function closeReplyModal() {
                document.getElementById('replyModal').classList.add('hidden');
                document.getElementById('replyModal').style.display = 'none';
                document.body.style.overflow = '';
                document.getElementById('replyForm').reset();
            }

            // Close modal on outside click
            document.getElementById('replyModal').addEventListener('click', function(e) {
                if (e.target === this) {
                    closeReplyModal();
                }
            });
        </script>
    @endpush

</x-dashboard.layout>

