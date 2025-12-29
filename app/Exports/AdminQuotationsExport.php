<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminQuotationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Quotation::with(['rfq', 'supplier']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reference_code', 'like', "%{$search}%")
                  ->orWhereHas('rfq', fn($r) => $r->where('title', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($s) => $s->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['supplier'])) {
            $query->where('supplier_id', $this->filters['supplier']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('created_at', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('created_at', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم عرض السعر',
            'تاريخ التقديم',
            'طلب الاستعلام',
            'المورد',
            'الحالة',
            'المبلغ الإجمالي',
            'العملة',
            'ملاحظات',
        ];
    }

    public function map($quotation): array
    {
        $statusLabels = [
            'draft' => 'مسودة',
            'submitted' => 'مقدم',
            'under_review' => 'قيد المراجعة',
            'approved' => 'معتمد',
            'rejected' => 'مرفوض',
        ];

        return [
            $quotation->reference_code,
            $quotation->created_at?->format('Y-m-d H:i'),
            $quotation->rfq?->title ?? '—',
            $quotation->supplier?->company_name ?? '—',
            $statusLabels[$quotation->status] ?? $quotation->status,
            number_format($quotation->total_price, 2),
            'LYD', // Default currency
            $quotation->terms ?? '—',
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
        return 'عروض الأسعار';
    }
}

