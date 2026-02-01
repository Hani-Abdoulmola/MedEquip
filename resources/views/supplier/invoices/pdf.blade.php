@extends('layouts.pdf-report')

@section('content')
<style>
    .info-section { display: table; width: 100%; margin-bottom: 20px; }
    .info-block { display: table-cell; width: 50%; vertical-align: top; padding: 15px; }
    .info-block h3 { font-size: 14px; color: #4b5563; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
    .info-block p { margin: 5px 0; font-size: 11px; }
    .info-block .label { font-weight: bold; color: #6b7280; }
    .invoice-meta { margin-bottom: 15px; font-size: 12px; color: #4b5563; }
    .totals { margin-top: 20px; margin-right: 0; width: 300px; }
    .totals-row { display: table; width: 100%; padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
    .totals-row.total { font-weight: bold; font-size: 14px; border-top: 2px solid #2563eb; border-bottom: 2px solid #2563eb; padding-top: 10px; padding-bottom: 10px; margin-top: 10px; }
    .status-badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 10px; font-weight: bold; margin-left: 8px; }
    .status-issued { background-color: #dbeafe; color: #1e40af; }
    .status-approved { background-color: #d1fae5; color: #065f46; }
    .status-paid { background-color: #d1fae5; color: #065f46; }
    .status-unpaid { background-color: #fee2e2; color: #991b1b; }
    .status-partial { background-color: #fef3c7; color: #92400e; }
    .status-draft, .status-cancelled { background-color: #e5e7eb; color: #4b5563; }
    .notes-box { margin-top: 20px; padding: 15px; background-color: #f9fafb; border-right: 3px solid #2563eb; }
    .sub-table { margin-top: 20px; }
</style>

<div class="invoice-meta">
    رقم الفاتورة: <strong>{{ $invoice->invoice_number }}</strong>
    — تاريخ الإصدار: {{ $invoice->invoice_date?->format('Y-m-d') ?? 'غير محدد' }}
</div>

<div class="info-section">
    <div class="info-block">
        <h3>من:</h3>
        <p><span class="label">الشركة:</span> {{ auth()->user()->supplierProfile?->company_name ?? auth()->user()->name }}</p>
        <p><span class="label">العنوان:</span> {{ auth()->user()->supplierProfile?->address ?? 'غير محدد' }}</p>
        <p><span class="label">البريد:</span> {{ auth()->user()->supplierProfile?->contact_email ?? auth()->user()->email }}</p>
        <p><span class="label">الهاتف:</span> {{ auth()->user()->supplierProfile?->contact_phone ?? 'غير محدد' }}</p>
    </div>
    <div class="info-block">
        <h3>إلى:</h3>
        <p><span class="label">الشركة:</span> {{ $invoice->order->buyer?->organization_name ?? 'غير محدد' }}</p>
        <p><span class="label">العنوان:</span> {{ $invoice->order->buyer?->address ?? 'غير محدد' }}</p>
        <p><span class="label">البريد:</span> {{ $invoice->order->buyer?->contact_email ?? 'غير محدد' }}</p>
        <p><span class="label">الهاتف:</span> {{ $invoice->order->buyer?->contact_phone ?? 'غير محدد' }}</p>
    </div>
</div>

<div style="margin-bottom: 15px;">
    <span class="status-badge status-{{ $invoice->status }}">
        @if($invoice->status === 'issued') صادرة
        @elseif($invoice->status === 'approved') معتمدة
        @elseif($invoice->status === 'draft') مسودة
        @elseif($invoice->status === 'cancelled') ملغية
        @else {{ $invoice->status }}
        @endif
    </span>
    <span class="status-badge status-{{ $invoice->payment_status }}">
        @if($invoice->payment_status === 'paid') مدفوعة
        @elseif($invoice->payment_status === 'partial') جزئية
        @elseif($invoice->payment_status === 'unpaid') غير مدفوعة
        @else {{ $invoice->payment_status }}
        @endif
    </span>
</div>

<table>
    <thead>
        <tr>
            <th>المنتج</th>
            <th>الكمية</th>
            <th>سعر الوحدة</th>
            <th>الإجمالي</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoice->order->items ?? [] as $item)
            <tr>
                <td>{{ $item->product?->name ?? $item->item_name ?? 'منتج' }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ number_format($item->unit_price, 2) }} د.ل</td>
                <td>{{ number_format($item->total_price, 2) }} د.ل</td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px; color: #6b7280;">لا توجد عناصر</td>
            </tr>
        @endforelse
    </tbody>
</table>

<div class="totals">
    <div class="totals-row">
        <span>المجموع الفرعي:</span>
        <span>{{ number_format($invoice->subtotal, 2) }} د.ل</span>
    </div>
    @if($invoice->tax > 0)
        <div class="totals-row">
            <span>الضريبة:</span>
            <span>{{ number_format($invoice->tax, 2) }} د.ل</span>
        </div>
    @endif
    @if($invoice->discount > 0)
        <div class="totals-row">
            <span>الخصم:</span>
            <span>-{{ number_format($invoice->discount, 2) }} د.ل</span>
        </div>
    @endif
    <div class="totals-row total">
        <span>الإجمالي:</span>
        <span>{{ number_format($invoice->total_amount, 2) }} د.ل</span>
    </div>
</div>

@if($invoice->notes)
    <div class="notes-box">
        <h3 style="margin-bottom: 10px; font-size: 12px; color: #374151;">ملاحظات:</h3>
        <p style="font-size: 11px; color: #4b5563;">{{ $invoice->notes }}</p>
    </div>
@endif

@if($invoice->payments->isNotEmpty())
    <div class="sub-table">
        <h3 style="margin-bottom: 15px; font-size: 12px; color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px;">المدفوعات:</h3>
        <table>
            <thead>
                <tr>
                    <th>رقم الدفعة</th>
                    <th>التاريخ</th>
                    <th>المبلغ</th>
                    <th>طريقة الدفع</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                    <tr>
                        <td>{{ $payment->payment_reference ?? '—' }}</td>
                        <td>{{ $payment->paid_at?->format('Y-m-d') ?? 'غير محدد' }}</td>
                        <td>{{ number_format($payment->amount, 2) }} د.ل</td>
                        <td>{{ \App\Models\Payment::getMethodLabel($payment->method ?? 'other') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

<p style="margin-top: 20px; font-size: 10px; color: #6b7280;">رقم الطلب: {{ $invoice->order->order_number ?? '—' }}</p>
@endsection
