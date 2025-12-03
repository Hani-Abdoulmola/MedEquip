# MedEquip Codebase Index

**Last Updated:** 2025-11-26  
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
│   ├── migrations/           # Database migrations (28 files)
│   ├── seeders/              # Database seeders (4 files)
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

#### Web Controllers (`app/Http/Controllers/Web/`)
1. **ActivityLogController.php** - Activity logging and audit trails
2. **BuyerController.php** - Buyer management (CRUD operations)
3. **DeliveryController.php** - Delivery tracking and management
4. **InvoiceController.php** - Invoice generation and management
5. **OrderController.php** - Order processing and management
6. **PaymentController.php** - Payment processing
7. **ProductController.php** - Product catalog management
8. **ProfileController.php** - User profile management
9. **QuotationController.php** - Quotation creation and management
10. **RegistrationApprovalController.php** - Approve/reject supplier/buyer registrations
11. **RfqController.php** - Request for Quotation management
12. **SupplierController.php** - Supplier management (CRUD operations)
13. **UserController.php** - User administration

#### Auth Controllers (`app/Http/Controllers/Auth/`)
Standard Laravel Breeze authentication controllers for login, registration, password reset, etc.

### Models (`app/Models/`)

1. **User.php** - User accounts (admins, suppliers, buyers)
2. **UserType.php** - User role types
3. **Supplier.php** - Supplier entities
4. **Buyer.php** - Buyer/healthcare institution entities
5. **Product.php** - Medical equipment products
6. **ProductCategory.php** - Product categorization with hierarchical structure
7. **ProductSupplier.php** - Pivot table for product-supplier relationships
8. **Rfq.php** - Request for Quotations
9. **RfqItem.php** - Individual items in RFQs
10. **Quotation.php** - Supplier quotations
11. **QuotationItem.php** - Individual items in quotations
12. **Order.php** - Purchase orders
13. **OrderItem.php** - Individual items in orders
14. **Invoice.php** - Invoices
15. **Payment.php** - Payment records
16. **Delivery.php** - Delivery tracking
17. **ActivityLog.php** - Activity log entries

### Services (`app/Services/`)

1. **NotificationService.php** - Centralized notification handling
2. **ReferenceCodeService.php** - Generate unique reference codes for entities

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
**Permissions:** Various granular permissions for each resource

- **Users:** CRUD operations for user management
- **Suppliers:** CRUD operations with approval workflow
- **Buyers:** CRUD operations with approval workflow
- **Products:** CRUD operations for product catalog
- **Orders:** CRUD operations for order management
- **Reports:** Reporting dashboard
- **Activity:** Activity log viewing
- **Registrations:** Approve/reject pending supplier/buyer registrations
- **Settings:** System settings

#### Supplier Routes (`/supplier/*`) - Placeholder
- Products, Orders, Sales, Profile

#### Buyer Routes (`/buyer/*`) - Placeholder
- Orders, Favorites, Suppliers

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

- **activity/** - Activity logs
- **buyers/** - Buyer management (index, create, edit, show)
- **orders/** - Order management
- **products/** - Product management
- **registrations/** - Pending registrations
- **reports/** - Reporting interfaces
- **settings/** - System settings
- **suppliers/** - Supplier management (index, create, edit, show, documents)
- **users/** - User management (index, create, edit, show, permissions)

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

*This index was automatically generated on 2025-11-26 and should be updated as the codebase evolves.*
