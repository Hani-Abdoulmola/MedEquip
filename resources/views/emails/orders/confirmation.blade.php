<x-mail::message>
# تأكيد الطلب رقم {{ $order->order_number }}

مرحباً **{{ $buyer->user->name ?? 'عزيزي العميل' }}**،

تم إنشاء طلبك بنجاح! نشكرك على استخدام منصة **{{ config('app.name') }}**.

## 📋 تفاصيل الطلب

<x-mail::panel>
**رقم الطلب:** {{ $order->order_number }}  
**تاريخ الطلب:** {{ $order->order_date->format('Y-m-d H:i') }}  
**الحالة:** {{ $order->status }}  
**المبلغ الإجمالي:** {{ number_format($order->total_amount, 2) }} {{ $order->currency }}
</x-mail::panel>

## 🏢 معلومات المورد

**اسم الشركة:** {{ $supplier->company_name ?? 'غير متوفر' }}  
**الهاتف:** {{ $supplier->phone ?? 'غير متوفر' }}  
**البريد الإلكتروني:** {{ $supplier->user->email ?? 'غير متوفر' }}

@if($quotation && $quotation->rfq)
## 📝 طلب العرض المرتبط

**عنوان RFQ:** {{ $quotation->rfq->title }}  
**رقم المرجع:** {{ $quotation->rfq->reference_code }}
@endif

## 📦 المنتجات المطلوبة

<x-mail::table>
| المنتج | الكمية | السعر | الإجمالي |
|:-------|:------:|:-----:|:--------:|
@foreach($order->items as $item)
| {{ $item->product->name ?? $item->item_name }} | {{ $item->quantity }} {{ $item->unit }} | {{ number_format($item->unit_price, 2) }} | {{ number_format($item->total_price, 2) }} |
@endforeach
| | | **الإجمالي** | **{{ number_format($order->total_amount, 2) }} {{ $order->currency }}** |
</x-mail::table>

<x-mail::button :url="route('buyer.orders.show', $order->id)">
عرض تفاصيل الطلب
</x-mail::button>

## 🔔 الخطوات التالية

1. سيقوم المورد بمراجعة الطلب وإرسال فاتورة
2. ستتلقى إشعاراً عند إصدار الفاتورة
3. يمكنك متابعة حالة الطلب من لوحة التحكم

@if($order->notes)
## 📌 ملاحظات إضافية

{{ $order->notes }}
@endif

---

شكراً لاستخدامك منصة **{{ config('app.name') }}**  
للاستفسارات: {{ config('mail.from.address') }}

مع تحياتنا،  
{{ config('app.name') }}
</x-mail::message>
