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
        'properties' => 'collection',
        'created_at' => 'datetime:Y-m-d H:i',
        'updated_at' => 'datetime:Y-m-d H:i',
    ];

    protected $appends = [
        'summary',
        'log_name_label',
        'event_label',
        'event_icon',
        'log_color',
    ];

    // -----------------------------------------------------
    //  Accessors (عربي واضح لواجهة الأدمن)
    // -----------------------------------------------------

    public function getSummaryAttribute(): string
    {
        return sprintf(
            '📌 [%s] — %s | %s (%s)',
            $this->created_at?->format('Y-m-d H:i'),
            $this->event_label,
            $this->description ?: 'بدون وصف',
            $this->log_name_label
        );
    }

    public function getLogNameLabelAttribute(): string
    {
        return match ($this->log_name) {
            'suppliers' => 'الموردين',
            'buyers'    => 'المشترين',
            'products'  => 'المنتجات',
            'orders'    => 'الطلبات',
            'system'    => 'النظام',
            'default', null, '' => 'عام',
            default     => ucfirst($this->log_name),
        };
    }

    public function getLogColorAttribute(): string
    {
        return match ($this->log_name) {
            'suppliers' => 'blue',
            'buyers'    => 'green',
            'products'  => 'purple',
            'orders'    => 'yellow',
            'system'    => 'red',
            default     => 'gray',
        };
    }

    public function getEventLabelAttribute(): string
    {
        return match ($this->event) {
            'created' => 'تم الإنشاء',
            'updated' => 'تم التعديل',
            'deleted' => 'تم الحذف',
            default   => 'عملية',
        };
    }

    public function getEventIconAttribute(): string
    {
        return match ($this->event) {
            'created' => '✨',
            'updated' => '✏️',
            'deleted' => '🗑️',
            default   => '🧾',
        };
    }

    // -----------------------------------------------------
    //  إعدادات Spatie LogsActivity (لو استخدمته مع هذا الموديل نفسه)
    // -----------------------------------------------------
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName('system')
            ->setDescriptionForEvent(function (string $event) {
                return match ($event) {
                    'created' => '✨ تم إنشاء سجل جديد',
                    'updated' => '✏️ تم تعديل السجل',
                    'deleted' => '🗑️ تم حذف السجل',
                    default   => "🧾 حدث: {$event}",
                };
            });
    }

    // -----------------------------------------------------
    //  Scopes للفلاتر (أفضل ممارسة)
    // -----------------------------------------------------

    public function scopeForUser($query, $userId)
    {
        return $query->where('causer_id', $userId);
    }

    public function scopeForLogName($query, string $logName)
    {
        return $query->where('log_name', $logName);
    }

    public function scopeForSubjectType($query, string $subjectType)
    {
        return $query->where('subject_type', 'LIKE', "%{$subjectType}%");
    }

    public function scopeBetweenDates($query, ?string $from, ?string $to)
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeQuickDate($query, ?string $filter)
    {
        if (! $filter) {
            return $query;
        }

        $now = now();

        return match ($filter) {
            'today' => $query->where('created_at', '>=', $now->startOfDay()),
            'week'  => $query->where('created_at', '>=', $now->startOfWeek()),
            'month' => $query->where('created_at', '>=', $now->startOfMonth()),
            default => $query,
        };
    }

    public function scopeSearch($query, ?string $term)
    {
        if (! $term) {
            return $query;
        }

        $term = trim($term);

        return $query->where(function ($q) use ($term) {
            $q->where('description', 'like', "%{$term}%")
                ->orWhere('log_name', 'like', "%{$term}%")
                ->orWhere('event', 'like', "%{$term}%");
        });
    }

    // -----------------------------------------------------
    //  Boot: إضافة معلومات الجهاز والـ IP تلقائياً
    // -----------------------------------------------------
    protected static function booted()
    {
        static::creating(function ($model) {
            if (request()) {
                $model->ip_address = request()->ip();
                $model->user_agent = request()->userAgent();
                $model->platform   = request()->header('sec-ch-ua-platform')
                    ?: php_uname('s');
            }
        });
    }
}
