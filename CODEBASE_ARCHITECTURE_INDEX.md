# MedEquip Codebase Architecture Index

**Comprehensive File-by-File Index and Architecture Documentation**

Generated: January 27, 2026

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Core Architecture](#core-architecture)
3. [Models & Data Structure](#models--data-structure)
4. [Controllers & Request Flow](#controllers--request-flow)
5. [Services & Business Logic](#services--business-logic)
6. [Policies & Authorization](#policies--authorization)
7. [Database Schema](#database-schema)
8. [Workflows & State Machines](#workflows--state-machines)
9. [Dependencies & Relationships](#dependencies--relationships)
10. [File Index](#file-index)

---

## System Overview

**MedEquip** is a B2B medical equipment marketplace platform built with Laravel. It facilitates the procurement process between buyers (hospitals, clinics, pharmacies) and suppliers through an RFQ (Request for Quotation) workflow.

### Key Features
- **User Management**: Admin, Staff, Supplier, Buyer roles with RBAC
- **Product Catalog**: Medical equipment with categories, manufacturers, supplier offers
- **RFQ System**: Buyers create RFQs, suppliers submit quotations
- **Order Management**: Orders created from accepted quotations
- **Invoice & Payment Tracking**: Financial workflow management
- **Delivery Management**: Order fulfillment and tracking

### Technology Stack
- **Framework**: Laravel 10+
- **Database**: MySQL
- **Authorization**: Spatie Permission Package
- **Media Management**: Spatie Media Library
- **Activity Logging**: Spatie Activity Log

---

## Core Architecture

### Directory Structure

```
app/
├── Console/Commands/          # Artisan commands
├── Exports/                   # Excel export classes
├── Http/
│   ├── Controllers/
│   │   ├── Web/              # Web controllers
│   │   │   ├── Buyers/       # Buyer-specific controllers
│   │   │   ├── Suppliers/   # Supplier-specific controllers
│   │   │   └── ...           # Admin/General controllers
│   │   └── Api/              # API controllers
│   └── Requests/             # Form request validation
├── Models/                    # Eloquent models
├── Policies/                  # Authorization policies
├── Providers/                 # Service providers
├── Services/                  # Business logic services
├── Traits/                    # Reusable traits
└── Observers/                 # Model observers

database/
├── migrations/               # Database schema migrations
├── seeders/                  # Data seeders
└── factories/                # Model factories
```

### Key Architectural Patterns

1. **MVC Pattern**: Controllers → Services → Models
2. **Service Layer**: Business logic abstracted into services
3. **Policy-Based Authorization**: Laravel policies with Spatie permissions
4. **State Machines**: Workflow management for RFQs and Quotations
5. **Repository Pattern**: Implicit through service classes
6. **Observer Pattern**: Model observers for automatic actions

---

## Models & Data Structure

### Core Models

#### User Model (`app/Models/User.php`)
- **Purpose**: Central authentication and user management
- **Relationships**:
  - `belongsTo(UserType)` - User type classification
  - `hasOne(Supplier)` - Supplier profile (if supplier)
  - `hasOne(Buyer)` - Buyer profile (if buyer)
  - `hasMany(Payment)` - Payments processed
- **Traits**: `HasRoles` (Spatie), `InteractsWithMedia`, `Auditable`
- **Key Features**:
  - Auto-hashing passwords (prevents double hashing)
  - Media collections for profile photos and documents
  - Soft deletes

#### Buyer Model (`app/Models/Buyer.php`)
- **Purpose**: Buyer organization profiles
- **Relationships**:
  - `belongsTo(User)` - User account
  - `hasMany(Rfq)` - RFQs created
  - `hasMany(Order)` - Orders placed
  - `hasMany(BuyerCart)` - Shopping carts/RFQ builders
  - `belongsToMany(Product)` - Favorite products
- **Key Features**:
  - Organization types (hospital, clinic, pharmacy, etc.)
  - Verification workflow (pending → verified → rejected)
  - Statistics methods (total RFQs, orders, spending)

#### Supplier Model (`app/Models/Supplier.php`)
- **Purpose**: Supplier company profiles
- **Relationships**:
  - `belongsTo(User)` - User account
  - `belongsToMany(Product)` - Products offered (via `product_supplier` pivot)
  - `hasMany(Quotation)` - Quotations submitted
  - `belongsToMany(Rfq)` - Assigned RFQs (via `rfq_supplier` pivot)
  - `hasMany(Order)` - Orders received
  - `hasMany(SupplierReview)` - Reviews from buyers
- **Key Features**:
  - Verification workflow
  - Average rating calculation
  - Performance metrics

#### Product Model (`app/Models/Product.php`)
- **Purpose**: Medical equipment catalog
- **Relationships**:
  - `belongsTo(ProductCategory)` - Category classification
  - `belongsTo(Manufacturer)` - Manufacturer
  - `belongsToMany(Supplier)` - Suppliers offering product (via `product_supplier`)
  - `hasMany(RfqItem)` - RFQ items referencing product
- **Key Features**:
  - Review workflow (pending → approved → needs_update → rejected)
  - Medical compliance fields (CE, FDA, ISO)
  - Availability status calculation
  - Media collections for images and documents
  - Denormalized `min_price` and `suppliers_count` (via observer)

#### Rfq Model (`app/Models/Rfq.php`)
- **Purpose**: Request for Quotation
- **Relationships**:
  - `belongsTo(Buyer)` - Buyer who created RFQ
  - `hasMany(RfqItem)` - Items in RFQ
  - `hasMany(Quotation)` - Quotations submitted
  - `belongsToMany(Supplier)` - Assigned suppliers (via `rfq_supplier`)
  - `belongsTo(Quotation)` - Awarded quotation
- **Key Features**:
  - Status workflow (draft → open → closed/awarded/cancelled)
  - Public/private visibility
  - Deadline management
  - Reference code generation

#### Quotation Model (`app/Models/Quotation.php`)
- **Purpose**: Supplier's price quote for an RFQ
- **Relationships**:
  - `belongsTo(Rfq)` - RFQ being quoted
  - `belongsTo(Supplier)` - Supplier submitting quote
  - `hasMany(QuotationItem)` - Line items
  - `hasMany(Order)` - Orders created from accepted quotation
- **Key Features**:
  - Status workflow (draft → pending → accepted/rejected/expired)
  - Scoring system for comparison (price, lead time, supplier, stock, validity)
  - Unique constraint: one accepted quotation per RFQ
  - Auto-expiration on deadline

#### Order Model (`app/Models/Order.php`)
- **Purpose**: Purchase order created from accepted quotation
- **Relationships**:
  - `belongsTo(Quotation)` - Source quotation
  - `belongsTo(Buyer)` - Buyer placing order
  - `belongsTo(Supplier)` - Supplier fulfilling order
  - `hasMany(OrderItem)` - Line items
  - `hasMany(Invoice)` - Invoices
  - `hasMany(Payment)` - Payments
  - `hasMany(Delivery)` - Deliveries
- **Key Features**:
  - Status workflow (pending → processing → shipped → delivered → cancelled)
  - Multi-currency support (LYD, USD, EUR)

#### Invoice Model (`app/Models/Invoice.php`)
- **Purpose**: Financial invoices for orders
- **Relationships**:
  - `belongsTo(Order)` - Source order
  - `hasMany(Payment)` - Payments against invoice
- **Key Features**:
  - Status workflow (draft → issued → approved → cancelled)
  - Payment status tracking (unpaid → partial → paid)

#### Delivery Model (`app/Models/Delivery.php`)
- **Purpose**: Order fulfillment tracking
- **Relationships**:
  - `belongsTo(Order)` - Source order
  - `belongsTo(Supplier)` - Supplier delivering
  - `belongsTo(Buyer)` - Buyer receiving
- **Key Features**:
  - Status workflow (pending → in_transit → delivered → failed)
  - Delivery proof uploads
  - Verification workflow

### Supporting Models

- **BuyerCart** / **BuyerCartItem**: RFQ builder (cart functionality)
- **RfqItem**: Individual items in an RFQ
- **QuotationItem**: Individual items in a quotation
- **OrderItem**: Individual items in an order
- **ProductCategory**: Hierarchical product categories
- **Manufacturer**: Product manufacturers
- **ProductSupplier**: Pivot table with price, stock, lead time
- **Role** / **Permission**: Spatie RBAC models
- **UserType**: User type classification
- **ActivityLog**: Spatie activity logging
- **Notification**: System notifications

---

## Controllers & Request Flow

### Controller Hierarchy

```
Controller (base)
└── BaseController (Web base)
    ├── Admin Controllers
    ├── Buyer Controllers
    └── Supplier Controllers
```

### Request Flow Pattern

```
Route → Middleware → Controller → Policy Check → Service → Model → Response
```

### Key Controllers

#### Buyer Controllers (`app/Http/Controllers/Web/Buyers/`)

**BuyerRfqController**
- **Routes**: `/buyer/rfqs/*`
- **Methods**:
  - `index()` - List buyer's RFQs
  - `create()` - Show RFQ creation form
  - `store()` - Create new RFQ
  - `show()` - View RFQ details with quotations
  - `edit()` / `update()` - Edit RFQ
  - `destroy()` - Delete RFQ
  - `updateStatus()` - Change RFQ status
  - `duplicate()` - Clone RFQ
  - `importCsv()` - Import RFQ from CSV
  - `estimateBudget()` - Calculate estimated budget
  - `suggestSuppliers()` - AI supplier suggestions
- **Dependencies**: `RfqRequest`, `RfqWorkflowService`, `ReferenceCodeService`, `RfqImportService`

**BuyerCartController**
- **Routes**: `/buyer/cart/*`
- **Purpose**: RFQ Builder (cart functionality)
- **Methods**:
  - `index()` - Show cart/builder
  - `add()` - Add product to cart
  - `update()` - Update cart item
  - `remove()` - Remove cart item
  - `checkout()` - Review before submitting RFQ
  - `submitRfq()` - Convert cart to RFQ
  - `loadTemplate()` - Load saved template
- **Dependencies**: `RfqBuilderService`, `RfqCreationService`

**BuyerQuotationController**
- **Routes**: `/buyer/quotations/*`
- **Methods**:
  - `index()` - List quotations for buyer's RFQs
  - `show()` - View quotation details
  - `compare()` - Compare multiple quotations
  - `accept()` - Accept quotation (creates order)
  - `reject()` - Reject quotation
- **Dependencies**: `QuotationWorkflowService`

**BuyerProductController**
- **Routes**: `/buyer/products/*`
- **Methods**:
  - `index()` - Browse product catalog
  - `show()` - Product details
  - `compare()` - Compare products
  - `favorites()` - Favorite products list
  - `toggleFavorite()` - Add/remove favorite
  - `createRfqWithProduct()` - Quick RFQ from product
  - `setPriceAlert()` / `setStockAlert()` - Price/stock alerts
- **Dependencies**: `BuyerProductService`, `BuyerService`

**BuyerOrderController**
- **Routes**: `/buyer/orders/*`
- **Methods**:
  - `index()` - List orders
  - `show()` - Order details
  - `reorder()` - Create new order from previous
  - `addToCart()` - Add order items to cart
  - `cancel()` - Cancel order
- **Dependencies**: `BuyerOrderService`

#### Supplier Controllers (`app/Http/Controllers/Web/Suppliers/`)

**SupplierRfqController**
- **Routes**: `/supplier/rfqs/*`
- **Methods**:
  - `index()` - List available RFQs
  - `show()` - View RFQ details
  - `createQuote()` - Show quotation form
  - `storeQuote()` - Submit quotation
  - `myQuotations()` - List submitted quotations
  - `showQuotation()` - View quotation
  - `editQuote()` / `updateQuote()` - Edit quotation
  - `destroyQuote()` - Delete quotation
- **Dependencies**: `SupplierQuotationRequest`, `QuotationWorkflowService`, `RfqWorkflowService`

**SupplierProductController**
- **Routes**: `/supplier/products/*`
- **Methods**: Full CRUD for supplier's product offers
- **Purpose**: Manage product catalog with prices, stock, lead times

**SupplierOrderController**
- **Routes**: `/supplier/orders/*`
- **Methods**:
  - `index()` - List orders
  - `show()` - Order details
  - `updateStatus()` - Update order status

**SupplierDeliveryController**
- **Routes**: `/supplier/deliveries/*`
- **Methods**:
  - `create()` - Create delivery record
  - `store()` - Save delivery
  - `updateStatus()` - Update delivery status
  - `uploadProof()` - Upload delivery proof

#### Admin Controllers (`app/Http/Controllers/Web/`)

**UserController**
- **Routes**: `/admin/users/*`
- **Purpose**: User management (CRUD, permissions)

**ProductController**
- **Routes**: `/admin/products/*`
- **Purpose**: Product catalog management

**OrderController**, **InvoiceController**, **PaymentController**, **DeliveryController**
- **Purpose**: Full CRUD for respective entities

**AdminRfqController** / **AdminQuotationController**
- **Purpose**: Admin oversight and management of RFQs/Quotations

**RolePermissionController**
- **Purpose**: Unified RBAC management interface

### Request Validation Classes

Located in `app/Http/Requests/`:
- **RfqRequest**: RFQ creation/update validation
- **QuotationRequest**: Quotation submission validation
- **OrderRequest**: Order creation validation
- **ProductRequest**: Product creation/update validation
- **UserRequest**: User management validation
- **BuyerRegistrationRequest** / **SupplierRegistrationRequest**: Registration validation

---

## Services & Business Logic

### Core Services

#### RfqWorkflowService (`app/Services/RfqWorkflowService.php`)
- **Purpose**: RFQ lifecycle management
- **Key Methods**:
  - `closeExpiredRfqs()` - Auto-close expired RFQs
  - `sendDeadlineReminders()` - Send deadline notifications
  - `canAcceptQuotations()` - Validate RFQ can accept quotes
  - `notifyNewRfq()` - Notify suppliers of new RFQ
  - `notifyQuotationSubmitted()` - Notify buyer of new quote
  - `getBuyerStats()` / `getSupplierStats()` - Statistics
- **Dependencies**: `RfqStateMachine`, `QuotationStateMachine`, `NotificationService`

#### QuotationWorkflowService (`app/Services/QuotationWorkflowService.php`)
- **Purpose**: Quotation lifecycle management
- **Key Methods**:
  - `submitQuotation()` - Submit quotation (draft → pending)
  - `acceptQuotation()` - Accept quotation (with RFQ locking)
  - `rejectQuotation()` - Reject quotation
  - `expireQuotations()` - Auto-expire old quotations
- **Dependencies**: `QuotationStateMachine`, `RfqStateMachine`, `NotificationService`

#### RfqStateMachine (`app/Services/RfqStateMachine.php`)
- **Purpose**: RFQ state transition management
- **States**: `draft` → `open` → `closed` / `awarded` / `cancelled`
- **Key Methods**:
  - `canTransition()` - Check if transition allowed
  - `transition()` - Execute state transition
  - `getAllowedTransitions()` - Get valid next states
- **Validation**: Enforces business rules (items required, deadline valid, etc.)

#### QuotationStateMachine (`app/Services/QuotationStateMachine.php`)
- **Purpose**: Quotation state transition management
- **States**: `draft` → `pending` → `accepted` / `rejected` / `expired`
- **Key Methods**: Similar to RfqStateMachine
- **Validation**: Enforces RFQ status, deadline, validity period

#### RfqBuilderService (`app/Services/RfqBuilderService.php`)
- **Purpose**: RFQ Builder (cart) management
- **Key Methods**:
  - `getOrCreateBuilder()` - Get active cart
  - `addProduct()` - Add product to cart
  - `updateItem()` - Update cart item
  - `removeItem()` - Remove cart item
  - `validateBuilder()` - Validate all items
  - `getBuilderSummary()` - Calculate totals
  - `saveAsTemplate()` - Save cart as template
  - `loadTemplate()` - Load template into cart
- **Dependencies**: `BuyerCart`, `BuyerCartItem`, `Product`

#### RfqCreationService (`app/Services/RfqCreationService.php`)
- **Purpose**: Convert cart to RFQ
- **Key Methods**:
  - `createFromCart()` - Create RFQ from cart items
  - `assignSuppliers()` - Auto-assign suppliers
- **Dependencies**: `RfqBuilderService`, `RfqWorkflowService`

#### BuyerProductService (`app/Services/BuyerProductService.php`)
- **Purpose**: Product catalog browsing for buyers
- **Key Methods**:
  - `browseProducts()` - Filtered product listing
  - `getProductDetails()` - Product detail view
  - `getRelatedProducts()` - Related products
- **Features**: Caching, filtering, sorting, favorites

#### BuyerOrderService (`app/Services/BuyerOrderService.php`)
- **Purpose**: Order management for buyers
- **Key Methods**: Order creation, reordering, cancellation

#### NotificationService (`app/Services/NotificationService.php`)
- **Purpose**: System notification management
- **Key Methods**: `send()` - Send notification to user

#### ReferenceCodeService (`app/Services/ReferenceCodeService.php`)
- **Purpose**: Generate unique reference codes
- **Prefixes**: RFQ, QUO, ORD, INV, PAY, DEL

### Supporting Services

- **BuyerService**: Buyer profile management
- **BuyerAlertService**: Price/stock alert management
- **SupplierPerformanceService**: Supplier metrics calculation
- **SupplierSuggestionService**: AI supplier recommendations
- **ProductCatalogService**: Product catalog management
- **AdminPermissionService**: Permission management utilities
- **PermissionAuditService**: Permission change tracking
- **RfqImportService**: CSV import for RFQs

---

## Policies & Authorization

### Authorization Architecture

1. **Spatie Permission Package**: Role-based permissions
2. **Laravel Policies**: Resource-level authorization
3. **Gate::before()**: Admin bypass (in `AppServiceProvider`)
4. **Middleware**: Route-level protection

### Policy Structure

Policies located in `app/Policies/`:

#### RfqPolicy
- **viewAny()**: Buyers/Suppliers can view their RFQs
- **view()**: Ownership/assignment check
- **create()**: Buyers can create
- **update()**: Ownership check
- **delete()**: Ownership + no quotations check
- **createQuotation()**: Supplier verification + RFQ access

#### QuotationPolicy
- **viewAny()**: Buyers/Suppliers can view their quotations
- **view()**: Ownership check
- **create()**: Suppliers only
- **update()**: Supplier ownership only
- **accept()** / **reject()**: Buyer ownership of RFQ

#### OrderPolicy
- **viewAny()**: Buyers/Suppliers can view their orders
- **view()**: Ownership check
- **update()**: Suppliers can update status
- **updateStatus()**: Supplier ownership

#### ProductPolicy
- **viewAny()**: Public catalog access
- **view()**: Approved products only
- **create()**: Suppliers/Admins
- **update()**: Ownership or admin
- **approve()** / **reject()**: Admin only

### Permission System

**Roles** (from `UnifiedRolePermissionSeeder`):
- **Admin**: Full access (bypasses all checks)
- **Staff**: Limited admin access (permission-based)
- **Supplier**: Supplier-specific permissions
- **Buyer**: Buyer-specific permissions

**Permissions** (atomic actions):
- `users.view`, `users.create`, `users.update`, `users.delete`
- `rfqs.view`, `rfqs.create`, `rfqs.update`, `rfqs.delete`
- `quotations.view`, `quotations.submit`, `quotations.accept`
- `orders.view`, `orders.create`, `orders.update`
- `products.view`, `products.create`, `products.approve`
- ... (see seeder for full list)

---

## Database Schema

### Core Tables

#### users
- `id`, `user_type_id`, `name`, `email`, `password`, `status`
- Foreign keys: `user_type_id`, `created_by`, `updated_by`
- Indexes: `email` (unique), `phone`, `status`

#### buyers
- `id`, `user_id`, `organization_name`, `organization_type`, `license_number`
- `is_verified`, `verified_at`, `is_active`, `rejection_reason`
- Foreign keys: `user_id`, `created_by`, `updated_by`

#### suppliers
- `id`, `user_id`, `company_name`, `commercial_register`, `tax_number`
- `is_verified`, `verified_at`, `is_active`, `rejection_reason`
- Foreign keys: `user_id`, `created_by`, `updated_by`

#### products
- `id`, `sku`, `name`, `model`, `brand`, `manufacturer_id`, `category_id`
- `description`, `is_active`, `review_status`, `review_notes`
- `specifications` (JSON), `features` (JSON), `technical_data` (JSON)
- `medical_class`, `ce_marked`, `fda_cleared`, `iso_certification`
- `min_price` (denormalized), `suppliers_count` (denormalized)
- Foreign keys: `manufacturer_id`, `category_id`, `created_by`, `updated_by`

#### product_supplier (pivot)
- `product_id`, `supplier_id`, `price`, `stock_quantity`, `lead_time`
- `warranty`, `status`, `notes`
- Foreign keys: `product_id`, `supplier_id`

#### rfqs
- `id`, `buyer_id`, `reference_code`, `title`, `description`, `deadline`
- `status` (enum: open, closed, cancelled, awarded), `is_public`
- `published_at`, `closed_at`, `awarded_at`, `cancelled_at`
- `awarded_quotation_id`
- Foreign keys: `buyer_id`, `created_by`, `updated_by`, `awarded_quotation_id`
- Indexes: `buyer_id`, `status`, `deadline`

#### rfq_items
- `id`, `rfq_id`, `product_id`, `item_name`, `specifications`, `quantity`, `unit`
- `preferred_supplier_id`, `max_price`
- Foreign keys: `rfq_id`, `product_id`, `preferred_supplier_id`

#### rfq_supplier (pivot)
- `rfq_id`, `supplier_id`, `status`, `invited_at`, `viewed_at`, `notes`
- Foreign keys: `rfq_id`, `supplier_id`

#### quotations
- `id`, `rfq_id`, `supplier_id`, `reference_code`, `total_price`, `terms`
- `status` (enum: draft, pending, accepted, rejected, expired, withdrawn, converted)
- `valid_until`, `submitted_at`, `accepted_at`, `rejected_at`, `expired_at`
- `accepted_by`, `rejected_by`, `rejection_reason`
- Foreign keys: `rfq_id`, `supplier_id`, `accepted_by`, `rejected_by`
- **Unique constraint**: `rfq_id` + `status='accepted'` (one accepted per RFQ)

#### quotation_items
- `id`, `quotation_id`, `rfq_item_id`, `product_id`, `item_name`
- `quantity`, `unit_price`, `total_price`, `lead_time`, `warranty`, `notes`
- Foreign keys: `quotation_id`, `rfq_item_id`, `product_id`

#### orders
- `id`, `quotation_id`, `buyer_id`, `supplier_id`, `order_number`, `order_date`
- `status` (enum: pending, processing, shipped, delivered, cancelled)
- `total_amount`, `currency`, `notes`
- Foreign keys: `quotation_id`, `buyer_id`, `supplier_id`, `created_by`

#### order_items
- `id`, `order_id`, `quotation_item_id`, `product_id`, `quantity`
- `unit_price`, `total_price`
- Foreign keys: `order_id`, `quotation_item_id`, `product_id`

#### invoices
- `id`, `order_id`, `invoice_number`, `invoice_date`
- `subtotal`, `tax`, `discount`, `total_amount`
- `status` (enum: draft, issued, approved, cancelled)
- `payment_status` (enum: unpaid, partial, paid)
- Foreign keys: `order_id`, `created_by`, `approved_by`

#### payments
- `id`, `invoice_id`, `order_id`, `buyer_id`, `supplier_id`
- `payment_reference`, `amount`, `currency`, `method`, `transaction_id`
- `status`, `paid_at`, `processed_by`
- Foreign keys: `invoice_id`, `order_id`, `buyer_id`, `supplier_id`, `processed_by`
- **Auto-sync**: `buyer_id` and `supplier_id` synced from `order_id` on create/update

#### deliveries
- `id`, `order_id`, `supplier_id`, `buyer_id`, `delivery_number`, `delivery_date`
- `status` (enum: pending, in_transit, delivered, failed)
- `delivery_location`, `receiver_name`, `receiver_phone`, `notes`
- `is_verified`, `verified_at`
- Foreign keys: `order_id`, `supplier_id`, `buyer_id`, `created_by`, `verified_by`

#### buyer_carts
- `id`, `buyer_id`, `name`, `template_name`, `is_template`, `source`
- `is_active`, `is_saved`, `expires_at`
- Foreign keys: `buyer_id`

#### buyer_cart_items
- `id`, `cart_id`, `product_id`, `quantity`, `specifications`, `unit`
- `supplier_id`, `max_price`
- Foreign keys: `cart_id`, `product_id`, `supplier_id`

### Spatie Permission Tables

- **roles**: `id`, `name`, `ar_name`, `guard_name`
- **permissions**: `id`, `name`, `ar_name`, `guard_name`
- **model_has_roles**: `role_id`, `model_type`, `model_id`
- **model_has_permissions**: `permission_id`, `model_type`, `model_id`
- **role_has_permissions**: `role_id`, `permission_id`

### Activity Log Tables

- **activity_log**: Spatie activity log (polymorphic)

### Media Tables

- **media**: Spatie media library (polymorphic)

---

## Workflows & State Machines

### RFQ Workflow

```
[draft] → [open] → [closed] / [awarded] / [cancelled]
```

**State Transitions**:
- `draft` → `open`: Publish RFQ (requires items, valid deadline)
- `open` → `closed`: Deadline passed or manually closed
- `open` → `awarded`: Quotation accepted (auto-closes RFQ)
- `open` → `cancelled`: Buyer cancels
- `closed` → `awarded`: Award quotation after closing
- `closed` → `open`: Reopen if no accepted quotation

**Business Rules**:
- RFQ must have at least one item to publish
- Deadline must be in future
- Only one quotation can be accepted per RFQ
- Auto-close on deadline expiration
- Auto-expire pending quotations when RFQ closes

### Quotation Workflow

```
[draft] → [pending] → [accepted] / [rejected] / [expired]
         ↓
    [revised] → [pending]
```

**State Transitions**:
- `draft` → `pending`: Submit quotation
- `pending` → `accepted`: Buyer accepts (locks RFQ, rejects others)
- `pending` → `rejected`: Buyer rejects
- `pending` → `expired`: Auto-expire (deadline passed or RFQ closed)
- `pending` → `revised`: Supplier revises
- `revised` → `pending`: Resubmit revision
- `accepted` → `converted`: Order created

**Business Rules**:
- Only one quotation can be accepted per RFQ (unique constraint)
- RFQ must be open to accept quotations
- Deadline must not be passed
- Auto-reject other pending quotations when one is accepted
- Auto-expire on RFQ deadline or validity period

### Order Workflow

```
[pending] → [processing] → [shipped] → [delivered]
                              ↓
                         [cancelled]
```

**State Transitions**:
- `pending` → `processing`: Supplier starts processing
- `processing` → `shipped`: Order shipped
- `shipped` → `delivered`: Delivery confirmed
- Any state → `cancelled`: Order cancelled

### Invoice Workflow

```
[draft] → [issued] → [approved] → [paid]
            ↓
       [cancelled]
```

**Payment Status**:
- `unpaid` → `partial` → `paid`

### Delivery Workflow

```
[pending] → [in_transit] → [delivered]
              ↓
          [failed]
```

---

## Dependencies & Relationships

### Model Dependency Graph

```
User
├── Buyer (1:1)
│   ├── Rfq (1:N)
│   │   ├── RfqItem (1:N)
│   │   ├── Quotation (1:N)
│   │   │   ├── QuotationItem (1:N)
│   │   │   └── Order (1:N)
│   │   │       ├── OrderItem (1:N)
│   │   │       ├── Invoice (1:N)
│   │   │       │   └── Payment (1:N)
│   │   │       └── Delivery (1:N)
│   │   └── Supplier (N:M via rfq_supplier)
│   ├── Order (1:N)
│   └── BuyerCart (1:N)
│       └── BuyerCartItem (1:N)
│
└── Supplier (1:1)
    ├── Product (N:M via product_supplier)
    ├── Quotation (1:N)
    ├── Order (1:N)
    └── Delivery (1:N)

Product
├── ProductCategory (N:1)
├── Manufacturer (N:1)
├── Supplier (N:M via product_supplier)
└── RfqItem (1:N)
```

### Service Dependencies

```
RfqWorkflowService
├── RfqStateMachine
├── QuotationStateMachine
└── NotificationService

QuotationWorkflowService
├── QuotationStateMachine
├── RfqStateMachine
└── NotificationService

RfqBuilderService
├── BuyerCart
├── BuyerCartItem
└── Product

RfqCreationService
├── RfqBuilderService
└── RfqWorkflowService

BuyerProductService
├── Product
├── ProductCategory
├── Manufacturer
└── BuyerService
```

### Controller Dependencies

```
BuyerRfqController
├── RfqRequest (validation)
├── RfqWorkflowService
├── ReferenceCodeService
├── RfqImportService
└── NotificationService

SupplierRfqController
├── SupplierQuotationRequest (validation)
├── QuotationWorkflowService
├── RfqWorkflowService
└── ReferenceCodeService

BuyerCartController
├── RfqBuilderService
└── RfqCreationService

BuyerQuotationController
└── QuotationWorkflowService
```

---

## File Index

### Controllers (63 files)

**Web Controllers**:
- `BaseController.php` - Base web controller
- `UserController.php` - User management
- `ProductController.php` - Product management
- `ProductCategoryController.php` - Category management
- `OrderController.php` - Order management
- `InvoiceController.php` - Invoice management
- `PaymentController.php` - Payment management
- `DeliveryController.php` - Delivery management
- `AdminRfqController.php` - Admin RFQ management
- `AdminQuotationController.php` - Admin quotation management
- `RolePermissionController.php` - RBAC management
- `ActivityLogController.php` - Activity logs
- `NotificationController.php` - Notifications
- `SettingController.php` - Settings

**Buyer Controllers** (13 files):
- `BuyerRfqController.php`
- `BuyerCartController.php`
- `BuyerQuotationController.php`
- `BuyerProductController.php`
- `BuyerOrderController.php`
- `BuyerInvoiceController.php`
- `BuyerDeliveryController.php`
- `BuyerDeliveryTrackingController.php`
- `BuyerProfileController.php`
- `BuyerNotificationController.php`
- `BuyerSupplierController.php`
- `BuyerReportsController.php`
- `BuyerReviewController.php`
- `BuyerRfqTemplateController.php`

**Supplier Controllers** (13 files):
- `SupplierRfqController.php`
- `SupplierProductController.php`
- `SupplierOrderController.php`
- `SupplierInvoiceController.php`
- `SupplierPaymentController.php`
- `SupplierDeliveryController.php`
- `SupplierProfileController.php`
- `SupplierNotificationController.php`
- `SupplierActivityLogController.php`
- `SupplierReportsController.php`
- `SupplierPerformanceController.php`
- `SupplierDashboardController.php`

**API Controllers**:
- `ProductSearchController.php` - Product search API

### Models (37 files)

**Core Models**:
- `User.php`
- `Buyer.php`
- `Supplier.php`
- `Product.php`
- `ProductCategory.php`
- `Manufacturer.php`
- `Rfq.php`
- `RfqItem.php`
- `Quotation.php`
- `QuotationItem.php`
- `Order.php`
- `OrderItem.php`
- `Invoice.php`
- `Payment.php`
- `Delivery.php`

**Supporting Models**:
- `BuyerCart.php`, `BuyerCartItem.php`
- `BuyerFavorite.php`
- `BuyerPriceAlert.php`, `BuyerStockAlert.php`
- `ProductSupplier.php`
- `ProductReview.php`
- `SupplierReview.php`
- `DeliveryTracking.php`, `DeliveryDispute.php`, `DeliveryReview.php`
- `RfqTemplate.php`, `RfqTemplateItem.php`
- `ProductRequest.php`
- `ProductPriceHistory.php`
- `SupplierPerformanceMetric.php`
- `Role.php`, `Permission.php`
- `UserType.php`
- `Setting.php`
- `Notification.php`
- `ActivityLog.php` (Spatie)

### Services (16 files)

- `RfqWorkflowService.php`
- `QuotationWorkflowService.php`
- `RfqStateMachine.php`
- `QuotationStateMachine.php`
- `RfqBuilderService.php`
- `RfqCreationService.php`
- `RfqImportService.php`
- `BuyerProductService.php`
- `BuyerOrderService.php`
- `BuyerService.php`
- `BuyerAlertService.php`
- `SupplierPerformanceService.php`
- `SupplierSuggestionService.php`
- `ProductCatalogService.php`
- `NotificationService.php`
- `ReferenceCodeService.php`
- `AdminPermissionService.php`
- `PermissionAuditService.php`

### Policies (18 files)

- `RfqPolicy.php`
- `QuotationPolicy.php`
- `OrderPolicy.php`
- `InvoicePolicy.php`
- `PaymentPolicy.php`
- `DeliveryPolicy.php`
- `ProductPolicy.php`
- `ProductRequestPolicy.php`
- `ProductCategoryPolicy.php`
- `ManufacturerPolicy.php`
- `BuyerPolicy.php`
- `SupplierPolicy.php`
- `UserPolicy.php`
- `RolePolicy.php`
- `PermissionPolicy.php`
- `SettingPolicy.php`
- `NotificationPolicy.php`
- `ActivityLogPolicy.php`

### Requests (27 files)

- `RfqRequest.php`
- `QuotationRequest.php`
- `OrderRequest.php`
- `InvoiceRequest.php`
- `PaymentRequest.php`
- `DeliveryRequest.php`
- `ProductRequest.php`
- `UserRequest.php`
- `BuyerRequest.php`
- `SupplierRequest.php`
- `ManufacturerRequest.php`
- `BuyerRegistrationRequest.php`
- `SupplierRegistrationRequest.php`
- `ProfileUpdateRequest.php`
- `BuyerProfileUpdateRequest.php`
- `SupplierProfileUpdateRequest.php`
- `SupplierQuotationRequest.php`
- `SupplierProductRequest.php`
- `SupplierDeliveryRequest.php`
- `SupplierDeliveryStatusRequest.php`
- `SupplierDeliveryProofRequest.php`
- `BuyerCartRequest.php`
- `BuyerReviewRequest.php`
- `AdminRfqItemRequest.php`
- `FileRequest.php`
- `LoginRequest.php` (Auth)

### Migrations (67 files)

**Core Tables**:
- `create_users_table.php`
- `create_user_types_table.php`
- `create_buyers_table.php`
- `create_suppliers_table.php`
- `create_products_table.php`
- `create_product_categories_table.php`
- `create_manufacturers_table.php`
- `create_product_supplier_table.php`
- `create_rfqs_table.php`
- `create_rfq_items_table.php`
- `create_rfq_supplier_table.php`
- `create_quotations_table.php`
- `create_quotation_items_table.php`
- `create_orders_table.php`
- `create_order_items_table.php`
- `create_invoices_table.php`
- `create_payments_table.php`
- `create_deliveries_table.php`

**Workflow Enhancements**:
- `add_workflow_fields_to_rfqs_table.php`
- `add_workflow_fields_to_quotations_table.php`
- `update_quotation_status_enum.php`
- `add_unique_accepted_quotation_constraint.php`

**Buyer Journey**:
- `add_buyer_journey_phase1_products.php`
- `add_buyer_journey_phase1_cart_rfq_builder.php`
- `create_buyer_carts_table.php`
- `create_buyer_cart_items_table.php`
- `add_preferred_supplier_max_price_to_rfq_items.php`

**Alerts & Tracking**:
- `create_product_price_history_table.php`
- `create_buyer_price_alerts_table.php`
- `create_buyer_stock_alerts_table.php`
- `create_delivery_trackings_table.php`
- `create_delivery_disputes_table.php`

**RBAC**:
- `create_permission_tables.php` (Spatie)
- `add_ar_name_to_roles_table.php`
- `add_ar_name_to_permissions_table.php`

**Other**:
- `create_activity_log_table.php` (Spatie)
- `create_notifications_table.php`
- `create_settings_table.php`
- `create_product_reviews_table.php`
- `create_supplier_reviews_table.php`
- `create_supplier_performance_metrics_table.php`
- `create_rfq_templates_table.php`
- `create_product_requests_table.php`
- `create_buyer_favorites_table.php`

### Seeders (10 files)

- `DatabaseSeeder.php` - Main seeder
- `UserTypeSeeder.php` - User types
- `UnifiedRolePermissionSeeder.php` - Roles & permissions
- `AdminSeeder.php` - Admin user
- `ProductCategorySeeder.php` - Product categories
- `ManufacturerSeeder.php` - Manufacturers
- `ProductCatalogSeeder.php` - Sample products
- `SettingsSeeder.php` - System settings
- `PermissionSeeder.php` - Legacy permissions
- `RolePermissionSeeder.php` - Legacy roles

### Factories (7 files)

- `UserFactory.php`
- `SupplierFactory.php`
- `ProductFactory.php`
- `RfqFactory.php`
- `RfqItemFactory.php`
- `QuotationFactory.php`
- `QuotationItemFactory.php`

### Observers (3 files)

- `ProductSupplierObserver.php` - Updates `min_price` and `suppliers_count` on Product
- `ProductCategoryObserver.php` - Invalidates cache on category changes
- `ManufacturerObserver.php` - Invalidates cache on manufacturer changes

### Exports (10 files)

- `AdminUsersExport.php`
- `AdminBuyersExport.php`
- `AdminSuppliersExport.php`
- `AdminOrdersExport.php`
- `AdminQuotationsExport.php`
- `AdminDeliveriesExport.php`
- `AdminInvoicesExport.php`
- `AdminPaymentsExport.php`
- `SupplierInvoicesExport.php`
- `SupplierOrdersExport.php`
- `SupplierQuotationsExport.php`

### Commands (17 files)

- `TestRbacSystem.php` - RBAC testing
- `CheckAuthorizationErrors.php` - Authorization diagnostics
- `ExpireQuotations.php` - Auto-expire quotations
- `CloseExpiredRfqs.php` - Auto-close RFQs
- `CheckPriceAlerts.php` - Price alert notifications
- `CheckStockAlerts.php` - Stock alert notifications
- `SendRfqDeadlineReminders.php` - Deadline reminders
- `SendAbandonedCartReminders.php` - Cart reminders
- `BackfillProductMinPrice.php` - Data migration
- `CalculateSupplierPerformance.php` - Performance metrics
- `ResetPermissionCache.php` - Cache management
- `VerifyAdminPermissions.php` - Permission verification
- `DiagnosePermissions.php` - Permission diagnostics
- `ValidatePermissions.php` - Permission validation
- `FixAdminPermissions.php` - Permission fixes
- `AddProductPlaceholderImages.php` - Image management
- `RemoveProductPlaceholderImages.php` - Image cleanup

---

## Key Workflows Summary

### Buyer Journey

1. **Browse Products** → `BuyerProductController::index()`
2. **Add to Cart** → `BuyerCartController::add()`
3. **Build RFQ** → `BuyerCartController::index()`
4. **Submit RFQ** → `BuyerCartController::submitRfq()` → `BuyerRfqController::store()`
5. **View Quotations** → `BuyerQuotationController::index()`
6. **Compare Quotations** → `BuyerQuotationController::compare()`
7. **Accept Quotation** → `BuyerQuotationController::accept()` → Creates Order
8. **Track Order** → `BuyerOrderController::show()`
9. **View Invoice** → `BuyerInvoiceController::show()`
10. **Track Delivery** → `BuyerDeliveryController::show()`

### Supplier Journey

1. **View RFQs** → `SupplierRfqController::index()`
2. **View RFQ Details** → `SupplierRfqController::show()`
3. **Create Quotation** → `SupplierRfqController::createQuote()`
4. **Submit Quotation** → `SupplierRfqController::storeQuote()`
5. **View Orders** → `SupplierOrderController::index()`
6. **Update Order Status** → `SupplierOrderController::updateStatus()`
7. **Create Delivery** → `SupplierDeliveryController::create()`
8. **Upload Delivery Proof** → `SupplierDeliveryController::uploadProof()`
9. **View Invoices** → `SupplierInvoiceController::index()`

### RFQ → Quotation → Order Flow

```
Buyer creates RFQ
    ↓
RFQ published (open status)
    ↓
Suppliers view RFQ
    ↓
Suppliers submit Quotations (pending status)
    ↓
Buyer compares Quotations
    ↓
Buyer accepts one Quotation
    ↓
Quotation → accepted status
    ↓
Other Quotations → rejected status
    ↓
RFQ → awarded status
    ↓
Order created from accepted Quotation
    ↓
Invoice generated
    ↓
Payment processed
    ↓
Delivery created
    ↓
Order → delivered status
```

---

## Critical Business Rules

1. **One Accepted Quotation Per RFQ**: Database unique constraint ensures only one quotation can be accepted per RFQ
2. **RFQ Locking**: When accepting quotation, RFQ row is locked to prevent race conditions
3. **Auto-Expiration**: Quotations expire automatically when RFQ deadline passes or RFQ closes
4. **Auto-Rejection**: When one quotation is accepted, all other pending quotations are auto-rejected
5. **Product Denormalization**: `min_price` and `suppliers_count` are denormalized on Product model for performance
6. **Admin Bypass**: Admin role bypasses all authorization checks via `Gate::before()`
7. **State Machine Validation**: All state transitions go through state machines with strict validation
8. **Reference Code Uniqueness**: All reference codes (RFQ, QUO, ORD, etc.) are unique
9. **Payment Auto-Sync**: `buyer_id` and `supplier_id` on Payment are auto-synced from Order
10. **Cart Expiration**: Active carts expire after 30 days

---

## Performance Optimizations

1. **Eager Loading**: Controllers use `with()` to prevent N+1 queries
2. **Caching**: Product filters cached for 1 hour
3. **Denormalization**: Product `min_price` and `suppliers_count` cached
4. **Database Indexes**: Key foreign keys and status fields indexed
5. **Query Scopes**: Reusable query filters via model scopes
6. **Pagination**: All list views paginated (15 items default)

---

## Security Considerations

1. **Authorization**: Policy-based authorization on all resources
2. **Validation**: Form request validation on all inputs
3. **CSRF Protection**: Laravel CSRF middleware
4. **SQL Injection**: Eloquent ORM prevents SQL injection
5. **XSS Protection**: Blade templating escapes output
6. **Password Hashing**: Bcrypt with auto-hash prevention
7. **Soft Deletes**: Data retention via soft deletes
8. **Activity Logging**: All changes logged via Spatie Activity Log

---

## Testing Structure

- **Feature Tests**: `tests/Feature/`
  - `RbacVerificationTest.php` - RBAC system tests
  - `RfqQuotationWorkflowTest.php` - Workflow tests
- **Unit Tests**: `tests/Unit/`
  - `QuotationStateMachineTest.php`
  - `RfqStateMachineTest.php`

---

## Conclusion

This codebase implements a comprehensive B2B medical equipment marketplace with:

- **Robust RBAC**: Spatie permissions with role-based access
- **State Machine Workflows**: Deterministic RFQ/Quotation lifecycle
- **Service Layer**: Clean separation of business logic
- **Policy-Based Authorization**: Resource-level access control
- **Comprehensive Models**: Rich relationships and business logic
- **Performance Optimizations**: Caching, denormalization, eager loading
- **Activity Logging**: Full audit trail
- **Media Management**: Spatie media library integration

The architecture follows Laravel best practices with clear separation of concerns, making it maintainable and scalable.

---

**Document Version**: 1.0  
**Last Updated**: January 27, 2026  
**Total Files Indexed**: 200+ files
