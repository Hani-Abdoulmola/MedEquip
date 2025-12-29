# MedEquip Codebase Index

**Last Updated:** 2025-01-27  
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
│   ├── Filters/              # Query filters
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/         # Authentication controllers (9 files)
│   │   │   └── Web/          # Main application controllers (13 files)
│   │   └── Requests/         # Form request validation (15 files)
│   ├── Models/               # Eloquent models (17 models)
│   ├── Notifications/        # Custom notifications
│   ├── Providers/            # Service providers
│   ├── Services/             # Business logic services (2 services)
│   ├── Traits/               # Reusable traits
│   └── View/                 # View composers
│
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/           # Database migrations (33 files)
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
│   ├── seeders/              # Database seeders (7 files)
│   │   ├── AdminSeeder.php - Create admin user
│   │   ├── DatabaseSeeder.php - Main seeder
│   │   ├── ManufacturerSeeder.php - Seed manufacturers
│   │   ├── ProductCategorySeeder.php - Seed product categories
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

#### Web Controllers (`app/Http/Controllers/Web/`) - 20 Controllers

**Admin Controllers:**
1. **ActivityLogController.php** - Activity logging and audit trails viewing
2. **AdminQuotationController.php** - Admin monitoring and management of quotations
3. **AdminRfqController.php** - Admin monitoring and management of RFQs
4. **BuyerController.php** - Buyer management (CRUD operations)
5. **BuyerDashboardController.php** - Buyer dashboard overview
6. **NotificationController.php** - System notifications management
7. **OrderController.php** - Order processing and management
8. **ProductCategoryController.php** - Product category management
9. **ProductController.php** - Product catalog management
10. **ProductReviewController.php** - Product review and approval workflow
11. **RegistrationApprovalController.php** - Approve/reject supplier/buyer registrations
12. **SettingController.php** - System settings management
13. **SupplierController.php** - Supplier management (CRUD operations)
14. **UserController.php** - User administration

**General Controllers:**
15. **DeliveryController.php** - Delivery tracking and management
16. **InvoiceController.php** - Invoice generation and management
17. **PaymentController.php** - Payment processing
18. **ProfileController.php** - User profile management
19. **QuotationController.php** - Quotation creation and management
20. **RfqController.php** - Request for Quotation management

#### Supplier Controllers (`app/Http/Controllers/Web/Suppliers/`) - 9 Controllers

1. **SupplierDashboardController.php** - Supplier dashboard overview
2. **SupplierDeliveryController.php** - Supplier delivery management
3. **SupplierInvoiceController.php** - Supplier invoice viewing
4. **SupplierNotificationController.php** - Supplier notifications
5. **SupplierOrderController.php** - Supplier order management
6. **SupplierProductController.php** - Supplier product catalog management
7. **SupplierProfileController.php** - Supplier profile management
8. **SupplierRfqController.php** - Supplier RFQ viewing and quotation creation

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

**Total Controllers: 39**

### Models (`app/Models/`) - 19 Models

**User Management:**
1. **User.php** - User accounts (admins, suppliers, buyers) with relationships
2. **UserType.php** - User role types (Admin, Supplier, Buyer)

**Business Entities:**
3. **Supplier.php** - Supplier entities with approval workflow
4. **Buyer.php** - Buyer/healthcare institution entities with approval workflow
5. **Product.php** - Medical equipment products with categories and suppliers
6. **ProductCategory.php** - Product categorization with hierarchical structure
7. **ProductSupplier.php** - Pivot model for product-supplier relationships
8. **Manufacturer.php** - Product manufacturer information

**Transaction Flow:**
9. **Rfq.php** - Request for Quotations from buyers
10. **RfqItem.php** - Individual items in RFQs
11. **Quotation.php** - Supplier quotations in response to RFQs
12. **QuotationItem.php** - Individual items in quotations
13. **Order.php** - Purchase orders
14. **OrderItem.php** - Individual items in orders
15. **Invoice.php** - Generated invoices
16. **Payment.php** - Payment records
17. **Delivery.php** - Delivery tracking

**System:**
18. **ActivityLog.php** - Activity log entries (Spatie Activity Log)
19. **Setting.php** - System settings configuration

### Services (`app/Services/`) - 2 Services

