<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * إنشاء جدول طلبات عروض الأسعار (RFQs)
     */
    public function up(): void
    {
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id()->comment('🔑 المعرّف الأساسي لطلب عرض السعر');

            $table->foreignId('buyer_id')
                ->constrained('buyers')
                ->cascadeOnDelete()
                ->comment('🏢 FK -> buyers.id يشير إلى المشتري (المنشأة) التي أنشأت الطلب');

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete()
                ->comment('👤 FK -> users.id يشير إلى المستخدم الذي أنشأ الطلب فعليًا');

            $table->string('reference_code', 100)->unique()->comment('🔢 كود مرجعي فريد لتعقّب الطلب');
            $table->string('title', 200)->comment('📝 عنوان مختصر للطلب مثل: طلب أجهزة تعقيم');
            $table->text('description')->nullable()->comment('📄 تفاصيل الطلب مثل المواصفات الفنية أو الكميات المطلوبة');
            $table->timestamp('deadline')->nullable()->comment('⏰ تاريخ انتهاء صلاحية الطلب لتقديم العروض');
            $table->timestamp('closed_at')->nullable()->comment('📅 تاريخ الإغلاق الفعلي للطلب عند انتهاء التقديم أو الإلغاء');
            $table->enum('status', ['open', 'closed', 'cancelled'])->default('open')->comment('📌 حالة الطلب: مفتوح / مغلق / ملغى');
            $table->boolean('is_public')->default(true)->comment('🌐 هل الطلب مرئي لجميع الموردين أم خاص؟');

            $table->timestamps(); // created_at, updated_at
            $table->softDeletes()->comment('🗑️ الحذف المنطقي دون فقد البيانات');

            // 🔍 فهارس لتحسين الأداء في البحث وإدارة الطلبات
            $table->index(['buyer_id', 'status', 'deadline', 'closed_at'], 'rfq_management_index');
        });
    }

    /**
     * حذف الجدول عند التراجع عن الترحيل
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
