<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'platform_name', 'value' => 'MediEquip', 'group' => 'general', 'type' => 'text', 'description' => 'اسم المنصة'],
            ['key' => 'support_email', 'value' => 'support@mediequip.ly', 'group' => 'general', 'type' => 'text', 'description' => 'البريد الإلكتروني للدعم'],
            ['key' => 'support_phone', 'value' => '+218 21 123 4567', 'group' => 'general', 'type' => 'text', 'description' => 'رقم الهاتف'],
            ['key' => 'default_currency', 'value' => 'LYD', 'group' => 'general', 'type' => 'select', 'description' => 'العملة الافتراضية'],
            ['key' => 'default_language', 'value' => 'ar', 'group' => 'general', 'type' => 'select', 'description' => 'اللغة الافتراضية'],
            ['key' => 'timezone', 'value' => 'Africa/Tripoli', 'group' => 'general', 'type' => 'select', 'description' => 'المنطقة الزمنية'],
            ['key' => 'platform_description', 'value' => 'منصة MediEquip هي منصة B2B لتوريد المعدات الطبية في ليبيا، تربط الموردين بالمشترين من المستشفيات والعيادات والصيدليات.', 'group' => 'general', 'type' => 'textarea', 'description' => 'وصف المنصة'],
            ['key' => 'maintenance_mode', 'value' => '0', 'group' => 'general', 'type' => 'boolean', 'description' => 'وضع الصيانة'],
            ['key' => 'user_registration_enabled', 'value' => '1', 'group' => 'general', 'type' => 'boolean', 'description' => 'السماح بتسجيل المستخدمين'],

            // Email Settings
            ['key' => 'smtp_host', 'value' => 'smtp.gmail.com', 'group' => 'email', 'type' => 'text', 'description' => 'خادم SMTP'],
            ['key' => 'smtp_port', 'value' => '587', 'group' => 'email', 'type' => 'number', 'description' => 'منفذ SMTP'],
            ['key' => 'smtp_username', 'value' => 'noreply@mediequip.ly', 'group' => 'email', 'type' => 'text', 'description' => 'اسم المستخدم SMTP'],
            ['key' => 'smtp_password', 'value' => '', 'group' => 'email', 'type' => 'password', 'description' => 'كلمة مرور SMTP'],
            ['key' => 'email_notifications_enabled', 'value' => '1', 'group' => 'email', 'type' => 'boolean', 'description' => 'تفعيل الإشعارات'],

            // Payment Settings
            ['key' => 'commission_percentage', 'value' => '5', 'group' => 'payment', 'type' => 'number', 'description' => 'نسبة العمولة'],
            ['key' => 'minimum_order_amount', 'value' => '100', 'group' => 'payment', 'type' => 'number', 'description' => 'الحد الأدنى للطلب'],

            // Security Settings
            ['key' => 'two_factor_enabled', 'value' => '0', 'group' => 'security', 'type' => 'boolean', 'description' => 'تفعيل المصادقة الثنائية'],
            ['key' => 'ssl_encryption_enabled', 'value' => '1', 'group' => 'security', 'type' => 'boolean', 'description' => 'تفعيل التشفير SSL'],
            ['key' => 'log_failed_attempts', 'value' => '1', 'group' => 'security', 'type' => 'boolean', 'description' => 'تسجيل المحاولات الفاشلة'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