1. **NotificationService.php** - Centralized notification handling and dispatch
2. **ReferenceCodeService.php** - Generate unique reference codes for entities (RFQs, Orders, Invoices, etc.)

### Traits (`app/Traits/`) - 1 Trait

1. **Auditable.php** - Trait for models that need activity logging

### Filters (`app/Filters/`) - 1 Filter

1. **ActivityLogFilter.php** - Query filtering for activity logs

### Form Requests (`app/Http/Requests/`) - 17 Requests

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

**Other:**
16. **FileRequest.php** - File upload validation
17. **ProfileUpdateRequest.php** - Profile update validation

### Middleware (`app/Http/Middleware/`) - 1 Custom Middleware

1. **EnsureUserIsVerified.php** - Ensures user email is verified

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
- `GET /admin/users/create` - Create user form
- `POST /admin/users` - Store new user
- `GET /admin/users/{user}/edit` - Edit user form
- `PUT /admin/users/{user}` - Update user
- `DELETE /admin/users/{user}` - Delete user

**Supplier Management:**
- `GET /admin/suppliers` - List all suppliers
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
- `GET /admin/orders/create` - Create order form
- `POST /admin/orders` - Store new order
- `GET /admin/orders/{order}` - View order details
- `GET /admin/orders/{order}/edit` - Edit order form
- `PUT /admin/orders/{order}` - Update order
- `DELETE /admin/orders/{order}` - Delete order

**RFQ Management (Admin Monitoring):**
- `GET /admin/rfqs` - List all RFQs
- `GET /admin/rfqs/{rfq}` - View RFQ details
- `PATCH /admin/rfqs/{rfq}/status` - Update RFQ status
- `PATCH /admin/rfqs/{rfq}/visibility` - Toggle RFQ visibility
- `POST /admin/rfqs/{rfq}/assign-suppliers` - Assign suppliers to RFQ

**Quotation Management (Admin Monitoring):**
- `GET /admin/quotations` - List all quotations
- `GET /admin/quotations/compare` - Compare quotations
- `GET /admin/quotations/{quotation}` - View quotation details
- `POST /admin/quotations/{quotation}/accept` - Accept quotation
- `POST /admin/quotations/{quotation}/reject` - Reject quotation

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
- `GET /supplier/quotations/{quotation}/edit` - Edit quotation form
- `PUT /supplier/quotations/{quotation}` - Update quotation
- `DELETE /supplier/quotations/{quotation}` - Delete quotation

**Orders:**
- `GET /supplier/orders` - List supplier orders
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
- `GET /supplier/invoices/{invoice}` - View invoice details

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

- `GET /buyer/dashboard` - Buyer dashboard
- `GET /buyer/orders` - List buyer orders
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

- **dashboard.blade.php** - Supplier dashboard
- **deliveries/** - Delivery management (index, create, show)
- **invoices/** - Invoice viewing (index, show)
- **notifications/** - Notifications (index)
- **orders/** - Order management (index, show)
- **products/** - Product management (index, create, edit, show)
- **profile/** - Profile management (edit, show)
- **quotations/** - Quotation management (index)
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

### 8. Reporting
- Sales reports
- Order analytics
- Supplier/buyer performance metrics

### 9. Responsive Design
- Tailwind CSS-based responsive UI
- Mobile-friendly interface
- Modern, clean design with medical theme

### 10. Arabic Language Support
- RTL (Right-to-Left) layout support
- Arabic fonts (Cairo, Tajawal)
- Bilingual interface capability

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

### Code Quality

```bash
# Laravel Pint (code style)
vendor/bin/pint

# IDE Helper
php artisan ide-helper:generate
php artisan ide-helper:models
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

- **Total Controllers:** 39
  - Web Controllers: 20
  - Supplier Controllers: 9
  - Auth Controllers: 9
  - Base Controller: 1

- **Total Models:** 19
- **Total Services:** 2
- **Total Traits:** 1
- **Total Filters:** 1
- **Total Form Requests:** 17
- **Total Middleware:** 1 custom
- **Total Migrations:** 33
- **Total Seeders:** 7
- **Total Views:** 150+ Blade templates

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

**Validation:**
- Form Requests: `app/Http/Requests/`

**Database:**
- Migrations: `database/migrations/`
- Seeders: `database/seeders/`
- Models: `app/Models/`

---

*This index was last updated on 2025-01-27 and should be updated as the codebase evolves.*
