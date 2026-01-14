# MedEquip Codebase Index

**Last Updated:** 2026-01-01  
**Project Type:** Laravel 12 Medical Equipment E-Commerce Platform  
**Language:** PHP 8.2+ with Blade Templates, JavaScript, Tailwind CSS

---

## 📋 Table of Contents

1. [Project Overview](#project-overview)
2. [Technology Stack](#technology-stack)
3. [Directory Structure](#directory-structure)
4. [Core Components](#core-components)
5. [Database Architecture](#database-architecture)
6. [Routing Structure](#routing-structure)
7. [Frontend Components](#frontend-components)
8. [Authentication & Authorization](#authentication--authorization)
9. [Key Features](#key-features)
10. [Development Workflows](#development-workflows)
11. [Testing](#testing)

---

## 🎯 Project Overview

**MedEquip** is a comprehensive B2B medical equipment e-commerce platform that connects medical equipment suppliers with healthcare institutions in the Arab world. The platform facilitates RFQs (Request for Quotations), quotations, orders, payments, and deliveries.

### Project Goals
- Connect medical equipment suppliers with buyers (healthcare institutions)
- Streamline procurement process through digital RFQ/quotation workflows
- Provide comprehensive product catalog with categories
- Enable secure payment and delivery tracking
- Implement role-based access control for admins, suppliers, and buyers

---

## 🛠 Technology Stack

### Backend
- **Framework:** Laravel 12.x
- **PHP Version:** 8.2+
- **Database:** SQLite (development), supports MySQL/PostgreSQL
- **Authentication:** Laravel Sanctum & Breeze
- **Authorization:** Spatie Laravel Permission (roles & permissions)

### Frontend
- **CSS Framework:** Tailwind CSS 3.x (migrated from Bootstrap)
- **JavaScript:** Alpine.js 3.4.2
- **Build Tool:** Vite 7.x
- **Templating:** Blade (Laravel)
- **Fonts:** Inter, Poppins, Cairo (Arabic support)

### Key Packages
- `spatie/laravel-permission` - Role & permission management
- `spatie/laravel-medialibrary` - Media handling
- `spatie/laravel-activitylog` - Activity tracking
- `spatie/laravel-query-builder` - API query building
- `barryvdh/laravel-dompdf` - PDF generation
- `intervention/image` - Image manipulation
- `maatwebsite/excel` - Excel import/export

---

## 📁 Directory Structure

```
MedEquip/
├── app/
│   ├── Console/              # Artisan commands
│   ├── Exports/              # Excel export classes (11 files)
│   ├── Filters/              # Query filters
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/         # Authentication controllers (9 files)
│   │   │   └── Web/          # Main application controllers (26 files)
│   │   ├── Middleware/       # Custom middleware (2 files)
│   │   └── Requests/         # Form request validation (18 files)
│   ├── Models/               # Eloquent models (21 models)
│   ├── Notifications/        # Custom notifications
│   ├── Policies/             # Authorization policies (17 files)
│   ├── Providers/            # Service providers
│   ├── Services/             # Business logic services (2 services)
│   ├── Traits/               # Reusable traits
│   └── View/                 # View composers
│
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations (35 files)
│   │   ├── 2025_10_31_000001_create_user_types_table.php
│   │   ├── 2025_10_31_000002_create_users_table.php
│   │   ├── 2025_10_31_000003_create_password_reset_tokens_table.php
│   │   ├── 2025_10_31_000004_create_sessions_table.php
│   │   ├── 2025_10_31_000005_create_cache_table.php
│   │   ├── 2025_10_31_000006_create_cache_locks_table.php
│   │   ├── 2025_10_31_000007_create_jobs_table.php
│   │   ├── 2025_10_31_000008_create_job_batches_table.php
│   │   ├── 2025_10_31_000009_create_failed_jobs_table.php
│   │   ├── 2025_10_31_000010_create_personal_access_tokens_table.php
│   │   ├── 2025_10_31_000011_create_permission_tables.php
│   │   ├── 2025_10_31_000012_create_media_table.php
│   │   ├── 2025_10_31_000013_create_activity_log_table.php
│   │   ├── 2025_10_31_000014_create_suppliers_table.php
│   │   ├── 2025_10_31_000015_create_product_categories_table.php
│   │   ├── 2025_10_31_000016_create_buyers_table.php
│   │   ├── 2025_10_31_000018_create_products_table.php
│   │   ├── 2025_10_31_000019_create_product_supplier_table.php
│   │   ├── 2025_10_31_000020_create_rfqs_table.php
│   │   ├── 2025_10_31_000021_create_quotations_table.php
│   │   ├── 2025_10_31_000022_create_orders_table.php
│   │   ├── 2025_10_31_000023_create_invoices_table.php
│   │   ├── 2025_10_31_000024_create_payments_table.php
│   │   ├── 2025_10_31_000025_create_deliveries_table.php
│   │   ├── 2025_11_02_191341_create_rfq_items_table.php
│   │   ├── 2025_11_03_130302_create_notifications_table.php
│   │   ├── 2025_11_13_000004_create_quotation_items_table.php
│   │   ├── 2025_11_13_000005_create_order_items_table.php
│   │   ├── 2025_11_30_163947_create_settings_table.php
│   │   ├── 2025_12_02_125526_add_review_status_to_products_table.php
│   │   ├── 2025_12_04_183056_create_manufacturers_table.php
│   │   ├── 2025_12_23_add_missing_columns_to_products_table.php
│   │   └── 2025_12_23_create_rfq_supplier_table.php
│   │   ├── 2025_12_28_164107_add_ar_name_to_roles_table.php
│   │   └── 2025_12_28_164822_add_ar_name_to_permissions_table.php
│   │   ├── 2025_01_27_000001_fix_rfq_status_enum.php
│   │   └── 2025_01_27_000002_add_rejection_reason_to_quotations.php
│   ├── seeders/              # Database seeders (8 files)
│   │   ├── AdminSeeder.php - Create admin user
│   │   ├── DatabaseSeeder.php - Main seeder
│   │   ├── ManufacturerSeeder.php - Seed manufacturers
│   │   ├── ProductCategorySeeder.php - Seed product categories
│   │   ├── PermissionSeeder.php - Seed permissions
│   │   ├── RolePermissionSeeder.php - Seed roles and permissions
│   │   ├── SettingsSeeder.php - Seed system settings
│   │   └── UserTypeSeeder.php - Seed user types
│   └── database.sqlite       # SQLite database
│
├── resources/
│   ├── css/                  # Custom CSS
│   ├── js/                   # JavaScript files
│   └── views/
│       ├── admin/            # Admin panel views (9 sections)
│       ├── auth/             # Authentication views (7 files)
│       ├── components/       # Reusable Blade components (33 files)
│       ├── dashboards/       # Dashboard views (3 types)
│       ├── layouts/          # Layout templates (4 layouts)
│       ├── partials/         # Partial views
│       ├── profile/          # User profile views
│       ├── sections/         # Landing page sections (8 sections)
│       └── vendor/           # Third-party vendor views
│
├── routes/
│   ├── auth.php              # Authentication routes
│   ├── console.php           # Console routes
│   └── web.php               # Web application routes
│
├── public/
│   ├── assets/               # Static assets
│   ├── build/                # Vite build output
│   └── storage/              # Public storage link
│
├── config/                   # Configuration files (20 files)
├── storage/                  # File storage
├── tests/                    # PHPUnit tests
└── vendor/                   # Composer dependencies
```

---

## 🔧 Core Components

### Controllers

#### Web Controllers (`app/Http/Controllers/Web/`) - 26 Controllers

**Admin Controllers:**
1. **ActivityLogController.php** - Activity logging and audit trails viewing
2. **AdminDashboardController.php** - Admin dashboard overview with analytics
3. **AdminManufacturerController.php** - Manufacturer management (CRUD operations)
4. **AdminQuotationController.php** - Admin monitoring and management of quotations
5. **AdminReportsController.php** - Admin reporting and analytics
6. **AdminRfqController.php** - Admin monitoring and management of RFQs
7. **AdminRfqItemController.php** - Admin RFQ item management
8. **BuyerController.php** - Buyer management (CRUD operations)
9. **BuyerDashboardController.php** - Buyer dashboard overview
10. **NotificationController.php** - System notifications management
11. **OrderController.php** - Order processing and management
12. **PermissionController.php** - Permission management
13. **ProductCategoryController.php** - Product category management
14. **ProductController.php** - Product catalog management
15. **ProductReviewController.php** - Product review and approval workflow
16. **RegistrationApprovalController.php** - Approve/reject supplier/buyer registrations
17. **RoleController.php** - Role management (CRUD operations)
18. **SettingController.php** - System settings management
19. **SupplierController.php** - Supplier management (CRUD operations)
20. **UserController.php** - User administration

**General Controllers:**
21. **DeliveryController.php** - Delivery tracking and management
22. **InvoiceController.php** - Invoice generation and management
23. **PaymentController.php** - Payment processing
24. **ProfileController.php** - User profile management (duplicate, see note below)

#### Supplier Controllers (`app/Http/Controllers/Web/Suppliers/`) - 11 Controllers

1. **SupplierActivityLogController.php** - Supplier activity log viewing
2. **SupplierDashboardController.php** - Supplier dashboard overview
3. **SupplierDeliveryController.php** - Supplier delivery management
4. **SupplierInvoiceController.php** - Supplier invoice viewing
5. **SupplierNotificationController.php** - Supplier notifications
6. **SupplierOrderController.php** - Supplier order management
7. **SupplierPaymentController.php** - Supplier payment management
8. **SupplierProductController.php** - Supplier product catalog management
9. **SupplierProfileController.php** - Supplier profile management
10. **SupplierReportsController.php** - Supplier reporting and analytics
11. **SupplierRfqController.php** - Supplier RFQ viewing and quotation creation

#### Auth Controllers (`app/Http/Controllers/Auth/`) - 9 Controllers

1. **AuthenticatedSessionController.php** - Login/logout handling
2. **ConfirmablePasswordController.php** - Password confirmation
3. **EmailVerificationNotificationController.php** - Email verification notifications
4. **EmailVerificationPromptController.php** - Email verification prompt
5. **NewPasswordController.php** - Password reset handling
6. **PasswordController.php** - Password update
7. **PasswordResetLinkController.php** - Password reset link generation
8. **RegisteredUserController.php** - User registration (suppliers & buyers)
9. **VerifyEmailController.php** - Email verification

**Total Controllers: 46**

### Models (`app/Models/`) - 21 Models

**User Management:**
1. **User.php** - User accounts (admins, suppliers, buyers) with relationships
2. **UserType.php** - User role types (Admin, Supplier, Buyer)
3. **Role.php** - Spatie role model (extended)
4. **Permission.php** - Spatie permission model (extended)

**Business Entities:**
5. **Supplier.php** - Supplier entities with approval workflow
6. **Buyer.php** - Buyer/healthcare institution entities with approval workflow
7. **Product.php** - Medical equipment products with categories and suppliers
8. **ProductCategory.php** - Product categorization with hierarchical structure
9. **ProductSupplier.php** - Pivot model for product-supplier relationships
10. **Manufacturer.php** - Product manufacturer information

**Transaction Flow:**
11. **Rfq.php** - Request for Quotations from buyers
12. **RfqItem.php** - Individual items in RFQs
13. **Quotation.php** - Supplier quotations in response to RFQs
14. **QuotationItem.php** - Individual items in quotations
15. **Order.php** - Purchase orders
16. **OrderItem.php** - Individual items in orders
17. **Invoice.php** - Generated invoices
18. **Payment.php** - Payment records
19. **Delivery.php** - Delivery tracking

**System:**
20. **ActivityLog.php** - Activity log entries (Spatie Activity Log)
21. **Setting.php** - System settings configuration

### Services (`app/Services/`) - 2 Services

1. **NotificationService.php** - Centralized notification handling and dispatch
2. **ReferenceCodeService.php** - Generate unique reference codes for entities (RFQs, Orders, Invoices, etc.)

### Traits (`app/Traits/`) - 1 Trait

1. **Auditable.php** - Trait for models that need activity logging

### Filters (`app/Filters/`) - 1 Filter

1. **ActivityLogFilter.php** - Query filtering for activity logs

### Policies (`app/Policies/`) - 17 Policies

Authorization policies for resource access control:
1. **ActivityLogPolicy.php** - Activity log access control
2. **BuyerPolicy.php** - Buyer resource access control
3. **DeliveryPolicy.php** - Delivery resource access control
4. **InvoicePolicy.php** - Invoice resource access control
5. **ManufacturerPolicy.php** - Manufacturer resource access control
6. **NotificationPolicy.php** - Notification access control
7. **OrderPolicy.php** - Order resource access control
8. **PaymentPolicy.php** - Payment resource access control
9. **PermissionPolicy.php** - Permission resource access control
10. **ProductCategoryPolicy.php** - Product category access control
11. **ProductPolicy.php** - Product resource access control
12. **QuotationPolicy.php** - Quotation resource access control
13. **RfqPolicy.php** - RFQ resource access control
14. **RolePolicy.php** - Role resource access control
15. **SettingPolicy.php** - Setting resource access control
16. **SupplierPolicy.php** - Supplier resource access control
17. **UserPolicy.php** - User resource access control

### Exports (`app/Exports/`) - 11 Export Classes

Excel export functionality using Maatwebsite Excel:
1. **AdminBuyersExport.php** - Export buyers data
2. **AdminDeliveriesExport.php** - Export deliveries data
3. **AdminInvoicesExport.php** - Export invoices data
4. **AdminOrdersExport.php** - Export orders data
5. **AdminPaymentsExport.php** - Export payments data
6. **AdminQuotationsExport.php** - Export quotations data
7. **AdminSuppliersExport.php** - Export suppliers data
8. **AdminUsersExport.php** - Export users data
9. **SupplierInvoicesExport.php** - Export supplier invoices
10. **SupplierOrdersExport.php** - Export supplier orders
11. **SupplierQuotationsExport.php** - Export supplier quotations

### Form Requests (`app/Http/Requests/`) - 21 Requests

**Auth Requests:**
1. **Auth/LoginRequest.php** - Login form validation

**Registration Requests:**
2. **BuyerRegistrationRequest.php** - Buyer registration validation
3. **SupplierRegistrationRequest.php** - Supplier registration validation

**Resource Requests:**
4. **BuyerRequest.php** - Buyer CRUD validation
5. **SupplierRequest.php** - Supplier CRUD validation
6. **UserRequest.php** - User CRUD validation
7. **ProductRequest.php** - Product CRUD validation
8. **OrderRequest.php** - Order CRUD validation
9. **QuotationRequest.php** - Quotation CRUD validation
10. **RfqRequest.php** - RFQ CRUD validation
11. **InvoiceRequest.php** - Invoice CRUD validation
12. **PaymentRequest.php** - Payment CRUD validation
13. **DeliveryRequest.php** - Delivery CRUD validation

**Supplier-Specific Requests:**
14. **Suppliers/SupplierProductRequest.php** - Supplier product validation
15. **Suppliers/SupplierQuotationRequest.php** - Supplier quotation validation
16. **Suppliers/SupplierDeliveryRequest.php** - Delivery creation/update validation
17. **Suppliers/SupplierDeliveryProofRequest.php** - Delivery proof upload validation
18. **Suppliers/SupplierDeliveryStatusRequest.php** - Delivery status update validation

**Other:**
19. **FileRequest.php** - File upload validation
20. **ManufacturerRequest.php** - Manufacturer CRUD validation
21. **ProfileUpdateRequest.php** - Profile update validation

### Middleware (`app/Http/Middleware/`) - 2 Custom Middleware

1. **EnsureSupplierProfile.php** - Ensures supplier has completed profile setup
2. **EnsureUserIsVerified.php** - Ensures user email is verified

---

## 🗄 Database Architecture

### Main Tables

#### User Management
- `user_types` - User role definitions (Admin, Supplier, Buyer)
- `users` - User accounts with type, status, approval
- `permissions` & `roles` - Spatie permission tables
- `model_has_permissions` & `model_has_roles` - Permission assignments

#### Business Entities
- `suppliers` - Supplier companies with approval status
- `buyers` - Healthcare institutions/buyer companies with approval status
- `products` - Medical equipment catalog
- `product_categories` - Hierarchical product categories
- `product_supplier` - Many-to-many pivot table

#### Transaction Flow
- `rfqs` - Request for Quotations from buyers
- `rfq_items` - Items requested in RFQs
- `quotations` - Supplier responses to RFQs
- `quotation_items` - Items quoted by suppliers
- `orders` - Purchase orders
- `order_items` - Items in orders
- `invoices` - Generated invoices
- `payments` - Payment records
- `deliveries` - Delivery tracking

#### System Tables
- `activity_log` - Audit trail
- `media` - Spatie media library
- `notifications` - System notifications
- `sessions`, `cache`, `jobs`, `failed_jobs` - Laravel system tables

### Key Relationships
- User → hasOne → Supplier/Buyer
- Product → belongsToMany → Suppliers (through product_supplier)
- Product → belongsTo → ProductCategory
- Rfq → hasMany → RfqItems
- Quotation → belongsTo → Rfq
- Order → hasMany → OrderItems
- Order → hasOne → Invoice, Payment, Delivery

---

## 🛣 Routing Structure

### Public Routes (`routes/web.php`)
- `GET /` - Homepage (landing page)

### Authentication Routes (`routes/auth.php`)
- Registration, Login, Logout, Password Reset
- Email Verification
- Waiting Approval page for pending users

### Authenticated Routes (Middleware: `auth`)

#### Profile Routes
- `GET /profile` - Edit profile
- `PATCH /profile` - Update profile
- `DELETE /profile` - Delete account

#### Dashboard
- `GET /dashboard` - Main dashboard (role-based view)

#### Admin Routes (`/admin/*`)
**Middleware:** `auth`, `role:Admin`

**User Management:**
- `GET /admin/users` - List all users
- `GET /admin/users/export` - Export users to Excel
- `GET /admin/users/create` - Create user form
- `POST /admin/users` - Store new user
- `GET /admin/users/{user}/edit` - Edit user form
- `PUT /admin/users/{user}` - Update user
- `DELETE /admin/users/{user}` - Delete user

**Supplier Management:**
- `GET /admin/suppliers` - List all suppliers
- `GET /admin/suppliers/export` - Export suppliers to Excel
- `GET /admin/suppliers/create` - Create supplier form
- `POST /admin/suppliers` - Store new supplier
- `GET /admin/suppliers/{supplier}` - View supplier details
- `GET /admin/suppliers/{supplier}/edit` - Edit supplier form
- `PUT /admin/suppliers/{supplier}` - Update supplier
- `DELETE /admin/suppliers/{supplier}` - Delete supplier
- `POST /admin/suppliers/{supplier}/verify` - Verify supplier
- `POST /admin/suppliers/{supplier}/toggle-active` - Toggle supplier active status

**Buyer Management:**
- `GET /admin/buyers` - List all buyers
- `GET /admin/buyers/export` - Export buyers to Excel
- `GET /admin/buyers/create` - Create buyer form
- `POST /admin/buyers` - Store new buyer
- `GET /admin/buyers/{buyer}` - View buyer details
- `GET /admin/buyers/{buyer}/edit` - Edit buyer form
- `PUT /admin/buyers/{buyer}` - Update buyer
- `DELETE /admin/buyers/{buyer}` - Delete buyer
- `POST /admin/buyers/{buyer}/toggle-active` - Toggle buyer active status
- `POST /admin/buyers/{buyer}/verify` - Verify buyer

**Product Management:**
- `GET /admin/products` - List all products
- `GET /admin/products/{product}` - View product details
- `GET /admin/products/{product}/review` - Review product form
- `POST /admin/products/{product}/approve` - Approve product
- `POST /admin/products/{product}/reject` - Reject product
- `POST /admin/products/{product}/request-changes` - Request product changes
- `DELETE /admin/products/{product}` - Delete product

**Product Categories:**
- `GET /admin/categories` - List all categories
- `GET /admin/categories/create` - Create category form
- `POST /admin/categories` - Store new category
- `GET /admin/categories/{category}` - View category details
- `GET /admin/categories/{category}/edit` - Edit category form
- `PUT /admin/categories/{category}` - Update category
- `DELETE /admin/categories/{category}` - Delete category

**Order Management:**
- `GET /admin/orders` - List all orders
- `GET /admin/orders/export` - Export orders to Excel
- `GET /admin/orders/create` - Create order form
- `POST /admin/orders` - Store new order
- `GET /admin/orders/{order}` - View order details
- `GET /admin/orders/{order}/edit` - Edit order form
- `PUT /admin/orders/{order}` - Update order
- `DELETE /admin/orders/{order}` - Delete order

**RFQ Management (Admin Monitoring):**
- `GET /admin/rfqs` - List all RFQs
- `GET /admin/rfqs/create` - Create RFQ form
- `POST /admin/rfqs` - Store new RFQ
- `GET /admin/rfqs/{rfq}` - View RFQ details
- `GET /admin/rfqs/{rfq}/edit` - Edit RFQ form
- `PUT /admin/rfqs/{rfq}` - Update RFQ
- `DELETE /admin/rfqs/{rfq}` - Delete RFQ
- `PATCH /admin/rfqs/{rfq}/status` - Update RFQ status
- `PATCH /admin/rfqs/{rfq}/visibility` - Toggle RFQ visibility
- `POST /admin/rfqs/{rfq}/assign-suppliers` - Assign suppliers to RFQ

**RFQ Items Management:**
- `GET /admin/rfqs/{rfq}/items/create` - Create RFQ item form
- `POST /admin/rfqs/{rfq}/items` - Store new RFQ item
- `GET /admin/rfqs/{rfq}/items/{item}/edit` - Edit RFQ item form
- `PUT /admin/rfqs/{rfq}/items/{item}` - Update RFQ item
- `DELETE /admin/rfqs/{rfq}/items/{item}` - Delete RFQ item

**Quotation Management (Admin Monitoring):**
- `GET /admin/quotations` - List all quotations
- `GET /admin/quotations/export` - Export quotations to Excel
- `GET /admin/quotations/create` - Create quotation form
- `POST /admin/quotations` - Store new quotation
- `GET /admin/quotations/compare` - Compare quotations
- `GET /admin/quotations/{quotation}` - View quotation details
- `GET /admin/quotations/{quotation}/edit` - Edit quotation form
- `PUT /admin/quotations/{quotation}` - Update quotation
- `DELETE /admin/quotations/{quotation}` - Delete quotation
- `POST /admin/quotations/{quotation}/accept` - Accept quotation
- `POST /admin/quotations/{quotation}/reject` - Reject quotation

**Manufacturer Management:**
- `GET /admin/manufacturers` - List all manufacturers
- `GET /admin/manufacturers/create` - Create manufacturer form
- `POST /admin/manufacturers` - Store new manufacturer
- `GET /admin/manufacturers/{manufacturer}` - View manufacturer details
- `GET /admin/manufacturers/{manufacturer}/edit` - Edit manufacturer form
- `PUT /admin/manufacturers/{manufacturer}` - Update manufacturer
- `DELETE /admin/manufacturers/{manufacturer}` - Delete manufacturer

**Role & Permission Management:**
- `GET /admin/roles` - List all roles
- `GET /admin/roles/create` - Create role form
- `POST /admin/roles` - Store new role
- `GET /admin/roles/{role}` - View role details
- `GET /admin/roles/{role}/edit` - Edit role form
- `PUT /admin/roles/{role}` - Update role
- `DELETE /admin/roles/{role}` - Delete role
- `GET /admin/permissions` - List all permissions
- `GET /admin/permissions/{permission}` - View permission details
- `PUT /admin/users/{user}/permissions` - Update user permissions

**Invoices Management:**
- `GET /admin/invoices` - List all invoices
- `GET /admin/invoices/export` - Export invoices to Excel
- `GET /admin/invoices/create` - Create invoice form
- `POST /admin/invoices` - Store new invoice
- `GET /admin/invoices/{invoice}` - View invoice details
- `GET /admin/invoices/{invoice}/edit` - Edit invoice form
- `PUT /admin/invoices/{invoice}` - Update invoice
- `DELETE /admin/invoices/{invoice}` - Delete invoice

**Payments Management:**
- `GET /admin/payments` - List all payments
- `GET /admin/payments/export` - Export payments to Excel
- `GET /admin/payments/create` - Create payment form
- `POST /admin/payments` - Store new payment
- `GET /admin/payments/{payment}` - View payment details
- `GET /admin/payments/{payment}/edit` - Edit payment form
- `PUT /admin/payments/{payment}` - Update payment
- `DELETE /admin/payments/{payment}` - Delete payment

**Deliveries Management:**
- `GET /admin/deliveries` - List all deliveries
- `GET /admin/deliveries/export` - Export deliveries to Excel
- `GET /admin/deliveries/create` - Create delivery form
- `POST /admin/deliveries` - Store new delivery
- `GET /admin/deliveries/{delivery}` - View delivery details
- `GET /admin/deliveries/{delivery}/edit` - Edit delivery form
- `PUT /admin/deliveries/{delivery}` - Update delivery
- `DELETE /admin/deliveries/{delivery}` - Delete delivery

**Other Admin Routes:**
- `GET /admin/reports` - Reporting dashboard
- `GET /admin/activity` - Activity log index
- `GET /admin/activity/{activity}` - View activity log entry
- `GET /admin/registrations/pending` - Pending registrations
- `POST /admin/registrations/{type}/{id}/approve` - Approve registration
- `POST /admin/registrations/{type}/{id}/reject` - Reject registration
- `GET /admin/settings` - System settings
- `POST /admin/settings/general` - Update general settings
- `POST /admin/settings/email` - Update email settings
- `POST /admin/settings/payment` - Update payment settings
- `POST /admin/settings/security` - Update security settings
- `POST /admin/settings/email/test` - Test email connection
- `GET /admin/notifications` - System notifications
- `POST /admin/notifications/{id}/read` - Mark notification as read
- `POST /admin/notifications/read-all` - Mark all notifications as read
- `DELETE /admin/notifications/{id}` - Delete notification
- `DELETE /admin/notifications` - Delete all notifications

#### Supplier Routes (`/supplier/*`)
**Middleware:** `auth`, `role:Supplier`

**Dashboard:**
- `GET /supplier/dashboard` - Supplier dashboard

**Products:**
- `GET /supplier/products` - List supplier products
- `GET /supplier/products/create` - Create product form
- `POST /supplier/products` - Store new product
- `GET /supplier/products/{product}` - View product details
- `GET /supplier/products/{product}/edit` - Edit product form
- `PUT /supplier/products/{product}` - Update product
- `DELETE /supplier/products/{product}` - Delete product

**RFQs & Quotations:**
- `GET /supplier/rfqs` - List assigned RFQs
- `GET /supplier/rfqs/{rfq}` - View RFQ details
- `GET /supplier/rfqs/{rfq}/quote` - Create quotation form
- `POST /supplier/rfqs/{rfq}/quote` - Store quotation
- `GET /supplier/quotations` - List supplier quotations
- `GET /supplier/quotations/export` - Export quotations to Excel
- `GET /supplier/quotations/{quotation}` - View quotation details
- `GET /supplier/quotations/{quotation}/edit` - Edit quotation form
- `PUT /supplier/quotations/{quotation}` - Update quotation
- `DELETE /supplier/quotations/{quotation}` - Delete quotation

**Orders:**
- `GET /supplier/orders` - List supplier orders
- `GET /supplier/orders/export` - Export orders to Excel
- `GET /supplier/orders/{order}` - View order details
- `PATCH /supplier/orders/{order}/status` - Update order status

**Deliveries:**
- `GET /supplier/deliveries` - List deliveries
- `GET /supplier/deliveries/create/{order}` - Create delivery form
- `POST /supplier/deliveries/{order}` - Store delivery
- `GET /supplier/deliveries/{delivery}` - View delivery details
- `PATCH /supplier/deliveries/{delivery}/status` - Update delivery status
- `POST /supplier/deliveries/{delivery}/proof` - Upload delivery proof

**Invoices:**
- `GET /supplier/invoices` - List invoices
- `GET /supplier/invoices/export` - Export invoices to Excel
- `GET /supplier/invoices/{invoice}` - View invoice details
- `GET /supplier/invoices/{invoice}/download` - Download invoice PDF

**Payments:**
- `GET /supplier/payments` - List payments
- `GET /supplier/payments/{payment}` - View payment details

**Reports:**
- `GET /supplier/reports` - Supplier reporting dashboard

**Activity Logs:**
- `GET /supplier/activity` - List supplier activity logs
- `GET /supplier/activity/{activity}` - View activity log entry

**Profile:**
- `GET /supplier/profile` - View supplier profile
- `GET /supplier/profile/edit` - Edit profile form
- `PUT /supplier/profile` - Update profile
- `PUT /supplier/profile/password` - Update password
- `POST /supplier/profile/document` - Upload document
- `DELETE /supplier/profile/document/{mediaId}` - Delete document

**Notifications:**
- `GET /supplier/notifications` - List notifications
- `POST /supplier/notifications/{id}/read` - Mark as read
- `POST /supplier/notifications/read-all` - Mark all as read
- `DELETE /supplier/notifications/{id}` - Delete notification
- `DELETE /supplier/notifications` - Delete all notifications

#### Buyer Routes (`/buyer/*`)
**Middleware:** `auth`, `role:Buyer`

**Dashboard:**
- `GET /buyer/dashboard` - Buyer dashboard

**Orders:**
- `GET /buyer/orders` - List buyer orders
- `GET /buyer/orders/create` - Create order form
- `POST /buyer/orders` - Store new order
- `GET /buyer/orders/{order}` - View order details

**Other:**
- `GET /buyer/favorites` - Favorites list
- `GET /buyer/suppliers` - Supplier directory

---

## 🎨 Frontend Components

### Layouts (`resources/views/layouts/`)

1. **landing.blade.php** - Public landing page layout
2. **app.blade.php** - Authenticated application layout
3. **guest.blade.php** - Guest/authentication pages layout
4. **navigation.blade.php** - Navigation component

### Landing Page Sections (`resources/views/sections/`)

1. **hero.blade.php** - Hero slideshow section
2. **about.blade.php** - About company section
3. **services.blade.php** - Services offered
4. **categories.blade.php** - Product categories showcase
5. **partners.blade.php** - Partners/team section
6. **gallery.blade.php** - Image gallery
7. **faq.blade.php** - Frequently asked questions
8. **contact.blade.php** - Contact form

### Reusable Components (`resources/views/components/`)

**UI Components:**
- `navbar.blade.php` - Main navigation bar
- `footer.blade.php` - Footer component
- `modal.blade.php` - Modal dialogs
- `dropdown.blade.php` - Dropdown menus

**Form Components:**
- `text-input.blade.php` - Text input field
- `input-label.blade.php` - Form labels
- `input-error.blade.php` - Validation errors
- `primary-button.blade.php` - Primary action button
- `secondary-button.blade.php` - Secondary button
- `danger-button.blade.php` - Destructive action button

**Dashboard Components:** (`components/dashboard/`)
- Overview cards, analytics widgets, data tables, etc.

**Section Components:** (`components/sections/`)
- Reusable section templates for landing page

### Admin Views (`resources/views/admin/`)

- **activity/** - Activity logs (index, show)
- **buyers/** - Buyer management (index, create, edit, show)
- **categories/** - Category management (index, create, edit, show)
- **notifications/** - Notifications index
- **orders/** - Order management (index, edit, show)
- **products/** - Product management (index, edit, review, show)
- **quotations/** - Quotation management (index, compare, show)
- **registrations/** - Pending registrations (pending)
- **reports/** - Reporting interfaces (index)
- **rfqs/** - RFQ management (index, show)
- **settings/** - System settings (index)
- **suppliers/** - Supplier management (index, create, edit, show)
- **users/** - User management (index, create, edit)

### Supplier Views (`resources/views/supplier/`)

- **activity/** - Activity logs (index, show)
- **dashboard.blade.php** - Supplier dashboard
- **deliveries/** - Delivery management (index, create, show)
- **invoices/** - Invoice viewing (index, show)
- **notifications/** - Notifications (index)
- **orders/** - Order management (index, show)
- **payments/** - Payment management (index, show)
- **products/** - Product management (index, create, edit, show)
- **profile/** - Profile management (edit, show)
- **quotations/** - Quotation management (index, show)
- **reports/** - Reporting interfaces (index)
- **rfqs/** - RFQ viewing and quotation (index, show, quote, quote-edit)

### Buyer Views (`resources/views/buyer/`)

- **dashboard.blade.php** - Buyer dashboard
- **favorites.blade.php** - Favorites list
- **suppliers.blade.php** - Supplier directory

### Authentication Views (`resources/views/auth/`)

- **confirm-password.blade.php** - Password confirmation
- **forgot-password.blade.php** - Password reset request
- **login.blade.php** - Login form
- **register.blade.php** - Registration form
- **reset-password.blade.php** - Password reset form
- **verify-email.blade.php** - Email verification
- **waiting-approval.blade.php** - Waiting approval page

### Component Views (`resources/views/components/`)

**Admin Components:**
- **admin/review/** - Product review components (block, field, list)

**Dashboard Components:**
- **dashboard/** - Dashboard UI components (activity-list, calendar-card, chart-card, header, layout, quick-actions, sidebar, stat-card, welcome-card)

**Form Components:**
- **text-input.blade.php** - Text input field
- **input-label.blade.php** - Form labels
- **input-error.blade.php** - Validation errors
- **primary-button.blade.php** - Primary action button
- **secondary-button.blade.php** - Secondary button
- **danger-button.blade.php** - Destructive action button

**UI Components:**
- **application-logo.blade.php** - Application logo
- **auth-layout.blade.php** - Authentication layout wrapper
- **auth-session-status.blade.php** - Session status display
- **dropdown.blade.php** - Dropdown menu
- **dropdown-link.blade.php** - Dropdown link item
- **footer.blade.php** - Footer component
- **modal.blade.php** - Modal dialogs
- **nav-link.blade.php** - Navigation link
- **navbar.blade.php** - Main navigation bar
- **responsive-nav-link.blade.php** - Responsive navigation link

**Section Components:**
- **sections/** - Landing page sections (about, categories, contact, faq, gallery, hero, services, team)

---

## 🔐 Authentication & Authorization

### User Types
1. **Admin** - Full system access
2. **Supplier** - Manage products, respond to RFQs, view orders
3. **Buyer** - Create RFQs, place orders, track deliveries

### Approval Workflow
- Suppliers and Buyers require admin approval after registration
- Status: `pending`, `approved`, `rejected`
- Waiting approval page redirects unapproved users

### Permissions (Spatie)
Granular permissions for each resource:
- `view {resource}`, `create {resource}`, `edit {resource}`, `delete {resource}`
- Resources: users, suppliers, buyers, products, orders, activity logs

### Roles
- Roles assigned to user types
- Permissions assigned to roles
- Middleware: `permission:{permission_name}`

---

## ⚙️ Key Features

### 1. Multi-Vendor E-Commerce
- Supplier registration and product catalog management
- Product categorization with hierarchical structure
- Product-supplier relationships (many-to-many)

### 2. RFQ/Quotation System
- Buyers create RFQs with multiple items
- Suppliers submit quotations in response
- Quote comparison and selection

### 3. Order Processing
- Convert quotations to orders
- Order status tracking
- Order item management

### 4. Payment & Invoice System
- Invoice generation
- Payment recording and tracking
- Multiple payment methods support

### 5. Delivery Tracking
- Delivery status management
- Tracking information
- Delivery confirmation

### 6. Activity Logging
- Comprehensive audit trail using Spatie Activity Log
- Track all CRUD operations
- User activity monitoring

### 7. Media Management
- Spatie Media Library integration
- Product images, supplier documents
- Company logos and certificates

### 8. Reporting & Analytics
- Sales reports and analytics
- Order analytics and tracking
- Supplier/buyer performance metrics
- Excel export functionality for data analysis
- Admin and supplier reporting dashboards

### 9. Responsive Design
- Tailwind CSS-based responsive UI
- Mobile-friendly interface
- Modern, clean design with medical theme

### 10. Arabic Language Support
- RTL (Right-to-Left) layout support
- Arabic fonts (Cairo, Tajawal)
- Bilingual interface capability
- Arabic names for roles and permissions

### 11. Authorization Policies
- Comprehensive policy-based authorization
- Resource-level access control
- Role and permission-based restrictions

### 12. Data Export
- Excel export functionality for all major resources
- Admin and supplier-specific exports
- Data analysis and reporting support

---

## 🚀 Development Workflows

### Setup & Installation

```bash
# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate --seed

# Build assets
npm run build
```

### Development

```bash
# Start development server (concurrent)
composer dev
# Runs: Laravel server + Queue worker + Pail logs + Vite dev server

# Or individually:
php artisan serve          # Laravel development server
npm run dev                # Vite development server
php artisan queue:listen   # Queue worker
php artisan pail           # Real-time logs
```

### Testing

```bash
composer test
# Runs: php artisan test
```

**Test Structure:**
- Feature tests: `tests/Feature/`
- Unit tests: `tests/Unit/`
- Test base class: `tests/TestCase.php`

### Code Quality

```bash
# Laravel Pint (code style)
vendor/bin/pint

# IDE Helper
php artisan ide-helper:generate
php artisan ide-helper:models
```

---

## 🧪 Testing

### Test Structure

The application uses PHPUnit for testing with a comprehensive test suite covering authentication, authorization, features, and system workflows.

**Test Base:**
- `tests/TestCase.php` - Base test case class extending Laravel's TestCase

### Feature Tests (`tests/Feature/`)

#### Authentication Tests (`tests/Feature/Auth/`) - 6 Test Files

1. **AuthenticationTest.php** - Login/logout functionality
   - Login screen rendering
   - User authentication
   - Invalid credentials handling
   - Logout functionality

2. **EmailVerificationTest.php** - Email verification workflow
   - Email verification prompt
   - Email verification sending
   - Email verification handling

3. **PasswordConfirmationTest.php** - Password confirmation
   - Password confirmation screen
   - Password confirmation validation

4. **PasswordResetTest.php** - Password reset functionality
   - Password reset link request
   - Password reset form
   - Password reset processing

5. **PasswordUpdateTest.php** - Password update functionality
   - Password update validation
   - Password update processing

6. **RegistrationTest.php** - User registration
   - Registration form rendering
   - Supplier registration
   - Buyer registration
   - Registration validation

#### Authorization Tests (`tests/Feature/Authorization/`) - 1 Test File

1. **PermissionBasedAuthorizationTest.php** - Permission-based access control
   - Role-based access
   - Permission checking
   - Resource authorization

#### Profile Tests (`tests/Feature/`) - 1 Test File

1. **ProfileTest.php** - User profile management
   - Profile viewing
   - Profile updates
   - Profile validation

#### Supplier Tests (`tests/Feature/Suppliers/`) - 1 Test File

1. **SupplierPaymentControllerTest.php** - Supplier payment functionality
   - Payment viewing
   - Payment management

#### System Tests (`tests/Feature/System/`) - 3 Test Files

1. **CompleteProcurementWorkflowTest.php** - End-to-end procurement workflow
   - RFQ creation → Quotation → Order → Invoice → Payment → Delivery
   - Complete business process validation

2. **DataIntegrityTest.php** - Data integrity and relationships
   - Model relationships
   - Data consistency
   - Foreign key constraints

3. **SystemIntegrationTest.php** - System integration testing
   - Component integration
   - Service interactions
   - System-wide functionality

4. **UserRegistrationApprovalWorkflowTest.php** - Registration approval process
   - Pending user workflow
   - Approval/rejection process
   - Status transitions

#### Other Feature Tests

- **ExampleTest.php** - Example feature test template

### Unit Tests (`tests/Unit/`) - 1 Test File

1. **ExampleTest.php** - Example unit test template

### Additional Test Files

**Root Level Test Files:**
- `app_improvements_test.php` - Application improvements validation
- `auth_registration_test.php` - Authentication and registration tests
- `database_improvements_test.php` - Database improvements validation
- `model_consistency_test.php` - Model consistency checks
- `product_categories_test.php` - Product categories functionality

### Test Coverage

The test suite covers:
- ✅ Authentication and authorization workflows
- ✅ User registration and approval processes
- ✅ Complete procurement workflow (RFQ → Delivery)
- ✅ Data integrity and relationships
- ✅ System integration
- ✅ Supplier-specific functionality
- ✅ Profile management

### Running Tests

```bash
# Run all tests
composer test
# or
php artisan test

# Run specific test file
php artisan test tests/Feature/Auth/AuthenticationTest.php

# Run with coverage
php artisan test --coverage

# Run specific test method
php artisan test --filter test_login_screen_can_be_rendered
```

---

## 🎨 Design System (Tailwind)

### Color Palette

**Medical Blue** (Primary)
- `medical-blue-500`: #0069af (Brand color)
- Range: 50-900

**Medical Green** (Secondary)
- `medical-green-500`: #199b69
- Range: 50-900

**Medical Red** (Alerts)
- `medical-red-500`: #ef4444
- Range: 50-900

**Medical Gray** (Neutral)
- `medical-gray-500`: #6b7280
- Range: 50-900

### Typography
- **Display:** Poppins, Tajawal
- **Sans:** Inter, Cairo
- **Arabic:** Cairo, Tajawal

### Custom Animations
- `fade-in`, `fade-in-up`, `fade-in-down`
- `slide-in-right`, `slide-in-left`
- `scale-in`, `pulse-slow`

### Shadows
- `shadow-medical`, `shadow-medical-lg`
- `shadow-medical-xl`, `shadow-medical-2xl`

---

## 📝 Documentation Files

The project includes extensive documentation:

- `README.md` - Standard Laravel readme
- `QUICK_START.md` - Quick start guide
- `APP_REVIEW_REPORT.md` - Application review
- `APP_IMPROVEMENTS_COMPLETED.md` - Completed improvements log
- `AUTH_REDESIGN_IMPLEMENTATION.md` - Authentication redesign documentation
- `DATABASE_IMPROVEMENT_PLAN.md` - Database improvement plans
- `DEPLOYMENT_CHECKLIST.md` - Deployment checklist
- `LANDING_PAGE_DOCUMENTATION.md` - Landing page documentation
- `PRODUCT_CATEGORIES_IMPLEMENTATION.md` - Categories system documentation
- `REFACTORING_SUMMARY.md` - Code refactoring summary

---

## 🔑 Key Conventions

### Naming Conventions
- **Controllers:** Singular resource name + `Controller` (e.g., `ProductController`)
- **Models:** Singular (e.g., `Product`, `OrderItem`)
- **Tables:** Plural snake_case (e.g., `products`, `order_items`)
- **Routes:** Plural kebab-case (e.g., `/admin/suppliers`)
- **Views:** Snake_case (e.g., `create.blade.php`)

### Code Organization
- Controllers handle HTTP requests, delegate to services
- Services contain business logic
- Models contain relationships and accessors
- Form Requests handle validation
- Traits for shared functionality
- View Composers for shared view data

### Migration Naming
- Format: `YYYY_MM_DD_HHMMSS_create_table_name_table.php`
- Chronological ordering: All base migrations start with `2025_10_31`

---

## 🎯 Current Project State

### ✅ Completed Features
- Laravel 12 upgrade complete
- Tailwind CSS migration from Bootstrap
- Authentication system with approval workflow
- User, Supplier, Buyer management
- Product catalog with categories
- Basic RFQ/Quotation flow
- Order, Invoice, Payment, Delivery models
- Activity logging
- Responsive landing page with modern design
- Admin panel with role-based access

### 🚧 In Progress / Future Enhancements
- Complete supplier and buyer dashboards
- Advanced reporting and analytics
- API development for mobile apps
- Real-time notifications
- Email templates and automated emails
- Advanced search and filtering
- Product reviews and ratings
- Wishlist functionality
- Multi-language support (full i18n)

---

## 📞 Support & Resources

- **Laravel Documentation:** https://laravel.com/docs/12.x
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Spatie Packages:** https://spatie.be/open-source

---

## 📊 Codebase Statistics

- **Total Controllers:** 46
  - Web Controllers: 26
  - Supplier Controllers: 11
  - Auth Controllers: 9
  - Base Controller: 1

- **Total Models:** 21
- **Total Services:** 2
- **Total Traits:** 1
- **Total Filters:** 1
- **Total Policies:** 17
- **Total Exports:** 11
- **Total Form Requests:** 21
- **Total Middleware:** 2 custom
- **Total Migrations:** 35
- **Total Seeders:** 8
- **Total Views:** 150+ Blade templates
- **Total Tests:** 21 test files
  - Feature Tests: 14 files
  - Unit Tests: 1 file
  - Root Level Tests: 6 files

## 🔍 Quick Reference

### Finding Files by Purpose

**Authentication:**
- Controllers: `app/Http/Controllers/Auth/`
- Views: `resources/views/auth/`
- Routes: `routes/auth.php`

**Admin Panel:**
- Controllers: `app/Http/Controllers/Web/` (Admin* controllers)
- Views: `resources/views/admin/`
- Routes: `routes/web.php` (prefix: `/admin`)

**Supplier Features:**
- Controllers: `app/Http/Controllers/Web/Suppliers/`
- Views: `resources/views/supplier/`
- Routes: `routes/web.php` (prefix: `/supplier`)

**Buyer Features:**
- Controllers: `app/Http/Controllers/Web/Buyer*`
- Views: `resources/views/buyer/`
- Routes: `routes/web.php` (prefix: `/buyer`)

**Business Logic:**
- Services: `app/Services/`
- Traits: `app/Traits/`
- Filters: `app/Filters/`

**Authorization:**
- Policies: `app/Policies/`

**Data Export:**
- Exports: `app/Exports/`

**Validation:**
- Form Requests: `app/Http/Requests/`

**Database:**
- Migrations: `database/migrations/`
- Seeders: `database/seeders/`
- Models: `app/Models/`

---

*This index was last updated on 2026-01-01 and should be updated as the codebase evolves.*
