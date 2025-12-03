<?php

namespace App\Filters;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogFilter
{
    /**
     * يطبّق جميع الفلاتر على Query واحد
     */
    public static function apply($query, Request $request)
    {
        // نقرأ فقط البارامترات اللي تهمنا
        $params = $request->only([
            'user_id',
            'event',
            'subject_type',
            'log_name',
            'date_from',
            'date_to',
            'date_filter',
        ]);

        /** @var \Illuminate\Database\Eloquent\Builder|ActivityLog $query */

        // log_name (نوع السجل)
        if (! empty($params['log_name'])) {
            $query->forLogName($params['log_name']);
        }

        // المستخدم اللي دار الحدث
        if (! empty($params['user_id'])) {
            $query->forUser($params['user_id']);
        }

        // نوع العملية: created / updated / deleted
        if (! empty($params['event'])) {
            $query->forEvent($params['event']);
        }

        // نوع الموديل المرتبط
        if (! empty($params['subject_type'])) {
            $query->forSubjectType($params['subject_type']);
        }

        // نطاق التاريخ من → إلى
        $query->betweenDates($params['date_from'] ?? null, $params['date_to'] ?? null);

        // اختصارات التاريخ (اليوم - هذا الأسبوع - هذا الشهر)
        $query->quickDate($params['date_filter'] ?? null);

        return $query;
    }
}
