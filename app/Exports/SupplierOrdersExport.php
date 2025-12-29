<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierOrdersExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = Order::where('supplier_id', $this->supplierId)
            ->with(['buyer', 'quotation']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
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
            'processing' => 'قيد التنفيذ',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
        ];

        return [
            $order->order_number,
            $order->order_date?->format('Y-m-d H:i'),
            $order->buyer?->organization_name ?? '—',
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

