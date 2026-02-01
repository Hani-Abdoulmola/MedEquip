@extends('layouts.pdf-report')

@section('content')
<style>
    .info-section { display: table; width: 100%; margin-bottom: 20px; }
    .info-block { display: table-cell; width: 50%; vertical-align: top; padding: 15px; }
    .info-block h3 { font-size: 14px; color: #4b5563; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
    .info-block p { margin: 5px 0; font-size: 11px; }
    .invoice-meta { margin-bottom: 15px; font-size: 12px; color: #4b5563; }
    .total-row { font-weight: bold; background-color: #e2e2e2; }
</style>

<div class="invoice-meta">
    رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong>
    — التاريخ: {{ $invoice->invoice_date?->format('Y-m-d') ?? 'غير محدد' }}
</div>

<div class="info-section">
    <div class="info-block">
        <h3>المورد</h3>
        <p>{{ $invoice->order?->supplier?->company_name ?? '—' }}</p>
        <p>{{ $invoice->order?->supplier?->contact_email ?? '—' }}</p>
        <p>{{ $invoice->order?->supplier?->contact_phone ?? '—' }}</p>
    </div>
    <div class="info-block">
        <h3>المشتري</h3>
        <p>{{ $invoice->order?->buyer?->organization_name ?? '—' }}</p>
        <p>{{ $invoice->order?->buyer?->contact_email ?? '—' }}</p>
        <p>{{ $invoice->order?->buyer?->contact_phone ?? '—' }}</p>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>السعر</th>
            <th>الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invoice->order?->items ?? [] as $item)
            <tr>
                <td>{{ $item->item_name ?? $item->product?->name ?? '—' }}</td>
                <td>{{ $item->quantity }} {{ $item->unit ?? '' }}</td>
                <td>{{ number_format($item->unit_price, 2) }} د.ل</td>
                <td>{{ number_format($item->total_price, 2) }} د.ل</td>
            </tr>
        @endforeach
        <tr class="total-row">
            <td colspan="3">الإجمالي</td>
            <td>{{ number_format($invoice->total_amount, 2) }} د.ل</td>
        </tr>
    </tbody>
</table>

<p style="margin-top: 20px; font-size: 10px; color: #6b7280;">رقم الطلب: {{ $invoice->order->order_number ?? '—' }}</p>
@endsection
