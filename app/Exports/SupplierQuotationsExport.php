<?php

namespace App\Exports;

use App\Models\Quotation;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierQuotationsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $supplierId;
    private array $filters;

    public function __construct(int $supplierId, array $filters = [])
    {
        $this->supplierId = $supplierId;
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Quotation::where('supplier_id', $this->supplierId)
            ->with(['rfq.buyer']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
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
            'رقم العرض',
            'تاريخ الإنشاء',
            'طلب عرض السعر',
            'المشتري',
            'المبلغ الإجمالي',
            'الحالة',
            'صالح حتى',
            'ملاحظات',
        ];
    }

    public function map($quotation): array
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'accepted' => 'مقبول',
            'rejected' => 'مرفوض',
        ];

        return [
            $quotation->reference_code,
            $quotation->created_at?->format('Y-m-d H:i'),
            $quotation->rfq?->reference_code ?? '—',
            $quotation->rfq?->buyer?->organization_name ?? '—',
            number_format($quotation->total_price, 2),
            $statusLabels[$quotation->status] ?? $quotation->status,
            $quotation->valid_until?->format('Y-m-d') ?? '—',
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

