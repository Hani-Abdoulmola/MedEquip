@extends('layouts.pdf-report')

@section('content')
<table>
    <thead>
        <tr>
            <th>#</th>
            <th>رقم المرجع</th>
            <th>تاريخ الدفع</th>
            <th>المبلغ</th>
            <th>العملة</th>
            <th>طريقة الدفع</th>
            <th>الحالة</th>
            <th>رقم الفاتورة</th>
            <th>رقم الطلب</th>
            <th>المشتري</th>
            <th>المورد</th>
            <th>ملاحظات</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payments as $payment)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $payment->payment_reference ?? '—' }}</td>
                <td>{{ $payment->paid_at?->format('Y-m-d H:i') ?? $payment->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
                <td>{{ number_format($payment->amount ?? 0, 2) }}</td>
                <td>{{ $payment->currency ?? 'LYD' }}</td>
                <td>{{ \App\Models\Payment::getMethodLabel($payment->method ?? 'other') }}</td>
                <td>{{ \App\Models\Payment::getStatusLabel($payment->status ?? 'pending') }}</td>
                <td>{{ $payment->invoice?->invoice_number ?? '—' }}</td>
                <td>{{ $payment->order?->order_number ?? '—' }}</td>
                <td>{{ $payment->buyer?->organization_name ?? '—' }}</td>
                <td>{{ $payment->supplier?->company_name ?? '—' }}</td>
                <td>{{ str()->limit($payment->notes ?? '—', 30) }}</td>
            </tr>
        @endforeach
        <tr class="summary">
            <td colspan="9">إجمالي عدد السجلات: <strong>{{ $payments->count() }}</strong></td>
            <td colspan="3">إجمالي المبالغ (مكتملة): <strong>{{ number_format($payments->where('status', \App\Models\Payment::STATUS_COMPLETED)->sum('amount'), 2) }} د.ل</strong></td>
        </tr>
    </tbody>
</table>
@endsection
