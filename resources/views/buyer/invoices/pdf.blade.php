<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>فاتورة {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; margin: 40px; font-size: 12px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #2563eb; padding-bottom: 20px; }
        .header h1 { margin: 0; color: #2563eb; font-size: 24px; }
        .info-section { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .info-box { width: 45%; }
        .info-box h3 { color: #374151; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #2563eb; color: white; padding: 10px; text-align: right; }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; }
        .total-row { font-weight: bold; background-color: #f3f4f6; }
        .footer { margin-top: 40px; text-align: center; color: #6b7280; font-size: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>فاتورة</h1>
        <p>رقم الفاتورة: {{ $invoice->invoice_number }}</p>
        <p>التاريخ: {{ $invoice->invoice_date?->format('Y/m/d') }}</p>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h3>المورد</h3>
            <p>{{ $invoice->order?->supplier?->company_name }}</p>
            <p>{{ $invoice->order?->supplier?->contact_email }}</p>
            <p>{{ $invoice->order?->supplier?->contact_phone }}</p>
        </div>
        <div class="info-box">
            <h3>المشتري</h3>
            <p>{{ $invoice->order?->buyer?->organization_name }}</p>
            <p>{{ $invoice->order?->buyer?->contact_email }}</p>
            <p>{{ $invoice->order?->buyer?->contact_phone }}</p>
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
                    <td>{{ $item->item_name ?? $item->product?->name }}</td>
                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                    <td>{{ number_format($item->unit_price, 2) }}</td>
                    <td>{{ number_format($item->total_price, 2) }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="3">الإجمالي</td>
                <td>{{ number_format($invoice->total_amount, 2) }} د.ل</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        <p>تم إنشاء هذه الفاتورة إلكترونياً</p>
    </div>
</body>
</html>

