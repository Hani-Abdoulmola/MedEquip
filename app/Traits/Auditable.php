<?php

namespace App\Traits;

use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

trait Auditable
{
    use LogsActivity;

    /**
     * إعدادات التتبع الافتراضية لجميع الموديلات
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // تتبع جميع الحقول تلقائيًا
            ->useLogName(class_basename($this)) // اسم السجل = اسم الموديل
            ->setDescriptionForEvent(function (string $eventName) {
                return "🧾 تم {$eventName} على سجل ".class_basename($this);
            });
    }
}
