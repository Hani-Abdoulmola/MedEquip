<?php

namespace App\Services;

use Illuminate\Support\Str;

class ReferenceCodeService
{
    /**
     * 🔢 توليد رمز مرجعي موحد لجميع الكيانات
     * 
     * @param string $prefix البادئة (RFQ, QT, ORD, INV, DLV, PAY)
     * @param int $length طول الجزء العشوائي (افتراضي: 6)
     * @return string
     */
    public static function generate(string $prefix, int $length = 6): string
    {
        $date = now()->format('Ymd');
        $random = strtoupper(Str::random($length));
        
        return "{$prefix}-{$date}-{$random}";
    }

    /**
     * 🔍 توليد رمز فريد مع التحقق من عدم التكرار
     * 
     * @param string $prefix البادئة
     * @param string $model اسم الموديل الكامل (مثل: \App\Models\Rfq::class)
     * @param string $column اسم العمود (افتراضي: reference_code)
     * @param int $length طول الجزء العشوائي
     * @return string
     */
    public static function generateUnique(
        string $prefix,
        string $model,
        string $column = 'reference_code',
        int $length = 6
    ): string {
        do {
            $code = self::generate($prefix, $length);
        } while ($model::where($column, $code)->exists());

        return $code;
    }

    /**
     * 📋 الثوابت للبادئات
     */
    public const PREFIX_RFQ = 'RFQ';
    public const PREFIX_QUOTATION = 'QT';
    public const PREFIX_ORDER = 'ORD';
    public const PREFIX_INVOICE = 'INV';
    public const PREFIX_DELIVERY = 'DLV';
    public const PREFIX_PAYMENT = 'PAY';
}

