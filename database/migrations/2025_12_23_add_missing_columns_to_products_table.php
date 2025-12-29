<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add manufacturer_id after updated_by
            if (!Schema::hasColumn('products', 'manufacturer_id')) {
                $table->foreignId('manufacturer_id')
                    ->nullable()
                    ->after('updated_by')
                    ->constrained('manufacturers')
                    ->nullOnDelete()
                    ->comment('الشركة المصنّعة الحقيقية للمنتج');
            }

            // Add JSON fields after rejection_reason
            if (!Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')
                    ->nullable()
                    ->after('rejection_reason')
                    ->comment('مواصفات المنتج (key/value)');
            }

            if (!Schema::hasColumn('products', 'features')) {
                $table->json('features')
                    ->nullable()
                    ->after('specifications')
                    ->comment('مميزات المنتج (list of strings)');
            }

            if (!Schema::hasColumn('products', 'technical_data')) {
                $table->json('technical_data')
                    ->nullable()
                    ->after('features')
                    ->comment('بيانات تقنية إضافية');
            }

            if (!Schema::hasColumn('products', 'certifications')) {
                $table->json('certifications')
                    ->nullable()
                    ->after('technical_data')
                    ->comment('الشهادات والاعتمادات');
            }

            if (!Schema::hasColumn('products', 'installation_requirements')) {
                $table->text('installation_requirements')
                    ->nullable()
                    ->after('certifications')
                    ->comment('متطلبات التركيب والتشغيل');
            }
        });

        // Add index if it doesn't exist
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->index(['manufacturer_id', 'category_id'], 'product_manufacturer_category_index');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['manufacturer_id']);
            $table->dropColumn([
                'manufacturer_id',
                'specifications',
                'features',
                'technical_data',
                'certifications',
                'installation_requirements'
            ]);
            $table->dropIndex('product_manufacturer_category_index');
        });
    }
};
