<?php

namespace App\Exports;

use App\Models\Buyer;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminBuyersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Buyer::with('user');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('organization_name', 'like', "%{$search}%")
                  ->orWhere('registration_number', 'like', "%{$search}%")
                  ->orWhereHas('user', fn($u) => $u->where('email', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['country'])) {
            $query->where('country', $this->filters['country']);
        }

        if (!empty($this->filters['status'])) {
            if ($this->filters['status'] === 'active') {
                $query->whereHas('user', fn($u) => $u->where('is_active', true));
            } elseif ($this->filters['status'] === 'inactive') {
                $query->whereHas('user', fn($u) => $u->where('is_active', false));
            }
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'اسم المؤسسة',
            'رقم التسجيل',
            'البلد',
            'المدينة',
            'البريد الإلكتروني',
            'الهاتف',
            'الشخص المسؤول',
            'تاريخ التسجيل',
            'الحالة',
        ];
    }

    public function map($buyer): array
    {
        return [
            $buyer->organization_name,
            $buyer->registration_number ?? '—',
            $buyer->country,
            $buyer->city ?? '—',
            $buyer->user->email ?? '—',
            $buyer->contact_phone ?? '—',
            $buyer->contact_person ?? '—',
            $buyer->created_at?->format('Y-m-d'),
            $buyer->user->is_active ? 'نشط' : 'غير نشط',
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
        return 'المشترون';
    }
}

