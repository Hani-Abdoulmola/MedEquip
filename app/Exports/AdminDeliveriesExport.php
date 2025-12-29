<?php

namespace App\Exports;

use App\Models\Delivery;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminDeliveriesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Delivery::with(['order', 'supplier', 'buyer']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('delivery_number', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('buyer', fn($sub) => $sub->where('organization_name', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($sub) => $sub->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['supplier'])) {
            $query->where('supplier_id', $this->filters['supplier']);
        }

        if (!empty($this->filters['buyer'])) {
            $query->where('buyer_id', $this->filters['buyer']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('delivery_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('delivery_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('delivery_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم التسليم',
            'تاريخ التسليم',
            'رقم الطلب',
            'المشتري',
            'المورد',
            'الحالة',
            'موقع التسليم',
            'رقم الهاتف',
            'اسم المستلم',
            'ملاحظات',
        ];
    }

    public function map($delivery): array
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'in_transit' => 'قيد النقل',
            'delivered' => 'تم التسليم',
            'failed' => 'فاشل',
        ];

        return [
            $delivery->delivery_number,
            $delivery->delivery_date?->format('Y-m-d H:i'),
            $delivery->order->order_number ?? '—',
            $delivery->buyer->organization_name ?? '—',
            $delivery->supplier->company_name ?? '—',
            $statusLabels[$delivery->status] ?? $delivery->status,
            $delivery->delivery_location ?? '—',
            $delivery->receiver_phone ?? '—',
            $delivery->receiver_name ?? '—',
            $delivery->notes ?? '—',
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
        return 'التسليمات';
    }
}

