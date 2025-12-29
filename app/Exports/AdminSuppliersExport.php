<?php

namespace App\Exports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminSuppliersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Supplier::with('user');

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('commercial_register', 'like', "%{$search}%")
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
            'اسم الشركة',
            'السجل التجاري',
            'الرقم الضريبي',
            'البلد',
            'المدينة',
            'البريد الإلكتروني',
            'الهاتف',
            'تاريخ التسجيل',
            'الحالة',
        ];
    }

    public function map($supplier): array
    {
        return [
            $supplier->company_name,
            $supplier->commercial_register,
            $supplier->tax_number ?? '—',
            $supplier->country,
            $supplier->city ?? '—',
            $supplier->user->email ?? '—',
            $supplier->contact_phone ?? '—',
            $supplier->created_at?->format('Y-m-d'),
            $supplier->user->is_active ? 'نشط' : 'غير نشط',
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
        return 'الموردون';
    }
}

