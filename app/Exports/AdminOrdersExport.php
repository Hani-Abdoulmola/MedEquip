<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Order::with(['buyer', 'supplier', 'quotation']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('buyer', fn($b) => $b->where('organization_name', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($s) => $s->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['buyer'])) {
            $query->where('buyer_id', $this->filters['buyer']);
        }

        if (!empty($this->filters['supplier'])) {
            $query->where('supplier_id', $this->filters['supplier']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('order_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('order_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('order_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم الطلب',
            'تاريخ الطلب',
            'المشتري',
            'المورد',
            'الحالة',
            'المبلغ الإجمالي',
            'العملة',
            'رقم عرض السعر',
            'ملاحظات',
        ];
    }

    public function map($order): array
    {
        $statusLabels = [
            'pending' => 'قيد الانتظار',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
        ];

        return [
            $order->order_number,
            $order->order_date?->format('Y-m-d H:i'),
            $order->buyer?->organization_name ?? '—',
            $order->supplier?->company_name ?? '—',
            $statusLabels[$order->status] ?? $order->status,
            number_format($order->total_amount, 2),
            $order->currency,
            $order->quotation?->reference_code ?? '—',
            $order->notes ?? '—',
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
        return 'الطلبات';
    }
}

