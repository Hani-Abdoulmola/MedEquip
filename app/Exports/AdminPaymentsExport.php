<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AdminPaymentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Payment::with(['invoice', 'order', 'buyer', 'supplier']);

        // Apply filters
        if (!empty($this->filters['search'])) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                  ->orWhereHas('order', fn($sub) => $sub->where('order_number', 'like', "%{$search}%"))
                  ->orWhereHas('buyer', fn($sub) => $sub->where('organization_name', 'like', "%{$search}%"))
                  ->orWhereHas('supplier', fn($sub) => $sub->where('company_name', 'like', "%{$search}%"));
            });
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['method'])) {
            $query->where('payment_method', $this->filters['method']);
        }

        if (!empty($this->filters['from_date'])) {
            $query->whereDate('paid_at', '>=', $this->filters['from_date']);
        }

        if (!empty($this->filters['to_date'])) {
            $query->whereDate('paid_at', '<=', $this->filters['to_date']);
        }

        return $query->orderBy('paid_at', 'desc');
    }

    public function headings(): array
    {
        return [
            'رقم المرجع',
            'تاريخ الدفع',
            'المبلغ',
            'العملة',
            'طريقة الدفع',
            'الحالة',
            'رقم الفاتورة',
            'رقم الطلب',
            'المشتري',
            'المورد',
            'ملاحظات',
        ];
    }

    public function map($payment): array
    {
        $statusLabels = [
            'completed' => 'مكتمل',
            'pending' => 'قيد الانتظار',
            'failed' => 'فاشل',
            'refunded' => 'مسترد',
        ];

        $methodLabels = [
            'cash' => 'نقدي',
            'bank_transfer' => 'تحويل بنكي',
            'check' => 'شيك',
            'credit_card' => 'بطاقة ائتمان',
            'other' => 'أخرى',
        ];

        return [
            $payment->payment_reference,
            $payment->paid_at?->format('Y-m-d H:i'),
            number_format($payment->amount, 2),
            $payment->currency,
            $methodLabels[$payment->payment_method] ?? $payment->payment_method,
            $statusLabels[$payment->status] ?? $payment->status,
            $payment->invoice->invoice_number ?? '—',
            $payment->order->order_number ?? '—',
            $payment->buyer->organization_name ?? '—',
            $payment->supplier->company_name ?? '—',
            $payment->notes ?? '—',
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
        return 'المدفوعات';
    }
}

