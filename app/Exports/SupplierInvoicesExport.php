<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SupplierInvoicesExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
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
        $query = Invoice::whereHas('order', function ($q) {
            $q->where('supplier_id', $this->supplierId);
        })->with(['order.buyer']);

        // Apply filters
        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['payment_status'])) {
            $query->where('payment_status', $this->filters['payment_status']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('invoice_date', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('invoice_date', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('invoice_date', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم الفاتورة',
            'تاريخ الفاتورة',
            'رقم الطلب',
            'المشتري',
            'المبلغ الفرعي',
            'الضريبة',
            'الخصم',
            'المبلغ الإجمالي',
            'حالة الفاتورة',
            'حالة الدفع',
        ];
    }

    public function map($invoice): array
    {
        $statusLabels = [
            'draft' => 'مسودة',
            'issued' => 'صادرة',
            'approved' => 'معتمدة',
            'cancelled' => 'ملغاة',
        ];

        $paymentStatusLabels = [
            'pending' => 'قيد الانتظار',
            'partial' => 'مدفوعة جزئياً',
            'paid' => 'مدفوعة',
            'overdue' => 'متأخرة',
        ];

        return [
            $invoice->invoice_number,
            $invoice->invoice_date?->format('Y-m-d'),
            $invoice->order?->order_number ?? '—',
            $invoice->order?->buyer?->organization_name ?? '—',
            number_format($invoice->subtotal, 2),
            number_format($invoice->tax, 2),
            number_format($invoice->discount, 2),
            number_format($invoice->total_amount, 2),
            $statusLabels[$invoice->status] ?? $invoice->status,
            $paymentStatusLabels[$invoice->payment_status] ?? $invoice->payment_status,
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
        return 'الفواتير';
    }
}

