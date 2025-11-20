{{-- Admin Activity Log - Show Activity Details --}}
<x-dashboard.layout title="تفاصيل النشاط" userRole="admin" :userName="auth()->user()->name" userType="مدير النظام">
    
    {{-- 
    ADMIN ACTIVITY LOG SHOW PAGE
    Controller: ActivityLogController@show
    Data: $activity = Activity::with(['causer', 'subject'])->find($id);
    --}}

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-medical-gray-900 font-display">تفاصيل النشاط</h1>
                <p class="mt-2 text-medical-gray-600">عرض معلومات تفصيلية عن النشاط</p>
            </div>
            <a href="{{ route('admin.activity') }}"
                class="inline-flex items-center space-x-2 space-x-reverse px-6 py-3 bg-medical-gray-100 text-medical-gray-700 rounded-xl hover:bg-medical-gray-200 transition-all duration-200 font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>العودة إلى القائمة</span>
            </a>
        </div>
    </div>

    {{-- Activity Details Card --}}
    <div class="bg-white rounded-2xl shadow-medical p-8 mb-6">
        <h2 class="text-xl font-bold text-medical-gray-900 mb-6 font-display">معلومات النشاط</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Activity ID --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">معرف النشاط</label>
                <p class="text-medical-gray-900 font-medium">#{{ $activity->id }}</p>
            </div>

            {{-- Log Name --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">نوع السجل</label>
                <span class="px-3 py-1 inline-flex text-xs leading-5 font-medium rounded-full bg-medical-blue-100 text-medical-blue-800">
                    {{ $activity->log_name }}
                </span>
            </div>

            {{-- Description --}}
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">الوصف</label>
                <p class="text-medical-gray-900 font-medium">{{ $activity->description }}</p>
            </div>

            {{-- Causer (User) --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">المستخدم</label>
                @if($activity->causer)
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-medical-blue-100 rounded-full flex items-center justify-center ml-3">
                        <span class="text-sm font-medium text-medical-blue-600">
                            {{ substr($activity->causer->name, 0, 1) }}
                        </span>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-medical-gray-900">{{ $activity->causer->name }}</div>
                        <div class="text-xs text-medical-gray-500">{{ $activity->causer->email }}</div>
                    </div>
                </div>
                @else
                <p class="text-medical-gray-500">النظام</p>
                @endif
            </div>

            {{-- Subject Type --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">نوع الكائن</label>
                <p class="text-medical-gray-900 font-medium">{{ $activity->subject_type ?? 'غير محدد' }}</p>
            </div>

            {{-- Created At --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">تاريخ الإنشاء</label>
                <p class="text-medical-gray-900 font-medium">{{ $activity->created_at->format('Y-m-d H:i:s') }}</p>
                <p class="text-xs text-medical-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
            </div>

            {{-- Updated At --}}
            <div>
                <label class="block text-sm font-medium text-medical-gray-600 mb-2">آخر تحديث</label>
                <p class="text-medical-gray-900 font-medium">{{ $activity->updated_at->format('Y-m-d H:i:s') }}</p>
                <p class="text-xs text-medical-gray-500 mt-1">{{ $activity->updated_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    {{-- Properties Card --}}
    @if($activity->properties && $activity->properties->isNotEmpty())
    <div class="bg-white rounded-2xl shadow-medical p-8 mb-6">
        <h2 class="text-xl font-bold text-medical-gray-900 mb-6 font-display">الخصائص</h2>
        
        <div class="bg-medical-gray-50 rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm text-medical-gray-900 font-mono">{{ json_encode($activity->properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif

    {{-- Subject Details Card --}}
    @if($activity->subject)
    <div class="bg-white rounded-2xl shadow-medical p-8">
        <h2 class="text-xl font-bold text-medical-gray-900 mb-6 font-display">تفاصيل الكائن</h2>
        
        <div class="bg-medical-gray-50 rounded-xl p-4 overflow-x-auto">
            <pre class="text-sm text-medical-gray-900 font-mono">{{ json_encode($activity->subject->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>
    </div>
    @endif

</x-dashboard.layout>

