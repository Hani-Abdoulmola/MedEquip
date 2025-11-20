<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ActivityLogFilter
{
    public static function apply($query, Request $request)
    {
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->input('user_id'));
        }

        if ($request->filled('event')) {
            $query->where('event', $request->input('event'));
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', 'like', "%{$request->input('subject_type')}%");
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        return $query;
    }
}
