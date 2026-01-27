<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminUsersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        // تصدير فقط المستخدمين الإداريين (Admin/Staff)
        // Support both Admin and Staff user types
        $adminType = \App\Models\UserType::where('slug', 'admin')->first();
        $staffType = \App\Models\UserType::where('slug', 'staff')->first();
        
        $userTypeIds = [];
        if ($adminType) {
            $userTypeIds[] = $adminType->id;
        }
        if ($staffType) {
            $userTypeIds[] = $staffType->id;
        }
        
        // Fallback: if types don't exist, use Admin type only (backward compatibility)
        if (empty($userTypeIds)) {
            $userTypeIds = [1];
        }
        
        $query = User::with('roles')
            ->whereIn('user_type_id', $userTypeIds) // Admin and/or Staff user types
            ->whereHas('roles', function ($q) {
                $q->whereIn('name', ['Admin', 'Staff']);
            });

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (!empty($this->filters['role'])) {
            $query->role($this->filters['role']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'الاسم',
            'البريد الإلكتروني',
            'الهاتف',
            'الأدوار',
            'تاريخ التسجيل',
            'آخر تسجيل دخول',
            'الحالة',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->email,
            $user->phone ?? '—',
            $user->roles->pluck('name')->join(', ') ?: '—',
            $user->created_at?->format('Y-m-d H:i'),
            $user->last_login_at?->format('Y-m-d H:i') ?? '—',
            $user->is_active ? 'نشط' : 'غير نشط',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'المستخدمون';
    }
}

