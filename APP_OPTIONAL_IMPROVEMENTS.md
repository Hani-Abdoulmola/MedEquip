# APP OPTIONAL IMPROVEMENTS
## Advanced Patterns and Best Practices

**Date:** 2025-11-14  
**Priority:** 🟢 OPTIONAL (Future Enhancements)  

---

## 🎯 IMPROVEMENT #1: Base Controller

### **Create BaseWebController**
**File:** `app/Http/Controllers/BaseWebController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

abstract class BaseWebController extends Controller
{
    /**
     * 🧾 تسجيل نشاط موحد
     */
    protected function logActivity(Model $model, string $message, array $properties = []): void
    {
        activity()
            ->performedOn($model)
            ->causedBy(auth()->user())
            ->withProperties($properties)
            ->log($message);
    }

    /**
     * ✅ إعادة توجيه ناجحة
     */
    protected function successRedirect(string $route, string $message, $params = [])
    {
        return redirect()->route($route, $params)->with('success', $message);
    }

    /**
     * ❌ رجوع مع خطأ
     */
    protected function errorBack(string $message, ?\Throwable $e = null)
    {
        if ($e) {
            Log::error($message . ': ' . $e->getMessage());
        }
        
        return back()->withErrors(['error' => $message])->withInput();
    }

    /**
     * 🔄 تنفيذ عملية داخل Transaction
     */
    protected function executeInTransaction(callable $callback)
    {
        try {
            \DB::beginTransaction();
            $result = $callback();
            \DB::commit();
            
            return $result;
        } catch (\Throwable $e) {
            \DB::rollBack();
            throw $e;
        }
    }

    /**
     * 📄 صفحة موحدة للعرض
     */
    protected function indexView(string $view, $query, string $resourceName, int $perPage = 20)
    {
        $items = $query->latest('id')->paginate($perPage);
        
        return view($view, [$resourceName => $items]);
    }
}
```

### **Usage Example:**
```php
class BuyerController extends BaseWebController
{
    public function index()
    {
        return $this->indexView(
            'buyers.index',
            Buyer::with(['user', 'rfqs', 'orders']),
            'buyers',
            15
        );
    }

    public function store(BuyerRequest $request)
    {
        try {
            $buyer = $this->executeInTransaction(function () use ($request) {
                $buyer = Buyer::create($request->validated());
                
                $this->logActivity($buyer, '✅ تم إنشاء مشتري جديد');
                
                return $buyer;
            });

            return $this->successRedirect('buyers.index', '✅ تم إضافة المشتري بنجاح');
        } catch (\Throwable $e) {
            return $this->errorBack('حدث خطأ أثناء إضافة المشتري', $e);
        }
    }
}
```

---

## 🎯 IMPROVEMENT #2: Enum Classes (PHP 8.1+)

### **OrderStatus Enum**
**File:** `app/Enums/OrderStatus.php`

```php
<?php

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';

    /**
     * 🏷️ التسمية بالعربية
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::PROCESSING => 'قيد المعالجة',
            self::SHIPPED => 'تم الشحن',
            self::DELIVERED => 'تم التسليم',
            self::CANCELLED => 'ملغي',
        };
    }

    /**
     * 🎨 اللون المناسب للحالة
     */
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::PROCESSING => 'info',
            self::SHIPPED => 'primary',
            self::DELIVERED => 'success',
            self::CANCELLED => 'danger',
        };
    }

    /**
     * 📋 جميع القيم
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * 📋 جميع التسميات
     */
    public static function labels(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }
}
```

### **RfqStatus Enum**
**File:** `app/Enums/RfqStatus.php`

```php
<?php

namespace App\Enums;

enum RfqStatus: string
{
    case DRAFT = 'draft';
    case OPEN = 'open';
    case UNDER_REVIEW = 'under_review';
    case CLOSED = 'closed';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'مسودة',
            self::OPEN => 'مفتوح',
            self::UNDER_REVIEW => 'قيد المراجعة',
            self::CLOSED => 'مغلق',
            self::CANCELLED => 'ملغي',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'secondary',
            self::OPEN => 'success',
            self::UNDER_REVIEW => 'warning',
            self::CLOSED => 'info',
            self::CANCELLED => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
```

