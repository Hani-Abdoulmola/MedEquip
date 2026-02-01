# MedEquip - B2B Medical Equipment Platform

**Version:** 1.0.0  
**Last Updated:** January 24, 2026  
**Status:** ✅ Production Ready  
**Framework:** Laravel 12.x | **PHP:** 8.2+ | **Database:** SQLite/MySQL/PostgreSQL

---

## 📋 Table of Contents

- [About MedEquip](#about-medequip)
- [Project Goals](#project-goals)
- [Key Features](#key-features)
- [Technology Stack](#technology-stack)
- [Project Structure](#project-structure)
- [Installation & Setup](#installation--setup)
- [Usage Examples](#usage-examples)
- [Dependencies & Requirements](#dependencies--requirements)
- [Development Guide](#development-guide)
- [Database Architecture](#database-architecture)
- [Authentication & Authorization](#authentication--authorization)
- [Deployment](#deployment)
- [Testing](#testing)
- [Contributing](#contributing)
- [Additional Resources](#additional-resources)
- [License](#license)

---

## 🎯 About MedEquip

**MedEquip** (also known as MediTrust) is a comprehensive B2B (Business-to-Business) medical equipment e-commerce platform designed to connect medical equipment suppliers with healthcare institutions across the Arab world, with a primary focus on the Libyan market.

### What Problem Does It Solve?

The medical equipment procurement process has traditionally been fragmented, time-consuming, and inefficient. Healthcare institutions often struggle to:

- **Find reliable suppliers** for medical equipment
- **Compare prices and specifications** across multiple vendors
- **Request and manage quotations** efficiently
- **Track orders and deliveries** transparently
- **Manage payments and invoices** systematically

MedEquip solves these challenges by providing a centralized digital platform that streamlines the entire procurement workflow from initial product discovery to final delivery and payment.

### Target Users

1. **Healthcare Institutions (Buyers)**
   - Hospitals, clinics, medical centers
   - Laboratories and diagnostic centers
   - Pharmacies requiring medical equipment
   - Healthcare administrators and procurement officers

2. **Medical Equipment Suppliers**
   - Manufacturers of medical devices
   - Distributors and importers
   - Regional suppliers serving the Arab market
   - Equipment resellers

3. **System Administrators**
   - Platform managers and moderators
   - Staff users with specific permissions
   - System oversight and maintenance personnel

---

## 🎯 Project Goals

### Primary Objectives

1. **Streamline Procurement Process**
   - Reduce time from product discovery to order placement by 70-90%
   - Eliminate manual quotation management
   - Automate order processing and tracking

2. **Enable Competitive Pricing**
   - Allow buyers to request quotations from multiple suppliers
   - Facilitate transparent price comparison
   - Support competitive bidding through RFQ system

3. **Ensure Transparency & Accountability**
   - Complete audit trail of all transactions
   - Activity logging for compliance
   - Transparent order and delivery tracking

4. **Support Business Growth**
   - Help suppliers reach more buyers
   - Enable buyers to discover new suppliers
   - Facilitate repeat business through saved templates and re-ordering

5. **Maintain Data Integrity**
   - Precise financial calculations (decimal precision)
   - Protected financial records (RESTRICT on delete)
   - Comprehensive validation and error handling

---

## ✨ Key Features

### 1. Multi-User Role System

The platform supports three distinct user types with role-based access control:

- **Admin** - Full system access and management capabilities
- **Supplier** - Product management, quotation submission, order fulfillment
- **Buyer** - RFQ creation, quotation evaluation, order placement

**Advanced RBAC Features:**
- Granular permission system using Spatie Laravel Permission
- Staff users with customizable permissions
- Permission templates for common roles
- Frontend and backend permission enforcement

### 2. Product Management

- **Comprehensive Product Catalog**
  - Hierarchical product categories (unlimited nesting)
  - Product-supplier relationships (many-to-many)
  - Manufacturer information
  - Product images via Spatie Media Library
  - Auto-slug generation for SEO-friendly URLs

- **Advanced Product Features**
  - Stock status tracking
  - Lead time management
  - Price history tracking
  - Product reviews and ratings
  - Favorites/wishlist functionality

### 3. RFQ/Quotation Workflow

**Complete Request-for-Quotation System:**

```
Buyer Creates RFQ → Suppliers View RFQ → Suppliers Submit Quotations 
→ Buyer Compares Quotes → Buyer Accepts Quote → Order Created Automatically
```

**Key Capabilities:**
- Multi-item RFQ creation
- RFQ templates for recurring orders
- CSV bulk import for large RFQs
- Smart supplier matching (AI-powered recommendations)
- Budget estimation tools
- Deadline suggestions
- Quotation scoring system (multi-factor analysis)
- Side-by-side quotation comparison

### 4. Order Processing

- **Automated Order Creation** from accepted quotations
- **Order Status Tracking:**
  - Pending → Processing → Shipped → Delivered → Cancelled
- **Order Management:**
  - Order items with quantity, pricing, and tax
  - Unique reference code generation (ORD-YYYYMMDD-XXXXXX)
  - Order history and analytics
  - Re-order functionality (1-click repeat orders)
  - Order cancellation workflow

### 5. Financial Management

- **Invoice Generation**
  - Auto-generated from orders
  - PDF export capability
  - Professional invoice templates

- **Payment Tracking**
  - Multiple payment methods (bank transfer, credit card, cash, check)
  - Payment auto-sync (automatically syncs buyer_id and supplier_id from order)
  - Payment status tracking
  - Payment history

- **Financial Precision**
  - All financial columns use `decimal(15,2)` to prevent floating-point errors
  - Currency support (LYD, USD, EUR)
  - Protected financial records (RESTRICT on delete to prevent data loss)

### 6. Delivery Management

- Delivery tracking and status updates
- Tracking numbers and delivery confirmation
- Delivery items management
- Delivery calendar view
- Delivery disputes system
- Delivery proof upload

### 7. Shopping Cart & RFQ Builder

- **Database-Backed Cart** (persists across sessions)
- **Cart Features:**
  - Add/remove/update items
  - Supplier selection per item
  - Cart expiration (30 days)
  - Abandoned cart recovery emails
  - Convert cart to RFQ

- **Smart RFQ Builder:**
  - Save RFQ as template
  - Load templates for quick creation
  - Duplicate existing RFQs
  - CSV bulk import
  - Budget estimation
  - Supplier suggestions

### 8. Activity Logging & Audit Trails

- Comprehensive audit logging using Spatie Activity Log
- Track all CRUD operations
- User activity monitoring
- Filterable activity logs (by user, date, event, model)
- Permission audit trail

### 9. Registration & Approval System

- Dual user type registration (Buyers & Suppliers)
- Admin approval workflow for new accounts
- Status tracking (pending → approved → rejected)
- Email notifications for status changes
- "Waiting Approval" page for pending users
- Account verification system

### 10. User Experience Enhancements

- **Product Discovery:**
  - Advanced filtering (stock, lead time, supplier rating, price range)
  - Multiple sorting options
  - Product comparison tool
  - Favorites management

- **Notifications:**
  - Real-time notifications
  - Email notifications
  - Notification preferences

- **Reports & Analytics:**
  - Buyer reports (order history, spending analytics)
  - Supplier reports (performance metrics, sales analytics)
  - Admin reports (system-wide analytics)

### 11. Responsive Design

- Mobile-first approach
- Tailwind CSS medical theme
- Professional color palette (Medical Blue, Green, Gray)
- Smooth animations and transitions
- RTL support for Arabic content
- Accessible design patterns

### 12. Landing Page

- Hero slideshow with medical imagery
- About section
- Services showcase
- Product categories display
- Partners section
- Image gallery
- FAQ accordion
- Contact form with map integration

---

## 🛠 Technology Stack

### Backend

- **Framework:** Laravel 12.x
- **PHP Version:** 8.2 or higher
- **Database:** SQLite (development), MySQL/PostgreSQL (production)
- **Authentication:** Laravel Sanctum & Breeze
- **Authorization:** Spatie Laravel Permission (roles & permissions)

### Frontend

- **CSS Framework:** Tailwind CSS 3.x
- **JavaScript:** Alpine.js 3.4.2
- **Build Tool:** Vite 7.x
- **Templating:** Blade (Laravel)
- **Fonts:** Inter, Poppins, Cairo (Arabic support)

### Key Packages

```json
{
    "spatie/laravel-permission": "^6.22",      // Roles & Permissions
    "spatie/laravel-medialibrary": "^11.17",   // File Management
    "spatie/laravel-activitylog": "^4.10",     // Audit Trails
    "spatie/laravel-query-builder": "^6.3",    // API Query Building
    "barryvdh/laravel-dompdf": "^3.1",         // PDF Generation
    "intervention/image": "^3.11",             // Image Processing
    "maatwebsite/excel": "^3.1"                // Excel Import/Export
}
```

### Development Tools

- **Debugbar:** Laravel Debugbar
- **IDE Helper:** Laravel IDE Helper
- **Code Style:** Laravel Pint (PSR-12)
- **Testing:** PHPUnit
- **Logging:** Laravel Pail (real-time logs)

---

## 📁 Project Structure

```
MedEquip/
├── app/
│   ├── Console/              # Artisan commands
│   │   └── Commands/         # Custom commands (abandoned cart, price alerts, etc.)
│   ├── Exports/              # Excel export classes
│   ├── Filters/              # Query filters (ActivityLogFilter)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Auth/         # Authentication controllers (9 controllers)
│   │   │   ├── Api/          # API controllers
│   │   │   └── Web/          # Application controllers
│   │   │       ├── Buyers/   # Buyer-specific controllers
│   │   │       └── Suppliers/ # Supplier-specific controllers
│   │   ├── Middleware/       # Custom middleware
│   │   └── Requests/         # Form validation (20+ request classes)
│   ├── Mail/                 # Mailable classes
│   ├── Models/               # Eloquent models (35+ models)
│   ├── Notifications/        # Custom notifications
│   ├── Observers/            # Model observers
│   ├── Policies/             # Authorization policies
│   ├── Providers/            # Service providers
│   ├── Services/             # Business logic services (18 services)
│   ├── Traits/               # Reusable traits (Auditable)
│   └── View/                 # View composers
│
├── database/
│   ├── factories/            # Model factories
│   ├── migrations/          # Database migrations (50+ migrations)
│   ├── seeders/              # Database seeders
│   └── database.sqlite      # SQLite database (development)
│
├── resources/
│   ├── css/                  # Custom CSS
│   ├── js/                   # JavaScript files
│   └── views/
│       ├── admin/            # Admin panel views
│       ├── auth/             # Authentication pages
│       ├── buyer/            # Buyer interface views
│       ├── components/       # Blade components (33+ components)
│       ├── dashboards/       # Role-based dashboards
│       ├── emails/           # Email templates
│       ├── errors/           # Error pages
│       ├── layouts/          # Layout templates
│       ├── sections/         # Landing page sections
│       └── supplier/         # Supplier interface views
│
├── routes/
│   ├── auth.php              # Authentication routes
│   ├── console.php          # Console routes
│   └── web.php               # Web application routes (498 lines)
│
├── public/
│   ├── assets/               # Static assets
│   └── build/                # Vite build output
│
├── config/                   # Configuration files (20+ files)
├── storage/                  # File storage & logs
├── tests/                    # Test suites (27 test files)
└── vendor/                   # Composer dependencies
```

### Key Components

**Models (35+):**
- User management: `User`, `UserType`, `Role`, `Permission`
- Business entities: `Supplier`, `Buyer`, `Product`, `ProductCategory`, `Manufacturer`
- Transaction flow: `Rfq`, `RfqItem`, `Quotation`, `QuotationItem`, `Order`, `OrderItem`
- Financial: `Invoice`, `Payment`
- Delivery: `Delivery`, `DeliveryTracking`, `DeliveryDispute`
- System: `ActivityLog`, `Setting`, `Notification`

**Controllers (46+):**
- Admin controllers: User, Supplier, Buyer, Product, Order, RFQ, Quotation management
- Buyer controllers: Product browsing, Cart, RFQ, Order, Invoice, Delivery tracking
- Supplier controllers: Product management, RFQ viewing, Quotation submission, Order fulfillment

**Services (18):**
- `RfqWorkflowService` - RFQ state management
- `QuotationWorkflowService` - Quotation state management
- `RfqBuilderService` - RFQ creation logic
- `RfqImportService` - CSV import functionality
- `SupplierSuggestionService` - AI supplier matching
- `BuyerProductService` - Product browsing logic
- `BuyerOrderService` - Order processing
- `BuyerAlertService` - Price and stock alerts
- `ReferenceCodeService` - Unique code generation
- `NotificationService` - Centralized notifications
- And more...

---

## 🚀 Installation & Setup

### Prerequisites

Before you begin, ensure you have the following installed:

- **PHP** 8.2 or higher
- **Composer** (PHP package manager)
- **Node.js** 18+ and **npm**
- **Database:** SQLite (for development) or MySQL/PostgreSQL (for production)
- **Git** (for version control)

### Step 1: Clone the Repository

```bash
git clone <repository-url>
cd MedEquip
```

### Step 2: Install PHP Dependencies

```bash
composer install
```

### Step 3: Install JavaScript Dependencies

```bash
npm install
```

### Step 4: Environment Configuration

```bash
# Copy the example environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

**Configure your `.env` file:**

```env
APP_NAME=MedEquip
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database Configuration (SQLite for development)
DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database/database.sqlite

# Or MySQL/PostgreSQL for production
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=medequip
# DB_USERNAME=root
# DB_PASSWORD=

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"

# Default Currency
DEFAULT_CURRENCY=LYD
```

### Step 5: Database Setup

```bash
# Create SQLite database (if using SQLite)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Seed the database with initial data
php artisan db:seed
```

**Available Seeders:**
- `UserTypeSeeder` - Creates user types (Admin, Supplier, Buyer)
- `UnifiedRolePermissionSeeder` - Creates roles and permissions
- `ProductCatalogSeeder` - Seeds product catalog (optional)

### Step 6: Build Frontend Assets

```bash
# Development build (with hot reload)
npm run dev

# Production build
npm run build
```

### Step 7: Create Storage Link

Uploaded files (product images, documents, etc.) are stored in `storage/app/public`. Laravel serves them via a **symbolic link** from `public/storage` to that folder. Create it with:

```bash
php artisan storage:link
```

**On Windows:** This command often fails because creating symlinks requires Administrator rights. If product images (or other uploads) do not appear after pull/clone on Windows:

1. Open **Command Prompt** or **PowerShell as Administrator** (right‑click → Run as administrator).
2. Go to your project root: `cd C:\path\to\MedEquip` (use your real path).
3. Create the link using Windows’ built-in command:
   ```cmd
   mklink /D "public\storage" "storage\app\public"
   ```
   If `public\storage` already exists as an empty folder, remove it first: `rmdir public\storage`, then run `mklink` again.

After the link exists, URLs like `/storage/1/...` will resolve correctly and images will display. This is the usual reason uploads work on macOS but not on Windows after pulling the project.

### Step 8: Start Development Server

```bash
# Option 1: Simple server
php artisan serve

# Option 2: Full development environment (recommended)
composer dev
# This runs: Laravel server + Queue worker + Logs + Vite dev server
```

### Step 9: Access the Application

Open your browser and navigate to:
- **Application:** http://localhost:8000
- **Default Admin Credentials:**
  - Email: `admin@medequip.ly`
  - Password: `password`

---

## 💡 Usage Examples

### Example 1: Buyer Creates an RFQ

```php
// 1. Buyer browses products and adds to cart
POST /buyer/cart/add/{product}
{
    "quantity": 10,
    "supplier_id": 1
}

// 2. Buyer creates RFQ from cart
POST /buyer/cart/submit-rfq
{
    "title": "Medical Equipment for Hospital",
    "deadline": "2026-02-15",
    "notes": "Urgent requirement"
}

// 3. System creates RFQ and notifies suppliers
// Suppliers can now view and submit quotations
```

### Example 2: Supplier Submits Quotation

```php
// 1. Supplier views available RFQs
GET /supplier/rfqs

// 2. Supplier creates quotation for an RFQ
POST /supplier/rfqs/{rfq}/quote
{
    "items": [
        {
            "rfq_item_id": 1,
            "unit_price": 1500.00,
            "lead_time_days": 14,
            "notes": "In stock, ready to ship"
        }
    ],
    "valid_until": "2026-02-10",
    "terms": "Payment: 30% advance, 70% on delivery"
}

// 3. Buyer receives notification and can compare quotations
```

### Example 3: Buyer Accepts Quotation (Creates Order)

```php
// 1. Buyer compares quotations
GET /buyer/quotations/compare?rfq_id=1

// 2. Buyer accepts best quotation
POST /buyer/quotations/{quotation}/accept

// 3. System automatically:
//    - Creates order from quotation
//    - Generates invoice
//    - Sends confirmation emails
//    - Updates RFQ status
```

### Example 4: Using RFQ Templates

```php
// 1. Save an RFQ as template
POST /buyer/rfqs/{rfq}/save-as-template
{
    "name": "Monthly Hospital Supplies",
    "category": "recurring",
    "department": "Procurement"
}

// 2. Create new RFQ from template
POST /buyer/rfq-templates/{template}/use
{
    "title": "February 2026 Supplies",
    "deadline": "2026-02-20"
}

// 3. System creates RFQ with all items from template
```

### Example 5: Bulk Import RFQ Items

```php
// 1. Download CSV sample template
GET /buyer/rfqs/csv-sample/download

// 2. Fill CSV with products:
// product_name,quantity,notes
// "Stethoscope",10,"Digital preferred"
// "Blood Pressure Monitor",5,""

// 3. Upload CSV
POST /buyer/rfqs/import-csv
{
    "file": <csv_file>,
    "rfq_id": null  // Creates new RFQ
}

// 4. System matches products and creates RFQ items
```

### Example 6: Re-order from Past Order

```php
// 1. View order history
GET /buyer/orders/history

// 2. Re-order with one click
POST /buyer/orders/{order}/reorder
{
    "create_rfq": true  // Creates RFQ directly
}

// 3. Or add to cart for modifications
POST /buyer/orders/{order}/add-to-cart
```

---

## 📦 Dependencies & Requirements

### System Requirements

**Minimum:**
- PHP 8.2
- MySQL 5.7+ / PostgreSQL 10+ / SQLite 3.8+
- 512MB RAM
- 100MB disk space

**Recommended:**
- PHP 8.3+
- MySQL 8.0+ / PostgreSQL 14+
- 2GB+ RAM
- 1GB+ disk space

### PHP Extensions Required

```ini
php8.2-cli
php8.2-fpm
php8.2-mbstring
php8.2-xml
php8.2-curl
php8.2-zip
php8.2-gd
php8.2-mysql  # For MySQL
php8.2-pgsql  # For PostgreSQL
php8.2-sqlite3  # For SQLite
php8.2-bcmath
php8.2-intl
```

### Composer Dependencies

**Production Dependencies:**
- `laravel/framework: ^12.0`
- `spatie/laravel-permission: ^6.22`
- `spatie/laravel-medialibrary: ^11.17`
- `spatie/laravel-activitylog: ^4.10`
- `barryvdh/laravel-dompdf: ^3.1`
- `maatwebsite/excel: ^3.1`
- `intervention/image: ^3.11`

**Development Dependencies:**
- `laravel/breeze: ^2.3`
- `barryvdh/laravel-debugbar: ^3.16`
- `barryvdh/laravel-ide-helper: ^3.6`
- `laravel/pint: ^1.24`
- `phpunit/phpunit: ^11.5.3`

### NPM Dependencies

```json
{
  "alpinejs": "^3.4.2",
  "tailwindcss": "^3.1.0",
  "vite": "^7.0.7",
  "axios": "^1.11.0"
}
```

---

## 👨‍💻 Development Guide

### Code Standards

**PSR-12 Coding Standard:**
```bash
# Format code automatically
vendor/bin/pint
```

**Naming Conventions:**
- **Controllers:** `{Singular}Controller` (e.g., `ProductController`)
- **Models:** Singular (e.g., `Product`, `OrderItem`)
- **Tables:** Plural snake_case (e.g., `products`, `order_items`)
- **Routes:** Plural kebab-case (e.g., `/admin/suppliers`)
- **Views:** Snake_case (e.g., `create.blade.php`)

### Development Workflow

**1. Create New Feature:**
```bash
# Create model with migration
php artisan make:model Example -m

# Create controller
php artisan make:controller ExampleController --resource

# Create form request
php artisan make:request ExampleRequest

# Create policy
php artisan make:policy ExamplePolicy --model=Example
```

**2. Database Operations:**
```bash
# Create migration
php artisan make:migration create_examples_table

# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration (⚠️ deletes data)
php artisan migrate:fresh --seed
```

**3. Cache Management:**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Common Development Tasks

**Run Queue Worker:**
```bash
php artisan queue:listen
# Or in development:
composer dev  # Runs queue worker automatically
```

**View Logs:**
```bash
# Real-time logs
php artisan pail

# Or traditional
tail -f storage/logs/laravel.log
```

**Generate IDE Helpers:**
```bash
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

**Tinker (REPL):**
```bash
php artisan tinker
```

---

## 🗄 Database Architecture

### Entity Relationship Overview

```
Users (user_types, users, roles, permissions)
├── Suppliers (suppliers, product_supplier)
├── Buyers (buyers)
│
├── Products (products, product_categories, manufacturers)
│
└── Transaction Flow
    ├── RFQs (rfqs, rfq_items, rfq_templates)
    ├── Quotations (quotations, quotation_items)
    ├── Orders (orders, order_items)
    ├── Invoices (invoices)
    ├── Payments (payments)
    └── Deliveries (deliveries, delivery_tracking)
```

### Key Tables

**User Management:**
- `user_types` - User role definitions (Admin, Supplier, Buyer, Staff)
- `users` - User accounts with relationships
- `permissions`, `roles` - Spatie permission system
- `model_has_permissions`, `model_has_roles` - Permission assignments

**Business Entities:**
- `suppliers` - Supplier companies (approval required)
- `buyers` - Healthcare institutions (approval required)
- `products` - Medical equipment catalog
- `product_categories` - Hierarchical categorization
- `product_supplier` - Many-to-many relationships
- `manufacturers` - Product manufacturers

**Transaction Flow:**
- `rfqs` & `rfq_items` - Request for Quotations
- `rfq_templates` & `rfq_template_items` - Reusable RFQ templates
- `quotations` & `quotation_items` - Supplier quotes
- `orders` & `order_items` - Purchase orders
- `invoices` - Generated invoices
- `payments` - Payment records (auto-synced with orders)
- `deliveries` - Delivery tracking

**System Tables:**
- `activity_log` - Audit trail (Spatie)
- `media` - File storage (Spatie Media Library)
- `notifications` - System notifications
- `buyer_carts` & `buyer_cart_items` - Shopping carts
- `buyer_price_alerts` & `buyer_stock_alerts` - Alert system
- `product_price_history` - Price tracking

### Financial Data Precision

All financial columns use `decimal(15,2)` to prevent floating-point precision loss:
- `orders.total_amount`
- `order_items.unit_price`, `order_items.total_price`
- `quotation_items.unit_price`, `quotation_items.total_price`
- `invoices.total_amount`
- `payments.amount`

### Foreign Key Cascading Rules

**Protective (RESTRICT):**
- Financial records prevent accidental deletion
- Orders, quotations, invoices cannot be deleted if related records exist

**Graceful Degradation (NULL ON DELETE):**
- Optional relationships set to null when parent deleted
- Categories, manufacturers can be deleted (products remain)

---

## 🔐 Authentication & Authorization

### User Types

| Type | ID | Slug | Description |
|------|----|----- |-------------|
| Admin | 1 | `admin` | Full system access |
| Staff | 1 | `staff` | Limited permissions (customizable) |
| Supplier | 2 | `supplier` | Product & order management |
| Buyer | 3 | `buyer` | RFQ & purchasing |

### Registration Flow

**Buyer Registration:**
1. Visit `/register`
2. Select "مشتري (Buyer)"
3. Fill user info (name, email, phone, password)
4. Fill organization info (name, type, license, location)
5. Submit → Creates user + buyer profile
6. Status: pending (requires admin approval)
7. Redirect to `/waiting-approval`

**Supplier Registration:**
1. Visit `/register`
2. Select "مورد (Supplier)"
3. Fill user info (name, email, phone, password)
4. Fill company info (name, commercial register, tax number, location)
5. Submit → Creates user + supplier profile
6. Status: pending (requires admin approval)
7. Redirect to `/waiting-approval`

### Permission System

**Permission Structure:**
```
{action} {resource}

Actions: view, create, update, delete, approve, reject, etc.
Resources: users, suppliers, buyers, products, orders, etc.
```

**Examples:**
- `users.view` - View users list
- `products.create` - Create new products
- `orders.update` - Edit orders
- `quotations.accept` - Accept quotations

**Middleware Usage:**
```php
Route::get('/admin/products', [ProductController::class, 'index'])
    ->middleware('permission:products.view');
```

**Blade Directive:**
```blade
@can('products.create')
    <a href="{{ route('admin.products.create') }}">Create Product</a>
@endcan
```

---

## 🚀 Deployment

### Pre-Deployment Checklist

**Code Verification:**
- [ ] All tests passing
- [ ] No debug code (`dd()`, `dump()`, `var_dump()`)
- [ ] `.env.example` updated
- [ ] Database migrations tested
- [ ] Seeders working correctly

**Environment Setup:**
- [ ] Production `.env` configured
- [ ] Database credentials correct
- [ ] `APP_ENV=production`
- [ ] `APP_DEBUG=false`
- [ ] `APP_URL` set correctly
- [ ] Queue driver configured
- [ ] Mail settings configured
- [ ] Storage linked: `php artisan storage:link`

### Deployment Steps

**1. Server Setup:**
```bash
# Update system
sudo apt update && sudo apt upgrade

# Install PHP 8.2, MySQL, Nginx
sudo apt install php8.2 php8.2-fpm php8.2-mysql php8.2-mbstring \
    php8.2-xml php8.2-zip php8.2-gd php8.2-bcmath

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js
curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
sudo apt install -y nodejs
```

**2. Deploy Application:**
```bash
# Clone repository
git clone <repository-url> /var/www/medequip
cd /var/www/medequip

# Install dependencies
composer install --optimize-autoloader --no-dev
npm install
npm run build

# Set permissions
sudo chown -R www-data:www-data /var/www/medequip
sudo chmod -R 755 /var/www/medequip/storage
sudo chmod -R 755 /var/www/medequip/bootstrap/cache

# Environment
cp .env.example .env
nano .env  # Configure production settings
php artisan key:generate
```

**3. Database Migration:**
```bash
# ⚠️ PRODUCTION WARNING: Always backup first!
php artisan down  # Maintenance mode

# Backup database
mysqldump -u root -p medequip > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migrations
php artisan migrate --force

# Seed (if first deployment)
php artisan db:seed --class=UserTypeSeeder
php artisan db:seed --class=UnifiedRolePermissionSeeder

php artisan up  # Exit maintenance mode
```

**4. Optimization:**
```bash
# Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize autoloader
composer dump-autoload --optimize
```

**5. Queue & Scheduler:**
```bash
# Install Supervisor
sudo apt install supervisor

# Create supervisor config for queue worker
sudo nano /etc/supervisor/conf.d/medequip-worker.conf
```

**Supervisor Config:**
```ini
[program:medequip-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/medequip/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/medequip/storage/logs/worker.log
```

```bash
# Start supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start medequip-worker:*
```

**Cron for Scheduler:**
```bash
crontab -e
# Add:
* * * * * cd /var/www/medequip && php artisan schedule:run >> /dev/null 2>&1
```

---

## 🧪 Testing

### Running Tests

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test --filter=ExampleTest

# Run with coverage (requires Xdebug)
php artisan test --coverage
```

### Test Suites

**Available Tests:**
- `tests/Feature/RbacVerificationTest.php` - RBAC system tests
- `tests/Feature/RfqQuotationWorkflowTest.php` - RFQ/Quotation workflow
- `tests/Unit/QuotationStateMachineTest.php` - State machine tests
- `tests/Unit/RfqStateMachineTest.php` - RFQ state machine tests

### Manual Testing Checklist

**Authentication:**
- [ ] Register as buyer
- [ ] Register as supplier
- [ ] Login with different user types
- [ ] Password reset flow
- [ ] Email verification

**Buyer Workflow:**
- [ ] Browse products with filters
- [ ] Add products to cart
- [ ] Create RFQ from cart
- [ ] View quotations
- [ ] Compare quotations
- [ ] Accept quotation (creates order)
- [ ] View order history
- [ ] Re-order from past order

**Supplier Workflow:**
- [ ] Add product to catalog
- [ ] View available RFQs
- [ ] Submit quotation
- [ ] View accepted orders
- [ ] Update order status
- [ ] Create delivery record

**Admin Workflow:**
- [ ] Approve/reject registrations
- [ ] Manage users and permissions
- [ ] View activity logs
- [ ] Generate reports

---

## 🤝 Contributing

### Development Workflow

1. **Fork & Clone** the repository
2. **Create Feature Branch:** `git checkout -b feature/amazing-feature`
3. **Code & Test** your changes
4. **Commit:** `git commit -m 'Add amazing feature'`
5. **Push:** `git push origin feature/amazing-feature`
6. **Create Pull Request**

### Code Review Checklist

- [ ] Follows PSR-12 coding standards
- [ ] Proper PHPDoc blocks
- [ ] Tests included and passing
- [ ] No debug code (`dd()`, `dump()`, etc.)
- [ ] Migrations tested
- [ ] Documentation updated
- [ ] Authorization checks implemented
- [ ] Error handling comprehensive

### Commit Message Format

```
type(scope): subject

body (optional)

footer (optional)
```

**Types:**
- `feat`: New feature
- `fix`: Bug fix
- `docs`: Documentation
- `style`: Code style changes
- `refactor`: Code refactoring
- `test`: Test additions/changes
- `chore`: Maintenance tasks

---

## 📚 Additional Resources

### Documentation Files

All detailed documentation is available in the project root:

- **CODEBASE_INDEX.md** - Complete codebase structure and reference
- **QUICK_START.md** - Fast setup guide
- **RBAC_SYSTEM_DESIGN.md** - RBAC architecture details
- **BUYER_JOURNEY_MASTER_SUMMARY.md** - Buyer journey implementation
- **RFQ_QUOTATION_QUICK_START_GUIDE.md** - RFQ/Quotation workflow
- **DEPLOYMENT_CHECKLIST.md** - Detailed deployment steps
- **LANDING_PAGE_DOCUMENTATION.md** - Landing page design

### External Documentation

- **Laravel:** https://laravel.com/docs/12.x
- **Tailwind CSS:** https://tailwindcss.com/docs
- **Alpine.js:** https://alpinejs.dev/
- **Spatie Packages:** https://spatie.be/open-source

### Community

- **Laravel:** https://laravel.io/
- **Stack Overflow:** https://stackoverflow.com/questions/tagged/laravel

---

## 📊 Project Status

### ✅ Completed Features

- [x] Laravel 12 framework setup
- [x] Tailwind CSS migration
- [x] User authentication with approval workflow
- [x] Multi-role system (Admin, Staff, Supplier, Buyer)
- [x] Spatie permission integration
- [x] Product catalog with hierarchical categories
- [x] RFQ/Quotation system with state machines
- [x] Order management
- [x] Invoice generation
- [x] Payment tracking with auto-sync
- [x] Delivery management
- [x] Activity logging & audit trails
- [x] Responsive landing page
- [x] Admin panel
- [x] Buyer journey improvements (70% complete)
- [x] Shopping cart with persistence
- [x] RFQ templates and bulk import
- [x] Quotation scoring system
- [x] Abandoned cart recovery

### 🚧 Roadmap (Optional Enhancements)

**Phase 1: User Experience**
- [ ] Email verification flow
- [ ] Social login (Google, Facebook)
- [ ] Real-time notifications (WebSockets)
- [ ] Advanced search (Elasticsearch)
- [ ] Product recommendations (AI)

**Phase 2: Business Features**
- [ ] Discount & coupon system
- [ ] Subscription plans for suppliers
- [ ] Featured products/suppliers
- [ ] Negotiation tools

**Phase 3: Analytics & Reporting**
- [ ] Advanced reporting dashboards
- [ ] Sales analytics
- [ ] Supplier performance metrics
- [ ] Buyer insights

**Phase 4: Integration**
- [ ] RESTful API development
- [ ] Mobile app (React Native/Flutter)
- [ ] Payment gateway integration
- [ ] Shipping provider integration

**Phase 5: Localization**
- [ ] Full Arabic translation (i18n)
- [ ] Multi-language support
- [ ] Multi-currency support

---

## 📞 Support & Contact

For questions, issues, or contributions:

- **Email:** support@medequip.ly
- **GitHub:** Create an issue in the repository
- **Documentation:** See `/docs` directory or project root markdown files

---

## 📄 License

This project is licensed under the **MIT License**.

---

## 🎉 Acknowledgments

Special thanks to:

- **Laravel Team** - For the amazing framework
- **Spatie** - For their excellent packages (Permission, Media Library, Activity Log)
- **Tailwind Labs** - For Tailwind CSS
- **All Contributors** - Who made this project possible

---

**Built with ❤️ for the healthcare industry in Libya and the Arab world.**

**Version:** 1.0.0  
**Last Updated:** January 24, 2026  
**Status:** ✅ Production Ready  
**Ready to deploy!** 🚀
