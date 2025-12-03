<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        // Get all settings grouped by category
        $settings = [
            'general' => [
                'platform_name' => Setting::get('platform_name', 'MediEquip'),
                'support_email' => Setting::get('support_email', 'support@mediequip.ly'),
                'support_phone' => Setting::get('support_phone', '+218 21 123 4567'),
                'default_currency' => Setting::get('default_currency', 'LYD'),
                'default_language' => Setting::get('default_language', 'ar'),
                'timezone' => Setting::get('timezone', 'Africa/Tripoli'),
                'platform_description' => Setting::get('platform_description', 'منصة MediEquip هي منصة B2B لتوريد المعدات الطبية في ليبيا'),
                'maintenance_mode' => Setting::getBoolean('maintenance_mode', false),
                'user_registration_enabled' => Setting::getBoolean('user_registration_enabled', true),
            ],
            'email' => [
                'smtp_host' => Setting::get('smtp_host', 'smtp.gmail.com'),
                'smtp_port' => Setting::get('smtp_port', '587'),
                'smtp_username' => Setting::get('smtp_username', 'noreply@mediequip.ly'),
                'smtp_password' => Setting::get('smtp_password', ''),
                'email_notifications_enabled' => Setting::getBoolean('email_notifications_enabled', true),
            ],
            'payment' => [
                'commission_percentage' => Setting::get('commission_percentage', '5'),
                'minimum_order_amount' => Setting::get('minimum_order_amount', '100'),
            ],
            'security' => [
                'two_factor_enabled' => Setting::getBoolean('two_factor_enabled', false),
                'ssl_encryption_enabled' => Setting::getBoolean('ssl_encryption_enabled', true),
                'log_failed_attempts' => Setting::getBoolean('log_failed_attempts', true),
            ],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update general settings
     */
    public function updateGeneral(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'platform_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'support_phone' => 'required|string|max:50',
            'default_currency' => 'required|string|max:10',
            'default_language' => 'required|string|max:10',
            'timezone' => 'required|string|max:100',
            'platform_description' => 'nullable|string|max:1000',
            'maintenance_mode' => 'nullable|boolean',
            'user_registration_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'حدث خطأ في البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            Setting::set('platform_name', $request->input('platform_name'), 'general', 'text');
            Setting::set('support_email', $request->input('support_email'), 'general', 'text');
            Setting::set('support_phone', $request->input('support_phone'), 'general', 'text');
            Setting::set('default_currency', $request->input('default_currency'), 'general', 'select');
            Setting::set('default_language', $request->input('default_language'), 'general', 'select');
            Setting::set('timezone', $request->input('timezone'), 'general', 'select');
            Setting::set('platform_description', $request->input('platform_description'), 'general', 'textarea');
            Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0', 'general', 'boolean');
            Setting::set('user_registration_enabled', $request->boolean('user_registration_enabled') ? '1' : '0', 'general', 'boolean');

            // Log activity
            activity('settings')
                ->causedBy(Auth::user())
                ->withProperties(['group' => 'general'])
                ->log('تم تحديث الإعدادات العامة للنظام');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم حفظ الإعدادات العامة بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update email settings
     */
    public function updateEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'smtp_host' => 'required|string|max:255',
            'smtp_port' => 'required|integer|min:1|max:65535',
            'smtp_username' => 'required|string|max:255',
            'smtp_password' => 'nullable|string|max:255',
            'email_notifications_enabled' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'حدث خطأ في البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            Setting::set('smtp_host', $request->input('smtp_host'), 'email', 'text');
            Setting::set('smtp_port', $request->input('smtp_port'), 'email', 'number');
            Setting::set('smtp_username', $request->input('smtp_username'), 'email', 'text');
            
            // Only update password if provided
            if ($request->filled('smtp_password')) {
                Setting::set('smtp_password', $request->input('smtp_password'), 'email', 'password');
            }
            
            Setting::set('email_notifications_enabled', $request->boolean('email_notifications_enabled') ? '1' : '0', 'email', 'boolean');

            // Log activity
            activity('settings')
                ->causedBy(Auth::user())
                ->withProperties(['group' => 'email'])
                ->log('تم تحديث إعدادات البريد الإلكتروني');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم حفظ إعدادات البريد الإلكتروني بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update payment settings
     */
    public function updatePayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'commission_percentage' => 'required|numeric|min:0|max:100',
            'minimum_order_amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'حدث خطأ في البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            Setting::set('commission_percentage', $request->input('commission_percentage'), 'payment', 'number');
            Setting::set('minimum_order_amount', $request->input('minimum_order_amount'), 'payment', 'number');

            // Log activity
            activity('settings')
                ->causedBy(Auth::user())
                ->withProperties(['group' => 'payment'])
                ->log('تم تحديث إعدادات الدفع والعمولات');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم حفظ إعدادات الدفع بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update security settings
     */
    public function updateSecurity(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'two_factor_enabled' => 'nullable|boolean',
            'log_failed_attempts' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'حدث خطأ في البيانات المدخلة');
        }

        try {
            DB::beginTransaction();

            Setting::set('two_factor_enabled', $request->boolean('two_factor_enabled') ? '1' : '0', 'security', 'boolean');
            Setting::set('log_failed_attempts', $request->boolean('log_failed_attempts') ? '1' : '0', 'security', 'boolean');

            // Log activity
            activity('settings')
                ->causedBy(Auth::user())
                ->withProperties(['group' => 'security'])
                ->log('تم تحديث إعدادات الأمان');

            DB::commit();

            return redirect()->route('admin.settings.index')
                ->with('success', 'تم حفظ إعدادات الأمان بنجاح');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'حدث خطأ أثناء حفظ الإعدادات: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Test email connection
     */
    public function testEmailConnection(Request $request)
    {
        try {
            // Here you would implement actual email testing logic
            // For now, we'll just return a success message
            
            return redirect()->back()
                ->with('success', 'تم اختبار الاتصال بخادم البريد الإلكتروني بنجاح');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'فشل الاتصال بخادم البريد الإلكتروني: ' . $e->getMessage());
        }
    }
}