### **PaymentStatus Enum**
**File:** `app/Enums/PaymentStatus.php`

```php
<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case PENDING = 'pending';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'قيد الانتظار',
            self::COMPLETED => 'مكتمل',
            self::FAILED => 'فشل',
            self::REFUNDED => 'مسترد',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::COMPLETED => 'success',
            self::FAILED => 'danger',
            self::REFUNDED => 'info',
        };
    }
}
```

### **Usage in Models:**
```php
use App\Enums\OrderStatus;

class Order extends Model
{
    protected $casts = [
        'status' => OrderStatus::class,
    ];
}
```

### **Usage in Validation:**
```php
use App\Enums\OrderStatus;

public function rules(): array
{
    return [
        'status' => ['required', Rule::enum(OrderStatus::class)],
    ];
}
```

### **Usage in Blade:**
```blade
<span class="badge bg-{{ $order->status->color() }}">
    {{ $order->status->label() }}
</span>
```

---

## 🎯 IMPROVEMENT #3: Service Classes

### **OrderService**
**File:** `app/Services/OrderService.php`

```php
<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * 📦 إنشاء طلب من عرض سعر
     */
    public function createFromQuotation(Quotation $quotation, array $data): Order
    {
        return DB::transaction(function () use ($quotation, $data) {
            // إنشاء الطلب
            $order = Order::create([
                'quotation_id' => $quotation->id,
                'buyer_id' => $quotation->rfq->buyer_id,
                'supplier_id' => $quotation->supplier_id,
                'order_number' => ReferenceCodeService::generateUnique(
                    ReferenceCodeService::PREFIX_ORDER,
                    Order::class,
                    'order_number'
                ),
                'order_date' => now(),
                'status' => 'pending',
                'total_amount' => $quotation->total_price,
                'currency' => $data['currency'] ?? Order::CURRENCY_LYD,
                'notes' => $data['notes'] ?? null,
            ]);

            // نسخ العناصر من العرض
            foreach ($quotation->items as $quotationItem) {
                $order->items()->create([
                    'product_id' => $quotationItem->product_id,
                    'item_name' => $quotationItem->item_name,
                    'quantity' => $quotationItem->quantity,
                    'unit_price' => $quotationItem->unit_price,
                    'total_price' => $quotationItem->total_price,
                ]);
            }

            // تحديث حالة العرض
            $quotation->update(['status' => 'accepted']);

            return $order;
        });
    }

    /**
     * 🔄 تحديث حالة الطلب
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $order->update(['status' => $newStatus]);

        // إشعارات تلقائية حسب الحالة
        if ($newStatus === 'delivered') {
            NotificationService::send(
                $order->buyer->user,
                '📦 تم تسليم طلبك',
                "تم تسليم طلبك رقم {$order->order_number} بنجاح.",
                route('orders.show', $order->id)
            );
        }

        return $order;
    }
}
```

---

## 🎯 IMPROVEMENT #4: Repository Pattern

### **OrderRepository**
**File:** `app/Repositories/OrderRepository.php`

```php
<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository
{
    /**
     * 🔍 جلب طلب مع جميع العلاقات
     */
    public function findWithRelations(int $id): Order
    {
        return Order::with([
            'quotation.rfq',
            'buyer.user',
            'supplier.user',
            'items.product',
            'invoices',
            'payments',
            'delivery',
        ])->findOrFail($id);
    }

    /**
     * 📋 الطلبات المعلقة
     */
    public function getPendingOrders(): Collection
    {
        return Order::where('status', 'pending')
            ->with(['buyer', 'supplier'])
            ->latest()
            ->get();
    }

    /**
     * 📊 إحصائيات الطلبات
     */
    public function getStatistics(): array
    {
        return [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
            'total_value' => Order::sum('total_amount'),
        ];
    }
}
```

---

## 📋 SUMMARY

**Optional Improvements:**
1. ✅ Base Controller - Reduce duplication
2. ✅ Enum Classes - Type safety
3. ✅ Service Classes - Business logic
4. ✅ Repository Pattern - Data access

**Benefits:**
- 📉 Less code duplication
- 🔒 Better type safety
- 🧪 Easier testing
- 📖 Better maintainability

**Estimated Time:** 7-10 hours total

**Recommendation:** Implement gradually over 2-3 sprints.

