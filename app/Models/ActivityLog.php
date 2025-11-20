<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Models\Activity as SpatieActivity;

class ActivityLog extends SpatieActivity
{
    use HasFactory, SoftDeletes;

    protected $table = 'activity_log';

    protected $fillable = [
        'log_name',
        'description',
        'event',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'batch_uuid',
        'module',
        'action',
        'ip_address',
        'user_agent',
        'platform',
    ];

    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    // ======================
    // 🧠 Accessors
    // ======================
    public function getSummaryAttribute(): string
    {
        return sprintf(
            '[%s] %s - %s (%s)',
            $this->created_at?->format('Y-m-d H:i'),
            ucfirst($this->event),
            $this->description,
            $this->module ?? 'General'
        );
    }

    // ======================
    // ⚙️ إعدادات Spatie LogsActivity
    // ======================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->useLogName('activity_log')
            ->setDescriptionForEvent(fn (string $eventName) => "🧾 تم {$eventName} على سجل النشاط");
    }
}
